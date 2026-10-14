<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('orders:cancel-overdue')->everyMinute();
Schedule::command('backup:customers')->dailyAt('00:00');

// Thông báo đẩy (Web Push) theo khung giờ ăn uống
Schedule::command('notify:meal lunch')->dailyAt('11:00');
Schedule::command('notify:meal dinner')->dailyAt('18:00');

Artisan::command('mail:test {email? : Địa chỉ email nhận thử nghiệm}', function ($email = null) {
    $recipient = $email ?: env('MAIL_FROM_ADDRESS', 'than28112000@gmail.com');
    $this->info("Đang kiểm tra kết nối SMTP tới Google Gmail...");
    $this->line("Tài khoản gửi: " . env('MAIL_USERNAME'));
    $this->line("Người nhận: " . $recipient);
    
    $mailer = app(\App\Services\PHPMailerService::class);
    $result = $mailer->send(
        $recipient,
        'Kiểm thử kết nối Email FOODDAILY 🚀',
        '<h2>Chúc mừng bạn!</h2><p>Hệ thống gửi email của <strong>FOODDAILY</strong> đã kết nối SMTP thành công.</p><p>Đường dẫn website: <a href="' . url('/') . '">' . url('/') . '</a></p>'
    );
    
    if ($result) {
        $this->info("✅ GỬI THÀNH CÔNG! Vui lòng mở hòm thư (hoặc mục Spam/Thư rác) của {$recipient} để kiểm tra.");
    } else {
        $this->error("❌ GỬI THẤT BẠI: Google từ chối mật khẩu (BadCredentials / 535-5.7.8).");
        $this->warn("Nguyên nhân: Mật khẩu ứng dụng trong .env (MAIL_PASSWORD) đã hết hạn hoặc không chính xác.");
        $this->line("Cách xử lý: Truy cập https://myaccount.google.com/apppasswords để tạo Mật khẩu ứng dụng mới (16 chữ cái) và cập nhật vào MAIL_PASSWORD trong file .env.");
    }
})->purpose('Kiểm tra kết nối SMTP và gửi thử email');
