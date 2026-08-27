<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminCustomerController extends Controller
{
    // Danh sách khách hàng
    public function customersList(Request $request)
    {
        $filter = $request->input('filter');
        $query = User::where('role', 'customer')->orderBy('created_at', 'desc');
        $query = $this->applyCustomerFilter($query, $filter);
        $query = $this->applyAdvancedCustomerFilters($query, $request);

        $customers = $query->get();

        return view('admin.customers', compact('customers', 'filter'));
    }

    private function applyCustomerFilter(Builder $query, ?string $filter): Builder
    {
        if ($filter === 'first_order') {
            $query->whereHas('orders');
        } elseif ($filter === 'active_package') {
            $query->whereHas('subscriptions', function ($q) {
                $q->where('status', 'active');
            });
        } elseif ($filter === 'refunded') {
            $query->whereHas('orders', function ($q) {
                $q->where('payment_status', 'refunded')
                  ->orWhere('health_notes', 'like', '%hoàn tiền%');
            });
        } elseif ($filter === 'inactive_3m') {
            $threeMonthsAgo = now()->subMonths(3);
            $query->where('created_at', '<', $threeMonthsAgo)
                  ->whereDoesntHave('orders', function ($q) use ($threeMonthsAgo) {
                      $q->where('created_at', '>=', $threeMonthsAgo);
                  });
        }

        return $query;
    }

    private function applyAdvancedCustomerFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->input('created_from'));
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->input('created_to'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status') === 'active' ? 1 : 0);
        }
        if ($request->filled('membership')) {
            $query->where('membership', $request->input('membership'));
        }
        if ($request->filled('gender')) {
            if (in_array($request->input('gender'), ['male', 'female', 'other'], true)) {
                $query->where('gender', $request->input('gender'));
            }
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    // Chi tiết khách hàng
    public function customerShow($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);

        return view('admin.customers_detail', compact('customer'));
    }

    // Form chỉnh sửa khách hàng
    public function customerEdit($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);

        return view('admin.customers_edit', compact('customer'));
    }

    // Cập nhật thông tin khách hàng
    public function customerUpdate(Request $request, $id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,'.$customer->id,
            'points' => 'required|integer|min:0',
            'membership' => 'required|string|in:bronze,silver,gold,diamond',
            'notes' => 'nullable|string',
        ]);

        $customer->update($request->only('fullname', 'phone', 'email', 'points', 'membership', 'notes'));

        return redirect()->route('quanly_khachhang')->with('success', 'Cập nhật thông tin khách hàng thành công!');
    }

    // Xóa tài khoản khách hàng
    public function customerDestroy($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);

        // Kiểm tra xem khách hàng có đơn hàng hoặc đăng ký gói không
        if ($customer->orders()->count() > 0 || $customer->subscriptions()->count() > 0) {
            return redirect()->route('quanly_khachhang')->with('error', 'Không thể xóa khách hàng này vì đã có dữ liệu đơn hàng hoặc gói dịch vụ liên kết.');
        }

        $customer->delete();

        return redirect()->route('quanly_khachhang')->with('success', 'Đã xóa tài khoản khách hàng thành công!');
    }

    // Danh sách khách vãng lai
    public function guestsList()
    {
        $guests = User::where('role', 'guest')->withCount('orders')->orderBy('created_at', 'desc')->get();
        return view('admin.guests', compact('guests'));
    }

    // Xuất báo cáo khách hàng (Excel .xlsx)
    public function exportCustomersExcel(Request $request)
    {
        $query = User::where('role', 'customer')->orderBy('created_at', 'desc');
        $query = $this->applyCustomerFilter($query, $request->input('filter'));
        $query = $this->applyAdvancedCustomerFilters($query, $request);

        $users = $query->with([
            'orders' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
        ])->get();

        if ($users->isEmpty()) {
            return redirect()->back()->with('error', 'Không có khách hàng để xuất.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách khách hàng');

        $now = now();
        $title = 'DANH SÁCH KHÁCH HÀNG';
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Ngày xuất: ' . $now->format('d/m/Y H:i'));
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $headers = [
            'STT',
            'Mã khách hàng',
            'Họ và tên',
            'Email',
            'Số điện thoại',
            'Giới tính',
            'Ngày sinh',
            'Địa chỉ',
            'Ngày đăng ký',
            'Trạng thái tài khoản',
            'Tổng số đơn hàng',
            'Tổng tiền đã chi',
            'Điểm tích lũy',
            'Hạng thành viên',
            'Lần đăng nhập cuối',
        ];

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($index + 1) . '4', $header);
        }

        $sheet->getStyle('A4:O4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $rowNumber = 5;
        foreach ($users as $index => $user) {
            $gender = $this->formatGender($user->gender ?? null);
            $birthday = $user->birthday ? $user->birthday->format('d/m/Y') : '';
            $address = $this->extractAddressFromNotes($user->notes);
            $statusText = $user->status ? 'Hoạt động' : 'Khóa';
            $totalOrders = $user->orders->count();
            $totalSpent = $user->orders->whereIn('payment_status', ['paid', 'completed'])->sum('final_amount');
            $membershipLabel = $this->formatMembership($user->membership);
            $lastLogin = $user->last_login ? $user->last_login->format('d/m/Y H:i:s') : '';

            $values = [
                $index + 1,
                'KH-' . sprintf('%03d', $user->id),
                $user->fullname,
                $user->email,
                $user->phone ? ('=' . $user->phone) : '',
                $gender,
                $birthday,
                $address,
                $user->created_at->format('d/m/Y H:i'),
                $statusText,
                $totalOrders,
                $totalSpent,
                $user->points ?? 0,
                $membershipLabel,
                $lastLogin,
            ];

            foreach ($values as $colIndex => $value) {
                $cellAddress = Coordinate::stringFromColumnIndex($colIndex + 1) . $rowNumber;
                if ($colIndex === 4) {
                    $sheet->setCellValueExplicit($cellAddress, $value, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($cellAddress, $value);
                }
            }

            $rowNumber++;
        }

        $dataRange = 'A5:O' . ($rowNumber - 1);
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $sheet->getStyle('A5:A' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F5:F' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J5:J' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K5:K' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('M5:M' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('N5:N' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D5:D' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('C5:C' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('H5:H' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('L5:L' . ($rowNumber - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle('L5:L' . ($rowNumber - 1))->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'O') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = 'DanhSachKhachHang_' . $now->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function extractAddressFromNotes(?string $notes): string
    {
        if (empty($notes)) {
            return '';
        }

        if (str_contains($notes, 'Địa chỉ:')) {
            $parts = explode('Địa chỉ:', $notes, 2);
            return trim($parts[1]);
        }

        return trim($notes);
    }

    private function formatGender(?string $gender): string
    {
        return match ($gender) {
            'male', 'nam' => 'Nam',
            'female', 'nu', 'nữ' => 'Nữ',
            default => 'Khác',
        };
    }

    private function formatMembership(?string $membership): string
    {
        return match ($membership) {
            'diamond' => 'Kim Cương',
            'gold' => 'Vàng',
            'silver' => 'Bạc',
            default => 'Đồng',
        };
    }
}
