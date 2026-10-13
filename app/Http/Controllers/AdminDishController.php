<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Dish;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class AdminDishController extends Controller
{
    // List all dishes
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $categories = Category::all();

        if (!$categoryId && $categories->isNotEmpty()) {
            $categoryId = $categories->first()->id;
        }

        $query = Dish::with('category');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('dish_name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $dishes = $query->get();

        return view('admin.single_dishes', compact('dishes', 'categories', 'search', 'categoryId'));
    }

    // View dish details
    public function show($id)
    {
        $dish = Dish::with('category')->findOrFail($id);

        return view('admin.single_dishes_detail', compact('dish'));
    }

    // Show create form
    public function create(Request $request)
    {
        $categories = Category::all();
        $selectedCategoryId = $request->query('category_id') ?? 1;

        return view('admin.single_dishes_add', compact('categories', 'selectedCategoryId'));
    }

    // Save new dish
    public function store(Request $request)
    {
        $request->validate([
            'dish_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $data = $request->only('dish_name', 'category_id', 'price', 'description');
        $data['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            $cloudinary = app(\App\Services\CloudinaryService::class);
            $imageUrl = $cloudinary->upload($request->file('image'));
            if ($imageUrl) {
                $data['image_url'] = $imageUrl;
            }
        }

        $dish = Dish::create($data);
        \App\Services\DishSyncService::recordMutation('created', $dish);

        return redirect()->route('quanly_monandon')->with('success', 'Đã thêm món ăn mới thành công!');
    }

    // Show edit form
    public function edit($id)
    {
        $dish = Dish::findOrFail($id);
        $categories = Category::all();

        return view('admin.single_dishes_edit', compact('dish', 'categories'));
    }

    // Update dish
    public function update(Request $request, $id)
    {
        $request->validate([
            'dish_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $dish = Dish::findOrFail($id);
        $data = $request->only('dish_name', 'category_id', 'price', 'description');
        $data['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            // Delete old local image if exists
            if ($dish->image_url && !str_starts_with($dish->image_url, 'http') && file_exists(public_path($dish->image_url))) {
                @unlink(public_path($dish->image_url));
            }

            $cloudinary = app(\App\Services\CloudinaryService::class);
            $imageUrl = $cloudinary->upload($request->file('image'));
            if ($imageUrl) {
                $data['image_url'] = $imageUrl;
            }
        }

        $dish->update($data);
        \App\Services\DishSyncService::recordMutation('updated', $dish);

        return redirect()->route('quanly_monandon')->with('success', 'Đã cập nhật món ăn thành công!');
    }

    // Delete dish
    public function destroy($id)
    {
        $dish = Dish::findOrFail($id);
        $dishId = $dish->id;
        $dishCopy = clone $dish;

        // Delete image file if exists
        if ($dish->image_url && file_exists(public_path($dish->image_url))) {
            @unlink(public_path($dish->image_url));
        }

        $dish->delete();
        \App\Services\DishSyncService::recordMutation('deleted', $dishCopy, $dishId);

        return redirect()->route('quanly_monandon')->with('success', 'Đã xóa món ăn thành công!');
    }

    // Xuất báo cáo món ăn bán chạy (Hỗ trợ Excel .xlsx và CSV UTF-8 BOM)
    public function exportDishesCsv(Request $request)
    {
        $dishes = Dish::with('category')
            ->withSum(['orderItems as total_sold' => function($q) {
                $q->whereHas('order', function($sub) {
                    $sub->whereIn('payment_status', ['paid', 'completed'])
                        ->whereNotIn('order_status', ['cancelled', 'failed']);
                });
            }], 'quantity')
            ->get()
            ->sortByDesc('total_sold');

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
            $headers = ['Mã món', 'Danh mục', 'Tên món ăn', 'Đơn giá (VNĐ)', 'Trạng thái', 'Số lượng bán lẻ đã bán', 'Ngày tạo'];
            return $this->exportCsvStream('mon-an-ban-chay.csv', $headers, $dishes, function ($dish) use ($formatDate) {
                $statusText = $dish->is_available ? 'Còn hàng' : 'Hết hàng';
                return [
                    'MON-' . sprintf('%03d', $dish->id),
                    $dish->category ? $dish->category->category_name : 'Không phân loại',
                    $dish->dish_name,
                    $dish->price,
                    $statusText,
                    $dish->total_sold ?? 0,
                    $formatDate($dish->created_at),
                ];
            });
        }

        // Mặc định xuất file Excel chuyên nghiệp (.xlsx)
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Món ăn bán chạy');

        $now = now();
        $title = 'BÁO CÁO THỐNG KÊ MÓN ĂN BÁN CHẠY';
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('991B1B');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Thời gian xuất: ' . $now->format('d/m/Y H:i') . ' | Tổng số món: ' . $dishes->count());
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->getColor()->setRGB('475569');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = [
            'STT',
            'Mã món',
            'Danh mục',
            'Tên món ăn',
            'Đơn giá (VNĐ)',
            'Trạng thái kinh doanh',
            'Số lượng đã bán',
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($index + 1) . '4', $header);
        }

        $sheet->getStyle('A4:G4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E11D48']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $rowNumber = 5;
        foreach ($dishes->values() as $index => $dish) {
            $statusText = $dish->is_available ? 'Còn hàng' : 'Hết hàng';
            $catName = $dish->category ? $dish->category->category_name : 'Không phân loại';

            $values = [
                $index + 1,
                'MON-' . sprintf('%03d', $dish->id),
                $catName,
                $dish->dish_name,
                (float) $dish->price,
                $statusText,
                (int) ($dish->total_sold ?? 0),
            ];

            foreach ($values as $colIndex => $value) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex + 1) . $rowNumber, $value);
            }

            $rowNumber++;
        }

        $lastRow = max(5, $rowNumber - 1);
        $sheet->getStyle('A5:G' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle('A5:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B5:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F5:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G5:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E5:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E5:E' . $lastRow)->getNumberFormat()->setFormatCode('#,##0 "₫"');
        $sheet->getStyle('G5:G' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = 'BaoCao_MonAn_BanChay_' . $now->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
