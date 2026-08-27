<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mã OTP khôi phục mật khẩu tài khoản FOODDAILY</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f8f9fa;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; background-color: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div style="background: linear-gradient(135deg, #ce1126, #a00d20); color: white; padding: 25px; text-align: center;">
            <h1 style="margin: 0; font-size: 26px; letter-spacing: 1px;">MÃ OTP XÁC THỰC</h1>
        </div>
        <div style="padding: 30px; text-align: left;">
            <h2 style="color: #444; margin-top: 0; font-size: 20px;">Xin chào {{ $fullname }},</h2>
            <p>Hệ thống nhận được yêu cầu khôi phục mật khẩu từ tài khoản của bạn tại <strong>FOODDAILY</strong>.</p>
            <p>Để đổi lại mật khẩu mới, vui lòng sử dụng mã xác thực OTP (One Time Password) dưới đây:</p>
            <div style="text-align: center; margin: 35px 0;">
                <span style="font-family: 'Courier New', Courier, monospace; font-size: 38px; font-weight: bold; color: #ce1126; letter-spacing: 8px; padding: 15px 30px; border: 2px dashed #ce1126; border-radius: 8px; background-color: #fff8f8; display: inline-block;">
                    {{ $otp }}
                </span>
            </div>
            <div style="background-color: #fff8f8; border-left: 4px solid #ce1126; padding: 12px; margin: 20px 0; border-radius: 4px; font-size: 13px; color: #555;">
                <strong>Lưu ý quan trọng:</strong><br>
                - Mã xác thực OTP này chỉ có hiệu lực trong vòng <strong>10 phút</strong> kể từ khi email này được gửi.<br>
                - Tuyệt đối không chia sẻ mã OTP này với bất kỳ ai để bảo mật tài khoản của bạn.<br>
                - Nếu bạn không yêu cầu hành động này, bạn có thể yên tâm bỏ qua email này. Mật khẩu cũ của bạn vẫn sẽ được giữ an toàn.
            </div>
            <p>Trân trọng,<br>Ban quản trị FOODDAILY</p>
        </div>
    </div>
</body>
</html>
