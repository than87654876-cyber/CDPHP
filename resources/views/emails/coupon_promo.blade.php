<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectText }} - FOODDAILY</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 20px 10px; background-color: #f1f5f9;">
    <div style="max-width: 600px; margin: 0 auto; border-radius: 20px; overflow: hidden; background-color: #ffffff; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04); border: 1px solid #e2e8f0;">
        
        <!-- Header Banner -->
        <div style="background: linear-gradient(135deg, #ce1126 0%, #ee4d2d 50%, #f97316 100%); color: #ffffff; padding: 35px 25px; text-align: center;">
            <div style="display: inline-block; background-color: rgba(255, 255, 255, 0.2); padding: 6px 16px; border-radius: 9999px; font-size: 12px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 12px; backdrop-filter: blur(4px);">
                ✨ Ưu Đãi Độc Quyền Dành Cho Bạn ✨
            </div>
            <h1 style="margin: 0; font-size: 26px; font-weight: 900; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                FOODDAILY • QUÀ TẶNG VOUCHER
            </h1>
            <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.95; font-weight: 500;">
                Món ngon mỗi ngày - Giao tận cửa - Trọn vẹn dinh dưỡng
            </p>
        </div>

        <!-- Body Content -->
        <div style="padding: 30px 25px;">
            <h2 style="color: #0f172a; margin-top: 0; font-size: 18px; font-weight: 800;">
                Xin chào <span style="color: #ce1126;">{{ $fullname }}</span>,
            </h2>

            <div style="font-size: 15px; color: #334155; margin-bottom: 24px; white-space: pre-line; line-height: 1.7;">
{{ $bodyText }}
            </div>

            <!-- Coupon Highlight Card -->
            <div style="border: 2px dashed #ee4d2d; background: linear-gradient(180deg, #fff7ed 0%, #fff1f2 100%); padding: 24px 20px; text-align: center; border-radius: 16px; margin: 25px 0; position: relative;">
                <p style="margin: 0 0 8px 0; font-size: 12px; text-transform: uppercase; color: #ea580c; font-weight: 800; letter-spacing: 1.5px;">
                    Mã Giảm Giá Của Bạn
                </p>
                <div style="font-size: 30px; font-weight: 900; color: #ce1126; letter-spacing: 3px; margin: 5px 0 8px 0; font-family: 'Courier New', Courier, monospace; background-color: #ffffff; display: inline-block; padding: 6px 24px; border-radius: 12px; border: 1px solid #fed7aa; box-shadow: 0 2px 6px rgba(238, 77, 45, 0.15);">
                    {{ $couponCode }}
                </div>
                <p style="margin: 8px 0 0 0; font-size: 13px; color: #64748b; font-weight: 600;">
                    ⏳ Thời hạn sử dụng: <strong>{{ $expiryDays }} ngày</strong> kể từ khi phát hành
                </p>
            </div>

            <!-- Tips Banner -->
            <div style="background-color: #f8fafc; border-left: 4px solid #ee4d2d; padding: 12px 16px; border-radius: 8px; margin-bottom: 25px; font-size: 13px; color: #475569; line-height: 1.5;">
                💡 <strong>Mẹo nhỏ:</strong> Mã ưu đãi này đã được đồng bộ vào <strong>Ví Voucher</strong> trong tài khoản của bạn. Khi đăng nhập và tiến hành thanh toán, hệ thống sẽ tự động đề xuất mã để bạn tiết kiệm chi phí tối đa!
            </div>

            <!-- Prominent Call To Action Button -->
            <div style="text-align: center; margin: 30px 0 15px 0;">
                <a href="{{ $websiteUrl ?? route('muahang') }}" target="_blank" style="background: linear-gradient(135deg, #ce1126 0%, #ee4d2d 100%); color: #ffffff; padding: 16px 36px; text-decoration: none; border-radius: 9999px; font-weight: 800; font-size: 15px; display: inline-block; box-shadow: 0 8px 20px -4px rgba(206, 17, 38, 0.4); text-transform: uppercase; letter-spacing: 0.5px;">
                    🛒 Đặt Món & Áp Mã Ngay
                </a>
            </div>

            <!-- Explicit Website Link -->
            <div style="background-color: #f1f5f9; border-radius: 12px; padding: 14px 18px; margin: 20px 0; text-align: center;">
                <p style="margin: 0 0 6px 0; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                    Đường dẫn truy cập trang web:
                </p>
                <a href="{{ $websiteUrl ?? route('muahang') }}" target="_blank" style="font-size: 14px; font-weight: 800; color: #ee4d2d; text-decoration: underline; word-break: break-all;">
                    {{ $websiteUrl ?? route('muahang') }}
                </a>
            </div>

            <p style="font-size: 14px; color: #475569; margin: 25px 0 0 0;">
                Chúc bạn có những bữa ăn ngon miệng và trọn vẹn niềm vui cùng FOODDAILY!<br>
                <strong>Đội ngũ Chăm sóc khách hàng FOODDAILY</strong>
            </p>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;">

            <!-- Footer -->
            <div style="font-size: 11px; color: #94a3b8; text-align: center; line-height: 1.6;">
                <p style="margin: 0 0 4px 0;">
                    Hệ thống chuỗi suất ăn dinh dưỡng & ẩm thực giao nhanh <strong>FOODDAILY</strong>.
                </p>
                <p style="margin: 0 0 4px 0;">
                    Website chính thức: <a href="{{ url('/') }}" target="_blank" style="color: #64748b; font-weight: bold; text-decoration: none;">{{ url('/') }}</a> • Hotline hỗ trợ: <strong>1900 xxxx</strong>
                </p>
                <p style="margin: 0;">
                    Email này được gửi tự động tới tài khoản đăng ký của bạn. Vui lòng không phản hồi trực tiếp vào địa chỉ này.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
