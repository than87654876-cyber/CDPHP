<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Ghi nhận một hành vi vào Audit Trail (Nhật ký hoạt động hệ thống)
     */
    public static function log(
        string $action,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $notes = null,
        ?int $userId = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent() ?? 'System',
            'notes' => $notes,
            'created_at' => now(),
        ]);
    }

    /**
     * Ghi nhận hành vi sửa đổi tồn kho / cân bằng kho
     */
    public static function logInventoryAdjustment(Model $inventoryItem, float $oldQty, float $newQty, string $reason): AuditLog
    {
        return self::log(
            'inventory_adjusted',
            $inventoryItem,
            ['current_stock' => $oldQty],
            ['current_stock' => $newQty, 'difference' => $newQty - $oldQty],
            "Điều chỉnh tồn kho nguyên liệu/món: {$reason}"
        );
    }

    /**
     * Ghi nhận thay đổi giá bán hoặc giá vốn
     */
    public static function logPriceChange(Model $item, float $oldPrice, float $newPrice, string $priceType = 'cost_price'): AuditLog
    {
        return self::log(
            'price_changed',
            $item,
            [$priceType => $oldPrice],
            [$priceType => $newPrice],
            "Thay đổi {$priceType} từ " . number_format($oldPrice) . "đ sang " . number_format($newPrice) . "đ"
        );
    }

    /**
     * Ghi nhận áp dụng chiết khấu tự do (Discount) trên POS
     */
    public static function logDiscountApplied(Model $order, float $discountPercent, ?int $authorizedByManagerId = null): AuditLog
    {
        $note = "Áp dụng chiết khấu {$discountPercent}% cho đơn hàng #{$order->id}";
        if ($authorizedByManagerId) {
            $note .= " (Được ủy quyền bởi Quản lý ID: {$authorizedByManagerId})";
        }

        return self::log(
            'discount_applied',
            $order,
            ['discount' => 0],
            ['discount_percent' => $discountPercent, 'authorized_by' => $authorizedByManagerId],
            $note
        );
    }

    /**
     * Ghi nhận hủy hóa đơn hoặc hoàn tiền
     */
    public static function logOrderCancellation(Model $order, string $reason, ?int $authorizedByManagerId = null): AuditLog
    {
        $note = "Hủy đơn hàng #{$order->id} với lý do: {$reason}";
        if ($authorizedByManagerId) {
            $note .= " (Được duyệt bởi Quản lý ID: {$authorizedByManagerId})";
        }

        return self::log(
            'order_cancelled',
            $order,
            ['status' => $order->status ?? 'active'],
            ['status' => 'cancelled', 'reason' => $reason, 'authorized_by' => $authorizedByManagerId],
            $note
        );
    }
}
