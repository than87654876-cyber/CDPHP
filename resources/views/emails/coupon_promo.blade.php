<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subjectText }} - FOODDAILY</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background-color: #f8f9fa;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; background-color: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
        <div style="background: linear-gradient(135deg, #ce1126, #a00d20); color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 26px; letter-spacing: 1px;">QUÀ TẶNG ƯU ĐÃI</h1>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Ưu đãi độc quyền từ FOODDAILY</p>
        </div>
        <div style="padding: 30px; text-align: left;">
            <h2 style="color: #444; margin-top: 0; font-size: 20px;">Xin chào {{ $fullname }},</h2>
            <div style="font-size: 15px; color: #444; margin-bottom: 25px; white-space: pre-line;">
                {{ $bodyText }}
            </div>

            <div style="border: 2px dashed #ce1126; background-color: #fffaf0; padding: 20px; text-align: center; border-radius: 8px; margin: 25px 0;">
                <p style="margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase; color: #666; font-weight: bold; letter-spacing: 1px;">Mã ưu đãi của bạn:</p>
                <div style="font-size: 28px; font-weight: bold; color: #ce1126; letter-spacing: 2px; margin-bottom: 5px;">
                    {{ $couponCode }}
                </div>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #999; font-style: italic;">
                    (Hạn dùng: {{ $expiryDays }} ngày kể từ ngày kích hoạt mã)
                </p>
            </div>

            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ route('trangchu') }}" style="background-color: #ce1126; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block; box-shadow: 0 4px 6px rgba(206,17,38,0.2);">
                    Đặt món và áp mã ngay
                </a>
            </p>
            <p>Trân trọng,<br>Đội ngũ FOODDAILY</p>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">
            <p style="font-size: 11px; color: #999; text-align: center; line-height: 1.4; margin-bottom: 0;">
                Email này được gửi tự động bởi tính năng Chăm sóc khách hàng của FOODDAILY.<br>
                Mọi thắc mắc vui lòng phản hồi hoặc liên hệ Hotline hỗ trợ của cửa hàng.
            </p>
        </div>
    </div>
</body>
</html>
