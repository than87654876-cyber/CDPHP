<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chào mừng thành viên mới - FOODDAILY</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f8f9fa;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; background-color: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div style="background: linear-gradient(135deg, #ce1126, #a00d20); color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-family: 'Amatic SC', Arial, sans-serif; font-size: 36px; letter-spacing: 2px;">FOODDAILY</h1>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Hương Vị Tuyệt Vời - Trọn Vẹn Niềm Vui</p>
        </div>
        <div style="padding: 30px; text-align: left;">
            <h2 style="color: #ce1126; margin-top: 0;">Xin chào {{ $fullname }},</h2>
            <p>Chào mừng bạn đã đăng ký tài khoản thành viên thành công tại <strong>FOODDAILY</strong>!</p>
            <p>Kể từ nay, bạn đã có thể đăng nhập vào hệ thống để đặt món nhanh chóng, theo dõi trạng thái đơn hàng thời gian thực, tích lũy điểm thưởng và nhận các ưu đãi độc quyền dành riêng cho thành viên.</p>
            <div style="background-color: #fff8f8; border-left: 4px solid #ce1126; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <h4 style="margin: 0 0 5px 0; color: #ce1126;">Đặc quyền thành viên của bạn:</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li><strong>Tích điểm đổi quà:</strong> Mỗi đơn hàng thành công giúp tích lũy điểm để thăng hạng (Bronze, Silver, Gold, Diamond).</li>
                    <li><strong>Theo dõi đơn hàng:</strong> Quản lý tiến trình chế biến và giao hàng dễ dàng.</li>
                    <li><strong>Chatbot AI:</strong> Nhận gợi ý món ăn phù hợp với khẩu vị từ trợ lý ảo Gemini AI.</li>
                </ul>
            </div>
            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ route('trangchu') }}" style="background-color: #ce1126; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; box-shadow: 0 4px 6px rgba(206,17,38,0.2);">
                    Khám phá thực đơn ngay
                </a>
            </p>
            <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua số điện thoại hỗ trợ hoặc phản hồi trực tiếp email này.</p>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
            <p style="font-size: 12px; color: #777; text-align: center; margin-bottom: 0;">
                Email này được gửi tự động từ hệ thống FOODDAILY.<br>
                Địa chỉ: Tầng 12, Tòa nhà Saigon Innovation Tower, Quận 3, TP. Hồ Chí Minh.
            </p>
        </div>
    </div>
</body>
</html>
