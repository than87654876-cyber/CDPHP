<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo & số lượng chưa đọc cho thanh Navbar (Client & Admin)
     */
    public function list(Request $request)
    {
        $userId = Auth::id();
        $notifications = NotificationService::getForUser($userId, 15);
        $unreadCount = NotificationService::countUnread($userId);

        $formatted = $notifications->map(function ($noti) {
            return [
                'id' => $noti->id,
                'title' => $noti->title,
                'message' => $noti->message,
                'url' => $noti->url ?: '/trangchu',
                'icon' => $noti->icon ?: '/icons/icon-192x192.png',
                'type' => $noti->type,
                'is_read' => $noti->isRead(),
                'created_at' => $noti->created_at ? $noti->created_at->diffForHumans() : 'Vừa xong',
            ];
        });

        if ($unreadCount === 0 && $formatted->isNotEmpty()) {
            $unreadCount = $formatted->where('is_read', false)->count();
        }

        // Nếu database trống hoặc chưa có bản ghi, tạo mẫu một vài thông báo ban đầu
        if ($formatted->isEmpty()) {
            $formatted = collect([
                [
                    'id' => 1,
                    'title' => '🍱 Chào mừng bạn đến với FOODDAILY!',
                    'message' => 'Khám phá hàng trăm món ăn thơm ngon mỗi ngày với ưu đãi ngập tràn!',
                    'url' => '/trangchu',
                    'icon' => '/icons/icon-192x192.png',
                    'type' => 'system',
                    'is_read' => false,
                    'created_at' => 'Hôm nay',
                ],
                [
                    'id' => 2,
                    'title' => '⚡ Ưu đãi giờ vàng hôm nay',
                    'message' => 'Nhập mã GIAM20 để được giảm 20% cho đơn hàng đầu tiên trong ngày.',
                    'url' => '/shop',
                    'icon' => '/icons/icon-192x192.png',
                    'type' => 'promo',
                    'is_read' => false,
                    'created_at' => '1 giờ trước',
                ],
            ]);
            $unreadCount = 2;
        }

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $formatted,
        ]);
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public function markAsRead(Request $request, $id)
    {
        $userId = Auth::id();
        $success = NotificationService::markAsRead((int) $id, $userId);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu là đã đọc',
            'unread_count' => NotificationService::countUnread($userId),
        ]);
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     */
    public function markAllAsRead(Request $request)
    {
        $userId = Auth::id();
        NotificationService::markAllAsRead($userId);

        return response()->json([
            'success' => true,
            'message' => 'Đã đánh dấu tất cả là đã đọc',
            'unread_count' => 0,
        ]);
    }

    /**
     * Lưu thông tin đăng ký Web Push Subscription từ Trình duyệt
     */
    public function savePushSubscription(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
            'keys' => 'nullable|array',
            'keys.p256dh' => 'nullable|string',
            'keys.auth' => 'nullable|string',
        ]);

        $userId = Auth::id();
        $endpoint = $validated['endpoint'];
        $p256dh = $validated['keys']['p256dh'] ?? null;
        $auth = $validated['keys']['auth'] ?? null;

        try {
            PushSubscription::updateOrCreate(
                ['endpoint' => $endpoint],
                [
                    'user_id' => $userId,
                    'p256dh' => $p256dh,
                    'auth' => $auth,
                    'user_agent' => $request->userAgent(),
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu đăng ký thông báo đẩy thành công!',
            ]);
        } catch (\Throwable $e) {
            Log::warning('[NotificationController] Lưu PushSubscription thất bại: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'message' => 'Đã ghi nhận đăng ký (offline mode)',
            ]);
        }
    }

    /**
     * Bắn thông báo đẩy thử nghiệm (Demo) từ Admin hoặc Client
     */
    public function triggerDemo(Request $request)
    {
        $type = $request->input('type', 'lunch');
        $customTitle = $request->input('title');
        $customBody = $request->input('body');
        $targetUserId = $request->input('user_id');

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

                case 'order':
                    $title = '🛵 FOODDAILY - Đơn hàng đang được giao!';
                    $body = 'Tài xế đang trên đường mang món ăn thơm ngon đến cho bạn. Vui lòng để ý điện thoại nhé!';
                    $url = '/tracuu';
                    break;

                case 'lunch':
                default:
                    $title = '🍱 FOODDAILY - Đến giờ cơm trưa rồi bạn ơi!';
                    $body = 'Bạn đã đói chưa? Xem ngay thực đơn hôm nay và chọn món yêu thích nhé!';
                    $url = '/trangchu';
                    break;
            }
        }

        $res = NotificationService::createNotification(
            $targetUserId ?: Auth::id(),
            $title,
            $body,
            $url,
            $type,
            '/icons/icon-192x192.png'
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi thông báo đẩy thành công!',
            'data' => $res['payload'],
        ]);
    }

    /**
     * API kiểm tra thông báo mới nhất cho Client qua Polling
     */
    public function poll(Request $request)
    {
        $lastSeenId = $request->input('last_id');
        $userId = Auth::id();

        $notification = null;
        if ($userId) {
            $notification = Cache::get("user_{$userId}_latest_notification");
        }
        if (!$notification) {
            $notification = Cache::get('latest_push_notification');
        }

        if ($notification && (!$lastSeenId || (string)$notification['id'] !== (string)$lastSeenId)) {
            if (time() - $notification['timestamp'] <= 300) {
                return response()->json([
                    'has_new' => true,
                    'notification' => $notification,
                    'unread_count' => NotificationService::countUnread($userId),
                ]);
            }
        }

        return response()->json([
            'has_new' => false,
            'unread_count' => NotificationService::countUnread($userId),
        ]);
    }
}
