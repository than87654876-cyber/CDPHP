<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AdminOrderController extends Controller
{
    // Danh sách đơn hàng (hỗ trợ lọc trạng thái)
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Order::with(['user', 'orderItems.dish'])
            ->where('order_type', '!=', 'subscription')
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('order_status', $status);
        }

        if ($search) {
            $cleanCode = str_ireplace(['#fdl-', 'fdl-', '#'], '', $search);
            $query->where(function ($q) use ($search, $cleanCode) {
                $q->where('id', $cleanCode)
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('fullname', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.orders', compact('orders', 'status', 'search'));
    }

    // Bảng chuẩn bị món ăn hôm nay của nhà bếp
    public function kitchenReport()
    {
        $todayStr = Carbon::today()->format('Y-m-d');

        // 1. Danh sách các đơn hàng chi tiết đang chờ nấu (Vé bếp / Order Tickets)
        $activeOrders = Order::with(['user', 'orderItems.dish.category'])
            ->whereIn('order_status', ['pending', 'confirmed', 'preparing'])
            ->where('order_type', '!=', 'subscription')
            ->orderBy('created_at', 'asc') // Đơn đặt trước làm trước (FIFO)
            ->get();

        // 2. Gom nhóm theo món ăn trực tiếp từ $activeOrders (Tránh query lại bảng order_items, users, dishes, categories lần 2)
        $kitchenOrderItems = $activeOrders->flatMap(function ($order) {
            foreach ($order->orderItems as $item) {
                $item->setRelation('order', $order);
            }
            return $order->orderItems;
        });

        $singleDishes = $kitchenOrderItems->groupBy('dish_id')->map(function ($items) {
            $first = $items->first();
            $dish = $first->dish;

            // Danh sách các mã vận đơn có món này
            $orders = $items->map(function ($item) {
                $order = $item->order;
                return (object) [
                    'order_id' => $item->order_id,
                    'tracking_code' => 'FDL-' . $item->order_id,
                    'quantity' => $item->quantity,
                    'order_status' => $order->order_status ?? 'pending',
                    'payment_status' => $order->payment_status ?? 'pending',
                    'customer_name' => $order->user->fullname ?? $order->user->name ?? 'Khách',
                    'health_notes' => $order->health_notes ?? '',
                    'created_at' => $order->created_at,
                ];
            })->sortByDesc('order_id')->values();

            return (object) [
                'dish_id' => $first->dish_id,
                'dish' => $dish,
                'total_qty' => $items->sum('quantity'),
                'orders' => $orders,
            ];
        })->sortByDesc('total_qty')->values();

        // 3. Danh sách các đơn vừa nấu xong gần đây (delivering hoặc completed) để hỗ trợ tra cứu xử lý sự cố
        $recentCompletedOrders = Order::with(['user', 'orderItems.dish.category'])
            ->whereIn('order_status', ['delivering', 'completed'])
            ->where('order_type', '!=', 'subscription')
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        // Format dữ liệu chi tiết cho modal tra cứu và giải quyết khiếu nại của khách
        $initialCompletedOrders = $recentCompletedOrders->map(function ($order) {
            $customerName = $order->user->fullname ?? $order->user->name ?? 'Khách đặt món';
            $phone = $order->resolved_phone;
            $address = $order->resolved_address;

            return [
                'id' => $order->id,
                'customer' => $customerName,
                'phone' => $phone,
                'address' => $address,
                'healthNotes' => $order->health_notes ?? '',
                'totalItems' => $order->orderItems->count(),
                'finalAmount' => number_format($order->final_amount, 0, ',', '.') . 'đ',
                'orderStatus' => $order->order_status,
                'paymentStatus' => $order->payment_status,
                'time' => $order->updated_at ? $order->updated_at->format('H:i') : ($order->created_at ? $order->created_at->format('H:i') : ''),
                'dishes' => $order->orderItems->map(function ($oi) {
                    return [
                        'name' => $oi->dish->dish_name ?? 'Món ăn',
                        'qty' => $oi->quantity,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        return view('admin.kitchen_report', compact('singleDishes', 'activeOrders', 'recentCompletedOrders', 'initialCompletedOrders', 'todayStr'));
    }

    // Xem chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with(['user', 'orderItems.dish'])->findOrFail($id);

        return view('admin.orders_detail', compact('order'));
    }

    // Form chỉnh sửa/cập nhật trạng thái đơn hàng
    public function edit($id)
    {
        $order = Order::findOrFail($id);

        return view('admin.orders_edit', compact('order'));
    }

    // Cập nhật trạng thái đơn hàng
    public function update(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|string|in:pending,confirmed,preparing,delivering,completed,cancelled',
            'payment_status' => 'required|string|in:pending,paid,failed',
            'admin_notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        $order->order_status = $request->order_status;
        $order->payment_status = $request->payment_status;
        if ($order->payment_status === 'paid' && $order->order_status === 'pending') {
            $order->order_status = 'confirmed';
        }
        if ($request->admin_notes) {
            $order->admin_notes = $request->admin_notes;
            $order->health_notes = ($order->health_notes ? $order->health_notes."\n" : '').'[Điều phối: '.$request->admin_notes.']';
        }
        $order->save();

        try {
            event(new \App\Events\OrderUpdated($order, 'status_updated'));
        } catch (\Exception $broadcastException) {
            \Illuminate\Support\Facades\Log::warning('Broadcasting failed: ' . $broadcastException->getMessage());
        }

        try {
            $statusLabels = [
                'pending' => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận',
                'preparing' => 'Đang chuẩn bị món',
                'delivering' => 'Đang giao hàng',
                'completed' => 'Đã giao thành công',
                'cancelled' => 'Đã hủy'
            ];
            $statusLabel = $statusLabels[$order->order_status] ?? $order->order_status;
            \App\Services\NotificationService::sendToUser(
                $order->user_id,
                "🛵 Cập nhật đơn hàng #{$order->order_code}",
                "Đơn hàng của bạn hiện đang: {$statusLabel}. Nhấn để theo dõi.",
                "/tracuu?order_code=" . $order->order_code,
                'order',
                '/icons/icon-192x192.png',
                ['order_id' => $order->id, 'order_code' => $order->order_code, 'status' => $order->order_status]
            );
        } catch (\Throwable $notiErr) {
            \Illuminate\Support\Facades\Log::warning('Order status notification error: ' . $notiErr->getMessage());
        }

        try {
            $telegram = app(\App\Services\TelegramService::class);
            $statusLabels = [
                'pending' => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận',
                'preparing' => 'Đang chuẩn bị',
                'delivering' => 'Đang giao hàng',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã hủy'
            ];
            $statusLabel = $statusLabels[$order->order_status] ?? $order->order_status;
            
            $telegram->sendMessage("🔄 <b>Cập nhật đơn hàng #FDL-{$order->id}:</b>\nTrạng thái mới: <b>{$statusLabel}</b>\nThanh toán: " . ($order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán'));
        } catch (\Exception $telError) {
            \Illuminate\Support\Facades\Log::warning('Telegram sending warning: ' . $telError->getMessage());
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái đơn hàng FDL-'.$id.' thành công!',
                'order' => $order,
            ]);
        }

        return redirect()->route('quanly_donhang')->with('success', 'Cập nhật trạng thái đơn hàng FDL-'.$id.' thành công!');
    }

    // Xóa đơn hàng
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        $order->orderItems()->delete();
        $order->delete();

        return redirect()->route('quanly_donhang')->with('success', 'Đã xóa đơn hàng FDL-'.$id.' thành công.');
    }

    // Xuất báo cáo đơn hàng (Hỗ trợ Excel .xlsx và CSV UTF-8 BOM)
    public function exportOrdersCsv(Request $request)
    {
        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();

        $formatDate = function ($value, string $format = 'd/m/Y H:i') {
            if (empty($value)) return 'N/A';
            if ($value instanceof \DateTimeInterface) return $value->format($format);
            try {
                return \Carbon\Carbon::parse($value)->format($format);
            } catch (\Throwable) {
                return (string) $value;
            }
        };

        if ($request->query('format') === 'csv') {
            $headers = ['Mã đơn hàng', 'Khách hàng', 'Email', 'Số điện thoại', 'Loại đơn', 'Tổng tiền', 'Hình thức thanh toán', 'Trạng thái thanh toán', 'Trạng thái đơn hàng', 'Ngày đặt'];
            return $this->exportCsvStream('bao-cao-don-hang.csv', $headers, $orders, function ($order) use ($formatDate) {
                $paymentMethodText = match ($order->payment_method) {
                    'bank_transfer' => 'Chuyển khoản',
                    'momo' => 'Ví MoMo',
                    default => 'Tiền mặt (COD)',
                };
                $orderTypeText = $order->order_type === 'single' ? 'Món lẻ' : 'Gói dài kỳ';

                return [
                    'FDL-' . $order->id,
                    $order->user ? $order->user->fullname : 'Khách vãng lai',
                    $order->user ? $order->user->email : 'N/A',
                    $order->user ? $order->user->phone : 'N/A',
                    $orderTypeText,
                    $order->final_amount,
                    $paymentMethodText,
                    $order->payment_status,
                    $order->order_status,
                    $formatDate($order->created_at),
                ];
            });
        }

        // Mặc định xuất file Excel chuyên nghiệp (.xlsx)
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách đơn hàng');

        $now = now();
        $title = 'BÁO CÁO DANH SÁCH ĐƠN HÀNG TOÀN HỆ THỐNG';
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('1E293B');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Thời gian xuất: ' . $now->format('d/m/Y H:i') . ' | Tổng số đơn: ' . $orders->count());
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->getColor()->setRGB('475569');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = [
            'STT',
            'Mã đơn hàng',
            'Khách hàng',
            'Email',
            'Số điện thoại',
            'Loại đơn',
            'Tổng tiền (VNĐ)',
            'Hình thức thanh toán',
            'Trạng thái thanh toán',
            'Ngày đặt',
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($index + 1) . '4', $header);
        }

        $sheet->getStyle('A4:J4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0284C7']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $rowNumber = 5;
        foreach ($orders->values() as $index => $order) {
            $paymentMethodText = match ($order->payment_method) {
                'bank_transfer' => 'Chuyển khoản',
                'momo' => 'Ví MoMo',
                default => 'Tiền mặt (COD)',
            };
            $orderTypeText = $order->order_type === 'single' ? 'Món lẻ' : 'Gói dài kỳ';

            $values = [
                $index + 1,
                'FDL-' . $order->id,
                $order->user ? $order->user->fullname : 'Khách vãng lai',
                $order->user ? $order->user->email : 'N/A',
                $order->user ? $order->user->phone : 'N/A',
                $orderTypeText,
                (float) $order->final_amount,
                $paymentMethodText,
                $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán',
                $formatDate($order->created_at),
            ];

            foreach ($values as $colIndex => $value) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex + 1) . $rowNumber, $value);
            }

            $rowNumber++;
        }

        $lastRow = max(5, $rowNumber - 1);
        $sheet->getStyle('A5:J' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle('A5:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B5:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F5:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H5:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I5:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J5:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G5:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G5:G' . $lastRow)->getNumberFormat()->setFormatCode('#,##0 "₫"');

        foreach (range('A', 'J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = 'BaoCao_DonHang_' . $now->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // =========================================================================
    // QUẢN LÝ ĐÁNH GIÁ ĐƠN HÀNG (REVIEWS)
    // =========================================================================

    // Danh sách đánh giá của khách hàng (hỗ trợ phân trang, lọc loại đánh giá và tìm kiếm)
    public function reviewsList(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type', 'all');

        $query = \App\Models\Review::with(['user', 'order', 'dish'])
            ->orderBy('created_at', 'desc');

        if ($type === 'order') {
            $query->whereNotNull('order_id');
        } elseif ($type === 'general') {
            $query->whereNull('order_id');
        }

        if ($search) {
            $cleanCode = str_ireplace(['#fdl-', 'fdl-', '#'], '', $search);
            $query->where(function ($q) use ($search, $cleanCode) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhere('order_id', $cleanCode)
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('fullname', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('dish', function ($dq) use ($search) {
                      $dq->where('dish_name', 'like', "%{$search}%");
                  });
            });
        }

        // Toàn bộ thống kê tổng quan (tính toán bằng 1 truy vấn SQL tổng hợp thay vì nạp toàn bộ Model vào RAM)
        $reviewStats = \Illuminate\Support\Facades\DB::table('reviews')->selectRaw("
            COUNT(id) as total_count,
            SUM(CASE WHEN order_id IS NOT NULL THEN 1 ELSE 0 END) as order_count,
            SUM(CASE WHEN order_id IS NULL THEN 1 ELSE 0 END) as general_count,
            AVG(rating) as avg_rating,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star_count,
            SUM(CASE WHEN comment IS NOT NULL AND comment != '' THEN 1 ELSE 0 END) as comment_count
        ")->first();

        $totalReviewsCount = (int) ($reviewStats->total_count ?? 0);
        $orderReviewsCount = (int) ($reviewStats->order_count ?? 0);
        $generalReviewsCount = (int) ($reviewStats->general_count ?? 0);
        $avgRating = $totalReviewsCount > 0 ? round((float) ($reviewStats->avg_rating ?? 5.0), 1) : 5.0;
        $fiveStarCount = (int) ($reviewStats->five_star_count ?? 0);
        $fiveStarPercent = $totalReviewsCount > 0 ? round(($fiveStarCount / $totalReviewsCount) * 100) : 100;
        $withCommentCount = (int) ($reviewStats->comment_count ?? 0);

        $reviews = $query->paginate(10)->withQueryString();

        return view('admin.reviews', compact(
            'reviews',
            'totalReviewsCount',
            'orderReviewsCount',
            'generalReviewsCount',
            'avgRating',
            'fiveStarCount',
            'fiveStarPercent',
            'withCommentCount',
            'search',
            'type'
        ));
    }

    // Xóa đánh giá
    public function destroyReview($id)
    {
        $review = \App\Models\Review::findOrFail($id);
        $review->delete();

        return redirect()->route('quanly_reviews')->with('success', 'Đã xóa đánh giá thành công.');
    }
}
