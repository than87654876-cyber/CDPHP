@echo off
chcp 65001 >nul
title KHỞI ĐỘNG HỆ THỐNG FOODDAILY & SEPAY WEBHOOK
color 0A

echo ==============================================================
echo         HỆ THỐNG GIAO ĐỒ ĂN FOODDAILY - SEPAY WEBHOOK
echo ==============================================================
echo.
echo [1/2] Đang khởi động máy chủ Laravel (Cổng 8000)...
start "FOODDAILY - Laravel Server" cmd /k "php artisan serve --port=8000"

timeout /t 3 >nul

echo [2/2] Đang khởi động đường truyền Webhook Cloudflare...
start "FOODDAILY - Cloudflare Tunnel" cmd /k ""C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel --url http://127.0.0.1:8000"

echo.
echo ==============================================================
echo  ĐÃ KHỞI ĐỘNG HOÀN TẤT!
echo  - Web đang chạy tại: http://localhost:8000
echo  - Cửa sổ Cloudflare sẽ hiển thị đường link https://...trycloudflare.com
echo    để bạn dán vào SePay (nếu link có thay đổi).
echo ==============================================================
echo.
pause
