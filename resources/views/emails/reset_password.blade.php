<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Khôi phục mật khẩu tài khoản FOODDAILY</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f8f9fa;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; background-color: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div style="background: linear-gradient(135deg, #ce1126, #a00d20); color: white; padding: 25px; text-align: center;">
            <h1 style="margin: 0; font-size: 26px; letter-spacing: 1px;">KHÔI PHỤC MẬT KHẨU</h1>
        </div>
        <div style="padding: 30px; text-align: left;">
            <h2 style="color: #444; margin-top: 0; font-size: 20px;">Xin chào {{ $fullname }},</h2>
            <p>Hệ thống nhận được yêu cầu khôi phục mật khẩu từ tài khoản của bạn tại <strong>FOODDAILY</strong>.</p>
            <p>Để đổi lại mật khẩu mới, vui lòng nhấn vào nút liên kết dưới đây:</p>
            <p style="text-align: center; margin: 35px 0;">
                <a href="{{ $resetLink }}" style="background-color: #ce1126; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; box-shadow: 0 4px 6px rgba(206,17,38,0.2);">
                    Đặt lại mật khẩu mới
                </a>
            </p>
            <div style="background-color: #fff8f8; border-left: 4px solid #ce1126; padding: 12px; margin: 20px 0; border-radius: 4px; font-size: 13px; color: #555;">
                <strong>Lưu ý quan trọng:</strong><br>
                - Đường dẫn khôi phục này có hiệu lực trong vòng <strong>60 phút</strong> kể từ khi email này được gửi.<br>
                - Nếu bạn không yêu cầu hành động này, bạn có thể yên tâm bỏ qua email này. Mật khẩu cũ của bạn vẫn sẽ được giữ an toàn.
            </div>
            <p>Trân trọng,<br>Ban quản trị FOODDAILY</p>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">
            <p style="font-size: 11px; color: #999; text-align: center; line-height: 1.4; margin-bottom: 0;">
                Nếu nút bấm ở trên không hoạt động, vui lòng sao chép và dán đường dẫn dưới đây vào trình duyệt:<br>
                <a href="{{ $resetLink }}" style="color: #ce1126; word-break: break-all;">{{ $resetLink }}</a>
            </p>
        </div>
    </div>
</body>
</html>
