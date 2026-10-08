<?php

namespace App\Console\Commands;

use App\Events\PushNotificationSent;
use Illuminate\Console\Command;

class SendMealNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:meal {type=lunch : Loại thông báo (lunch, dinner, promo)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gửi thông báo đẩy (Web Push) theo giờ ăn hoặc khuyến mãi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = strtolower($this->argument('type'));

        switch ($type) {
            case 'dinner':
                $title = '🍲 FOODDAILY - Bữa tối ấm cúng cùng gia đình!';
                $body = 'Nghỉ ngơi thôi! Hàng trăm món ngon nóng hổi đang chờ bạn, đặt ngay kẻo trễ!';
                $url = '/trangchu';
                break;

            case 'promo':
                $title = '⚡ FOODDAILY - Flash Sale Giờ Vàng!';
                $body = 'Giảm giá tới 20% cho các món ăn HOT hôm nay. Đặt ngay nhận ưu đãi!';
                $url = '/trangchu';
                break;

            case 'lunch':
            default:
                $title = '🍱 FOODDAILY - Đến giờ cơm trưa rồi bạn ơi!';
                $body = 'Bạn đã đói chưa? Xem ngay thực đơn hôm nay và chọn món yêu thích nhé!';
                $url = '/trangchu';
                break;
        }

        // Lưu thông báo mới nhất vào Cache để client có thể nhận ngay cả khi không bật Reverb
        \Illuminate\Support\Facades\Cache::put('latest_push_notification', [
            'id' => uniqid('noti_'),
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => '/icons/icon-192x192.png',
            'type' => $type,
            'timestamp' => time(),
        ], 3600);

        // Bắn sự kiện realtime tới channel (nếu có Reverb/Pusher đang chạy)
        try {
            event(new PushNotificationSent($title, $body, $url, '/icons/icon-192x192.png', $type));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[Notification] WebSocket offline, đã lưu vào Cache: ' . $e->getMessage());
        }

        $this->info("✅ Đã tạo thông báo đẩy thành công: [{$type}] - {$title}");
        return 0;
    }
}
