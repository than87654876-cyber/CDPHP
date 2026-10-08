<?php

namespace App\Http\Controllers;

use App\Events\PushNotificationSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Bắn thông báo đẩy thử nghiệm (Demo) từ Admin hoặc Client
     */
    public function triggerDemo(Request $request)
    {
        $type = $request->input('type', 'lunch');
        $customTitle = $request->input('title');
        $customBody = $request->input('body');

        if ($customTitle && $customBody) {
            $title = $customTitle;
            $body = $customBody;
            $url = $request->input('url', '/trangchu');
        } else {
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
        }

        $notificationData = [
            'id' => uniqid('noti_'),
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => '/icons/icon-192x192.png',
            'type' => $type,
            'timestamp' => time(),
        ];

        // Lưu vào Cache để client có thể nhận ngay lập tức qua API Polling
        Cache::put('latest_push_notification', $notificationData, 3600);

        // Bắn WebSocket Event tới Laravel Echo / Reverb (nếu đang bật)
        try {
            event(new PushNotificationSent($title, $body, $url, '/icons/icon-192x192.png', $type));
        } catch (\Throwable $e) {
            Log::info('[NotificationController] WebSocket broadcast skipped (Reverb offline)');
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi thông báo đẩy thành công!',
            'data' => $notificationData,
        ]);
    }

    /**
     * API kiểm tra thông báo mới nhất cho Client
     */
    public function poll(Request $request)
    {
        $lastSeenId = $request->input('last_id');
        $notification = Cache::get('latest_push_notification');

        if ($notification && (!$lastSeenId || $notification['id'] !== $lastSeenId)) {
            // Chỉ gửi nếu thông báo được tạo trong vòng 5 phút qua
            if (time() - $notification['timestamp'] <= 300) {
                return response()->json([
                    'has_new' => true,
                    'notification' => $notification,
                ]);
            }
        }

        return response()->json([
            'has_new' => false,
        ]);
    }
}
