<?php

namespace App\Services;

use App\Events\PushNotificationSent;
use App\Models\AppNotification;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Gửi thông báo đến 1 người dùng cụ thể
     */
    public static function sendToUser($userId, string $title, string $message, string $url = '/trangchu', string $type = 'system', ?string $icon = null, array $data = [])
    {
        return self::createNotification($userId, $title, $message, $url, $type, $icon, $data);
    }

    /**
     * Gửi thông báo chung cho toàn bộ hệ thống
     */
    public static function sendToAll(string $title, string $message, string $url = '/trangchu', string $type = 'system', ?string $icon = null, array $data = [])
    {
        return self::createNotification(null, $title, $message, $url, $type, $icon, $data);
    }

    /**
     * Tạo thông báo, kích hoạt WebSocket realtime và lưu cache
     */
    public static function createNotification(?int $userId, string $title, string $message, string $url = '/trangchu', string $type = 'system', ?string $icon = null, array $data = [])
    {
        $iconUrl = $icon ?: '/icons/icon-192x192.png';
        $createdNotification = null;

        // 1. Lưu vào Database (nếu kết nối DB khả dụng)
        try {
            $createdNotification = AppNotification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'url' => $url,
                'icon' => $iconUrl,
                'data' => $data,
                'read_at' => null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[NotificationService] Lưu DB thất bại (có thể chưa chạy migrate hoặc DB offline): ' . $e->getMessage());
        }

        $payload = [
            'id' => $createdNotification ? $createdNotification->id : uniqid('noti_'),
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'body' => $message,
            'url' => $url,
            'icon' => $iconUrl,
            'type' => $type,
            'data' => $data,
            'created_at' => now()->toIso8601String(),
            'time_human' => 'Vừa xong',
            'is_read' => false,
            'timestamp' => time(),
        ];

        // 2. Lưu vào Cache để phục vụ Polling
        Cache::put('latest_push_notification', $payload, 3600);
        if ($userId) {
            Cache::put("user_{$userId}_latest_notification", $payload, 3600);
        }

        // 3. Bắn WebSocket Realtime tới Laravel Echo / Reverb
        try {
            event(new PushNotificationSent($title, $message, $url, $iconUrl, $type));
        } catch (\Throwable $e) {
            Log::info('[NotificationService] WebSocket broadcast skipped (Reverb offline)');
        }

        return [
            'notification' => $createdNotification,
            'payload' => $payload,
        ];
    }

    /**
     * Lấy danh sách thông báo của User
     */
    public static function getForUser(?int $userId, int $limit = 15)
    {
        if (!$userId) {
            // Khách chưa đăng nhập: lấy thông báo chung của hệ thống
            try {
                return AppNotification::whereNull('user_id')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
            } catch (\Throwable $e) {
                return collect([]);
            }
        }

        try {
            return AppNotification::where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                      ->orWhereNull('user_id');
                })
                ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END ASC')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Throwable $e) {
            return collect([]);
        }
    }

    /**
     * Đếm số lượng chưa đọc
     */
    public static function countUnread(?int $userId): int
    {
        try {
            if (!$userId) {
                return AppNotification::whereNull('user_id')
                    ->whereNull('read_at')
                    ->count();
            }

            return AppNotification::where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                      ->orWhereNull('user_id');
                })
                ->whereNull('read_at')
                ->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public static function markAsRead(int $id, ?int $userId = null): bool
    {
        try {
            $query = AppNotification::where('id', $id);
            if ($userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhereNull('user_id');
                });
            }
            $noti = $query->first();
            if ($noti) {
                $noti->update(['read_at' => now()]);
                return true;
            }
        } catch (\Throwable $e) {
            Log::error('[NotificationService] markAsRead error: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     */
    public static function markAllAsRead(?int $userId): int
    {
        if (!$userId) {
            return 0;
        }

        try {
            return AppNotification::where(function ($q) use ($userId) {
                    $q->where('user_id', $userId)->orWhereNull('user_id');
                })
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('[NotificationService] markAllAsRead error: ' . $e->getMessage());
            return 0;
        }
    }
}
