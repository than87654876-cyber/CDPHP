<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\InventoryItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminProcurementController extends Controller
{
    // ==========================================
    // 1. QUẢN LÝ KHO & TỒN KHO (INVENTORY)
    // ==========================================

    public function inventoryIndex(Request $request)
    {
        $query = InventoryItem::with('supplier', 'dish');

        // Tìm kiếm theo từ khóa (Tên mặt hàng hoặc mã)
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('item_code', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Lọc theo nhà cung cấp
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Lọc theo trạng thái tồn kho
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('current_stock', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('current_stock', '>', 0)
                      ->whereColumn('current_stock', '<=', 'min_stock');
            } elseif ($request->stock_status === 'in_stock') {
                $query->whereColumn('current_stock', '>', 'min_stock');
            }
        }

        // Thống kê tổng quan kho
        $totalItems = InventoryItem::count();
        $outOfStockCount = InventoryItem::where('current_stock', '<=', 0)->count();
        $lowStockCount = InventoryItem::where('current_stock', '>', 0)
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->count();
        $totalInventoryValue = InventoryItem::sum(DB::raw('current_stock * cost_price'));

        $items = $query->orderByRaw("
            CASE 
                WHEN current_stock <= 0 THEN 1 
                WHEN current_stock <= min_stock THEN 2 
                ELSE 3 
            END ASC
        ")->latest()->paginate(15)->withQueryString();

        $categories = InventoryItem::whereNotNull('category')->distinct()->pluck('category');
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();

        return view('admin.procurement.inventory_index', compact(
            'items',
            'categories',
            'suppliers',
            'totalItems',
            'outOfStockCount',
            'lowStockCount',
            'totalInventoryValue'
        ));
    }

    public function inventoryCreate()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $categories = InventoryItem::whereNotNull('category')->distinct()->pluck('category');
        return view('admin.procurement.inventory_create', compact('suppliers', 'categories'));
    }

    public function inventoryStore(Request $request)
    {
        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:inventory_items,item_code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        InventoryItem::create($validated);

        return redirect()->route('quanly_kho')->with('success', "Đã thêm mặt hàng/nguyên liệu '{$validated['name']}' vào kho thành công!");
    }

    public function inventoryEdit($id)
    {
        $item = InventoryItem::findOrFail($id);
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $categories = InventoryItem::whereNotNull('category')->distinct()->pluck('category');
        return view('admin.procurement.inventory_edit', compact('item', 'suppliers', 'categories'));
    }

    public function inventoryUpdate(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);

        $validated = $request->validate([
            'item_code' => 'required|string|max:50|unique:inventory_items,item_code,' . $item->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('quanly_kho')->with('success', "Đã cập nhật thông tin mặt hàng '{$item->name}'!");
    }

    public function inventoryAdjustStock(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);

        $validated = $request->validate([
            'new_stock' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $oldStock = $item->current_stock;
        $item->update([
            'current_stock' => $validated['new_stock'],
            'notes' => ($item->notes ? $item->notes . "\n" : '') . "[" . Carbon::now()->format('d/m/Y H:i') . "] Điều chỉnh từ {$oldStock} -> {$validated['new_stock']} ({$validated['reason']})",
        ]);

        return redirect()->back()->with('success', "Đã cập nhật số lượng tồn kho của {$item->name} thành {$validated['new_stock']} {$item->unit}!");
    }

    public function inventoryDestroy($id)
    {
        $item = InventoryItem::findOrFail($id);

        if ($item->receiptItems()->exists()) {
            return redirect()->back()->with('error', "Không thể xóa mặt hàng này vì đã có dữ liệu trong các phiếu nhập kho!");
        }

        $item->delete();
        return redirect()->route('quanly_kho')->with('success', "Đã xóa mặt hàng khỏi kho!");
    }

    // ==========================================
    // 2. QUẢN LÝ NHÀ CUNG CẤP (SUPPLIERS)
    // ==========================================

    public function suppliersIndex(Request $request)
    {
        $query = Supplier::withCount('inventoryItems', 'goodsReceipts');

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('supplier_code', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('contact_person', 'like', "%{$keyword}%")
                  ->orWhere('supply_category', 'like', "%{$keyword}%");
            });
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        return view('admin.procurement.suppliers_index', compact('suppliers'));
    }

    public function supplierCreate()
    {
        return view('admin.procurement.supplier_create');
    }

    public function supplierStore(Request $request)
    {
        $validated = $request->validate([
            'supplier_code' => 'required|string|max:50|unique:suppliers,supplier_code',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'supply_category' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'active';

        Supplier::create($validated);

        return redirect()->route('quanly_nhacungcap')->with('success', "Đã thêm nhà cung cấp '{$validated['name']}' thành công!");
    }

    public function supplierEdit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.procurement.supplier_edit', compact('supplier'));
    }

    public function supplierUpdate(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'supplier_code' => 'required|string|max:50|unique:suppliers,supplier_code,' . $supplier->id,
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'supply_category' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('quanly_nhacungcap')->with('success', "Đã cập nhật thông tin nhà cung cấp '{$supplier->name}'!");
    }

    public function supplierDestroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        if ($supplier->goodsReceipts()->exists()) {
            return redirect()->back()->with('error', "Không thể xóa nhà cung cấp này vì đã có lịch sử phiếu nhập hàng! Bạn có thể chuyển trạng thái sang Tạm ngưng (Inactive).");
        }

        $supplier->delete();
        return redirect()->route('quanly_nhacungcap')->with('success', "Đã xóa nhà cung cấp thành công!");
    }

    // ==========================================
    // 3. QUY TRÌNH NHẬP HÀNG (GOODS RECEIPTS)
    // ==========================================

    public function receiptsIndex(Request $request)
    {
        $query = GoodsReceipt::with(['supplier', 'creator', 'items.item']);

        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where('receipt_code', 'like', "%{$keyword}%");
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('receipt_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('receipt_date', '<=', $request->to_date);
        }

        $receipts = $query->latest('receipt_date')->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        $totalReceiptsCost = GoodsReceipt::sum('total_amount');
        $monthReceiptsCost = GoodsReceipt::whereMonth('receipt_date', Carbon::now()->month)
                                         ->whereYear('receipt_date', Carbon::now()->year)
                                         ->sum('total_amount');

        return view('admin.procurement.receipts_index', compact('receipts', 'suppliers', 'totalReceiptsCost', 'monthReceiptsCost'));
    }

    public function receiptCreate()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $inventoryItems = InventoryItem::where('status', 'active')->orderBy('name')->get();

        // Tạo sẵn mã phiếu gợi ý theo ngày: PNK-YYYYMMDD-XXX
        $todayPrefix = 'PNK-' . Carbon::now()->format('Ymd');
        $countToday = GoodsReceipt::where('receipt_code', 'like', "{$todayPrefix}%")->count() + 1;
        $suggestedCode = $todayPrefix . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        return view('admin.procurement.receipt_create', compact('suppliers', 'inventoryItems', 'suggestedCode'));
    }

    public function receiptStore(Request $request)
    {
        $validated = $request->validate([
            'receipt_code' => 'required|string|max:50|unique:goods_receipts,receipt_code',
            'supplier_id' => 'required|exists:suppliers,id',
            'receipt_date' => 'required|date',
            'payment_status' => 'required|in:paid,pending,debt',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.notes' => 'nullable|string|max:255',
        ], [
            'items.required' => 'Vui lòng thêm ít nhất một mặt hàng vào phiếu nhập!',
            'items.min' => 'Vui lòng thêm ít nhất một mặt hàng vào phiếu nhập!',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $totalAmount = 0;

            // 1. Tính tổng tiền phiếu nhập
            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['unit_price'];
                $totalAmount += $subtotal;
            }

            // 2. Tạo Phiếu Nhập Kho
            $receipt = GoodsReceipt::create([
                'receipt_code' => $validated['receipt_code'],
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id() ?? 1,
                'receipt_date' => $validated['receipt_date'],
                'total_amount' => $totalAmount,
                'payment_status' => $validated['payment_status'],
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
            ]);

            // 3. Tạo từng chi tiết mặt hàng và TỰ ĐỘNG CỘNG DỒN TỒN KHO
            foreach ($validated['items'] as $itemData) {
                $qty = (float) $itemData['quantity'];
                $price = (float) $itemData['unit_price'];
                $subtotal = $qty * $price;

                GoodsReceiptItem::create([
                    'goods_receipt_id' => $receipt->id,
                    'inventory_item_id' => $itemData['inventory_item_id'],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // Cộng dồn tồn kho thực tế và cập nhật giá vốn gần nhất
                $inventoryItem = InventoryItem::find($itemData['inventory_item_id']);
                if ($inventoryItem) {
                    $inventoryItem->increment('current_stock', $qty);
                    $inventoryItem->update(['cost_price' => $price]);
                }
            }

            return redirect()->route('nhaphang_xem', $receipt->id)
                ->with('success', "Đã tạo phiếu nhập kho '{$receipt->receipt_code}' thành công! Tồn kho đã được tự động cộng dồn.");
        });
    }

    public function receiptShow($id)
    {
        $receipt = GoodsReceipt::with(['supplier', 'creator', 'items.item'])->findOrFail($id);
        return view('admin.procurement.receipt_show', compact('receipt'));
    }

    public function receiptPrint($id)
    {
        $receipt = GoodsReceipt::with(['supplier', 'creator', 'items.item'])->findOrFail($id);
        return view('admin.procurement.receipt_print', compact('receipt'));
    }
}
