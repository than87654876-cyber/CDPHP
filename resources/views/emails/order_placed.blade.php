<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận đơn hàng FDL-{{ $order->id }} - FOODDAILY</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f8f9fa;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; background-color: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div style="background: linear-gradient(135deg, #ce1126, #a00d20); color: white; padding: 25px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px; letter-spacing: 1px;">XÁC NHẬN ĐƠN HÀNG</h1>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Cảm ơn bạn đã đặt món ăn tại FOODDAILY!</p>
        </div>
        <div style="padding: 25px;">
            <h2 style="color: #ce1126; margin-top: 0; font-size: 20px; border-bottom: 2px solid #f8f9fa; padding-bottom: 10px;">
                Mã đơn hàng: #FDL-{{ $order->id }}
            </h2>
            <p style="font-size: 14px; color: #666;">
                Thời gian đặt: {{ $order->created_at->format('d/m/Y H:i:s') }}
            </p>

            <h3 style="color: #444; font-size: 16px; margin-top: 25px;">Chi tiết món ăn đã chọn:</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px;">
                <thead>
                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #eee;">
                        <th style="padding: 10px; text-align: left; font-weight: bold;">Tên món ăn</th>
                        <th style="padding: 10px; text-align: center; font-weight: bold; width: 80px;">Số lượng</th>
                        <th style="padding: 10px; text-align: right; font-weight: bold; width: 120px;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; text-align: left;">
                            <strong>{{ $item->dish->dish_name }}</strong>
                        </td>
                        <td style="padding: 10px; text-align: center;">{{ $item->quantity }}</td>
                        <td style="padding: 10px; text-align: right; color: #ce1126; font-weight: bold;">
                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ
                        </td>
                    </tr>
                    @endforeach
                    <tr style="border-top: 2px solid #eee;">
                        <td colspan="2" style="padding: 15px 10px 10px 10px; text-align: right; font-weight: bold; font-size: 15px;">Tổng cộng:</td>
                        <td style="padding: 15px 10px 10px 10px; text-align: right; color: #ce1126; font-weight: bold; font-size: 18px;">
                            {{ number_format($order->final_amount, 0, ',', '.') }} đ
                        </td>
                    </tr>
                </tbody>
            </table>

            <h3 style="color: #444; font-size: 16px; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Thông tin giao hàng & Thanh toán:</h3>
            <table style="width: 100%; font-size: 14px; margin-top: 10px;">
                <tr>
                    <td style="padding: 5px 0; color: #666; width: 150px;">Người nhận:</td>
                    <td style="padding: 5px 0; font-weight: bold;">{{ $order->user->fullname }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666;">Số điện thoại:</td>
                    <td style="padding: 5px 0;">{{ $order->user->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666; vertical-align: top;">Ghi chú giao nhận:</td>
                    <td style="padding: 5px 0; white-space: pre-line;">{{ $order->health_notes }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666;">Phương thức thanh toán:</td>
                    <td style="padding: 5px 0; font-weight: bold;">
                        @if($order->payment_method === 'cash')
                            Tiền mặt khi nhận hàng (COD)
                        @elseif($order->payment_method === 'bank_transfer')
                            Chuyển khoản qua Ngân hàng (VietQR/PayOS)
                        @elseif($order->payment_method === 'momo')
                            Ví điện tử MoMo
                        @else
                            {{ $order->payment_method }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #666;">Trạng thái thanh toán:</td>
                    <td style="padding: 5px 0; font-weight: bold; color: {{ $order->payment_status === 'paid' ? '#28a745' : '#ffc107' }}">
                        @if($order->payment_status === 'paid')
                            Đã thanh toán thành công
                        @else
                            Chờ thanh toán
                        @endif
                    </td>
                </tr>
            </table>

            <p style="text-align: center; margin: 35px 0 15px 0;">
                <a href="{{ route('tracuu', ['order_id' => $order->id, 'phone' => $order->user->phone, 'email' => $order->user->email]) }}" style="background-color: #ce1126; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; box-shadow: 0 4px 6px rgba(206,17,38,0.2);">
                    Theo dõi trạng thái đơn hàng
                </a>
            </p>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">
            <p style="font-size: 12px; color: #777; text-align: center; margin-bottom: 0;">
                Cảm ơn bạn đã lựa chọn dịch vụ của chúng tôi!<br>
                Hệ thống đặt món ăn trực tuyến FOODDAILY.
            </p>
        </div>
    </div>
</body>
</html>
