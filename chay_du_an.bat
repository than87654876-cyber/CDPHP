@echo off
setlocal
chcp 65001 >nul
cd /d "%~dp0"
title FOODDAILY - HE THONG KHOI DONG THUYET TRINH (LARAVEL + REVERB + SEPAY)
color 0B

echo ======================================================================
echo             HE THONG GIAO DO AN FOODDAILY - CHE DO THUYET TRINH
echo ======================================================================
echo.

tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I /N "mysqld.exe" >nul
if "%ERRORLEVEL%"=="0" (
    echo [OK] Co so du lieu MySQL dang hoat dong tot.
) else (
    echo [CHU Y] Ban chua bat Laragon!
    echo Website can Database MySQL. Vui long mo Laragon va bam Start All!
    echo.
)

echo [1/4] Dang khoi dong may chu Laravel Web Server (Port 8000)...
start "FOODDAILY - Laravel Web Server" cmd /k "cd /d "%~dp0" && php artisan serve --port=8000"

echo [2/4] Dang khoi dong Reverb WebSocket Server (Port 8080 - Thong bao Realtime)...
start "FOODDAILY - WebSocket Reverb" cmd /k "cd /d "%~dp0" && php artisan reverb:start --port=8080"

echo [3/4] Dang ket noi duong truyen Cloudflare Tunnel (Thanh toan Quet Ma QR)...
if exist "storage\logs\cloudflared.log" del /f /q "storage\logs\cloudflared.log"
start "FOODDAILY - Cloudflare Tunnel" cmd /k ""C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel --url http://127.0.0.1:8000 --logfile "%~dp0storage\logs\cloudflared.log""

echo [4/4] Dang lay duong dan Webhook SePay moi...
set "WEBHOOK_URL="

for /L %%i in (1,1,12) do (
    ping 127.0.0.1 -n 2 >nul
    for /f "delims=" %%a in ('php extract_tunnel_url.php "%~dp0storage\logs\cloudflared.log" 2^>nul') do (
        set "WEBHOOK_URL=%%a"
    )
    if defined WEBHOOK_URL goto FOUND_URL
)

:FOUND_URL
cls
color 0A
echo ======================================================================
echo                  HE THONG FOODDAILY DA SAN SANG THUYET TRINH!
echo ======================================================================
echo.
echo  - Website Khach hang:     http://localhost:8000
echo  - Hoac qua Laragon VHost: http://cdphp.test
echo  - Trang quan tri Admin:   http://localhost:8000/quanly
echo  - Ban lam viec Bep:       http://localhost:8000/quanly_bep
echo  - Ban lam viec NV / Kho:  http://localhost:8000/quanly_banlamviec
echo.
if defined WEBHOOK_URL (
    echo  - Webhook SePay:          %WEBHOOK_URL%
    echo %WEBHOOK_URL%| clip
    echo.
    echo  ------------------------------------------------------------------
    echo  [DA TU DONG COPY] Link Webhook da duoc luu vao bo nho tam!
    echo. 
    echo  HUONG DAN KET NOI SEPAY:
    echo  1. Mo https://my.sepay.vn
    echo  2. Vao muc Webhook -> Dan [Ctrl + V] vao o URL Webhook
    echo  3. Bam "Gui kiem tra" va Luu lai
    echo  ------------------------------------------------------------------
) else (
    echo  [!] Chua bat kip Webhook. Ban co the kiem tra cua so Cloudflare.
)
echo.
echo ======================================================================
echo  Dang tu dong mo Website FOODDAILY tren trinh duyet...
echo ======================================================================
start http://localhost:8000
echo.
echo  Nhan phim bat ky neu ban muon mo them trang quan tri SePay...
pause >nul
start https://my.sepay.vn
