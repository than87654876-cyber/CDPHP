<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManagerPinService
{
    /**
     * Xác thực mã PIN Quản lý để mở khóa hành động đặc biệt trên POS
     * (như chiết khấu vượt mức 5%, hủy hóa đơn, hoàn tiền)
     */
    public static function verifyPin(string $pin, ?int $branchId = null): ?User
    {
        $query = User::where('status', 1)
            ->whereNotNull('manager_pin')
            ->where(function ($q) {
                $q->whereIn('role', ['admin', 'superadmin', 'manager'])
                  ->orWhereHas('roles', function ($rq) {
                      $rq->whereIn('name', ['admin', 'superadmin', 'manager']);
                  });
            });

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhere('role', 'superadmin')
                  ->orWhereNull('branch_id');
            });
        }

        $managers = $query->get();

        foreach ($managers as $manager) {
            // Hỗ trợ cả bcrypt hash hoặc plain text 4-6 số khi khởi tạo demo
            if (Hash::check($pin, $manager->manager_pin) || $manager->manager_pin === $pin) {
                return $manager;
            }
        }

        return null;
    }

    /**
     * Cài đặt hoặc cập nhật mã PIN Quản lý cho tài khoản
     */
    public static function setPin(User $user, string $pin): bool
    {
        return $user->update([
            'manager_pin' => Hash::make($pin),
        ]);
    }

    /**
     * Kiểm tra xem mức chiết khấu có cần mã PIN Quản lý hay không
     * Giới hạn: Nhân viên được chiết khấu tối đa 5%, trên 5% bắt buộc có PIN Quản lý
     */
    public static function requiresManagerApprovalForDiscount(float $discountPercent): bool
    {
        return $discountPercent > 5.0;
    }
}
