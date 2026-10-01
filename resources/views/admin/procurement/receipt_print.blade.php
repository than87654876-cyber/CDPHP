<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>In Phiếu Nhập Kho - {{ $receipt->receipt_code }}</title>
    <style>
        body {
            font-family: "DejaVu Sans", "Times New Roman", serif, sans-serif;
            font-size: 13px;
            color: #111;
            margin: 20px;
            line-height: 1.4;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .title {
            text-align: center;
            margin: 20px 0 10px 0;
        }
        .title h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-box {
            width: 48%;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-size: 11px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .signatures {
            display: flex;
            justify-content: space-between;
            text-align: center;
            margin-top: 40px;
        }
        .sig-col {
            width: 30%;
        }
        .sig-space {
            height: 70px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #ee4d2d; color: #fff; border: none; font-weight: bold; cursor: pointer; border-radius: 4px;">
            In Phiếu (Print)
        </button>
    </div>

    <div class="header">
        <div>
            <strong>FOODDAILY - HỆ THỐNG SUẤT ĂN DINH DƯỠNG</strong><br>
            Địa chỉ: Ký Túc Xá Khu B, ĐHQG-HCM, Dĩ An, Bình Dương<br>
            Hotline Bếp: 1900 6868 - Email: contact@fooddaily.vn
        </div>
        <div style="text-align: right;">
            <strong>Mã phiếu: {{ $receipt->receipt_code }}</strong><br>
            Ngày nhận: {{ $receipt->receipt_date->format('d/m/Y H:i') }}<br>
            Trạng thái: {{ $receipt->payment_status === 'paid' ? 'ĐÃ THANH TOÁN' : 'GHI NỢ NCC' }}
        </div>
    </div>

    <div class="title">
        <h1>PHIẾU NHẬP KHO HÀNG HÓA</h1>
        <p><em>(Kiểm kê và nghiệm thu số lượng thực tế nhập kho)</em></p>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <strong>ĐƠN VỊ CUNG CẤP (BÊN GIAO HÀNG):</strong><br>
            Tên công ty: <strong>{{ $receipt->supplier->name ?? 'N/A' }}</strong><br>
            Mã NCC: {{ $receipt->supplier->supplier_code ?? '-' }}<br>
            Hotline / SĐT: {{ $receipt->supplier->phone ?? '-' }}<br>
            Địa chỉ: {{ $receipt->supplier->address ?? '-' }}
        </div>
        <div class="info-box">
            <strong>BÊN NHẬN HÀNG (KHO BẾP FOODDAILY):</strong><br>
            Kho nhập: <strong>Kho Bếp Trung Tâm FOODDAILY</strong><br>
            Người kiểm nhận: <strong>{{ $receipt->creator->name ?? 'Admin' }}</strong><br>
            Thời điểm nhận: {{ $receipt->receipt_date->format('d/m/Y H:i') }}<br>
            Ghi chú / Số HĐ đỏ: {{ $receipt->notes ?? 'Không có' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">STT</th>
                <th>Tên Mặt Hàng / Nguyên Liệu</th>
                <th class="text-center" style="width: 70px;">Đơn Vị</th>
                <th class="text-center" style="width: 80px;">Số Lượng</th>
                <th class="text-right" style="width: 100px;">Đơn Giá (VNĐ)</th>
                <th class="text-right" style="width: 120px;">Thành Tiền (VNĐ)</th>
                <th class="text-center" style="width: 90px;">Hạn Dùng</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->item->name ?? '-' }}</strong><br>
                    <small>Mã: {{ $item->item->item_code ?? '-' }}</small>
                </td>
                <td class="text-center">{{ $item->item->unit ?? '-' }}</td>
                <td class="text-center"><strong>{{ rtrim(rtrim(number_format($item->quantity, 2, '.', ','), '0'), '.') }}</strong></td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right"><strong>{{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
                <td class="text-center">{{ $item->expiry_date ? $item->expiry_date->format('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>TỔNG CỘNG TIỀN HÀNG:</strong></td>
                <td class="text-right" style="font-size: 15px; color: #ee4d2d;">
                    <strong>{{ number_format($receipt->total_amount, 0, ',', '.') }} VNĐ</strong>
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div class="sig-col">
            <strong>NGƯỜI GIAO HÀNG</strong><br>
            <em>(Ký, ghi rõ họ tên)</em>
            <div class="sig-space"></div>
            <span>{{ $receipt->supplier->contact_person ?? 'Đại diện bên bán' }}</span>
        </div>
        <div class="sig-col">
            <strong>THỦ KHO / NGƯỜI NHẬN</strong><br>
            <em>(Ký, ghi rõ họ tên)</em>
            <div class="sig-space"></div>
            <span>{{ $receipt->creator->name ?? 'Admin' }}</span>
        </div>
        <div class="sig-col">
            <strong>KẾ TOÁN / QUẢN LÝ</strong><br>
            <em>(Ký duyệt)</em>
            <div class="sig-space"></div>
            <span>Đã duyệt hệ thống</span>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Uncomment to auto print: window.print();
        };
    </script>
</body>
</html>
