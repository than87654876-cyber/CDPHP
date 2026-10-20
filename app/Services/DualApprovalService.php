<?php

namespace App\Services;

use App\Models\DualApproval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DualApprovalService
{
    /**
     * Ngưỡng giá trị kích hoạt bắt buộc duyệt 2 bước độc lập
     */
    public const LARGE_AMOUNT_THRESHOLD = 20000000; // 20.000.000đ

    /**
     * Kiểm tra xem một giao dịch có vượt ngưỡng yêu cầu duyệt 2 bước hay không
     */
    public static function requiresDualApproval(float $amount): bool
    {
        return $amount >= self::LARGE_AMOUNT_THRESHOLD;
    }

    /**
     * Tạo yêu cầu phê duyệt 2 bước
     */
    public static function createRequest(
        Model $approvable,
        string $actionType,
        float $amountValue,
        ?string $notes = null,
        ?int $requestedBy = null
    ): DualApproval {
        return DualApproval::create([
            'approvable_type' => get_class($approvable),
            'approvable_id' => $approvable->id,
            'action_type' => $actionType,
            'amount_value' => $amountValue,
            'requested_by' => $requestedBy ?? Auth::id(),
            'status' => 'pending_step_1',
            'notes' => $notes,
        ]);
    }

    /**
     * Thực hiện duyệt bước 1 hoặc bước 2
     */
    public static function approve(DualApproval $approval, int $approverId, ?string $notes = null): DualApproval
    {
        // Người tạo yêu cầu không được tự duyệt
        if ($approval->requested_by === $approverId) {
            throw new \InvalidArgumentException('Người lập phiếu không được phép tự phê duyệt giao dịch này.');
        }

        // Bước 1: Duyệt lần 1
        if ($approval->status === 'pending_step_1') {
            $approval->update([
                'first_approver_id' => $approverId,
                'first_approved_at' => now(),
                'status' => 'pending_step_2',
                'notes' => $notes ? ($approval->notes . "\n[Duyệt bước 1]: " . $notes) : $approval->notes,
            ]);

            AuditLogService::log(
                'dual_approval_step_1',
                $approval,
                null,
                ['first_approver_id' => $approverId],
                "Phê duyệt bước 1 cho yêu cầu #{$approval->id}",
                $approverId
            );

            return $approval;
        }

        // Bước 2: Duyệt lần 2 (Phải là người khác với người duyệt bước 1)
        if ($approval->status === 'pending_step_2') {
            if ($approval->first_approver_id === $approverId) {
                throw new \InvalidArgumentException('Người duyệt bước 2 phải là tài khoản độc lập khác với người đã duyệt bước 1.');
            }

            $approval->update([
                'second_approver_id' => $approverId,
                'second_approved_at' => now(),
                'status' => 'approved',
                'notes' => $notes ? ($approval->notes . "\n[Duyệt bước 2 hoàn tất]: " . $notes) : $approval->notes,
            ]);

            AuditLogService::log(
                'dual_approval_step_2_completed',
                $approval,
                null,
                ['second_approver_id' => $approverId],
                "Phê duyệt bước 2 hoàn tất cho yêu cầu #{$approval->id}",
                $approverId
            );

            return $approval;
        }

        return $approval;
    }

    /**
     * Từ chối yêu cầu phê duyệt
     */
    public static function reject(DualApproval $approval, int $rejectorId, string $reason): DualApproval
    {
        $approval->update([
            'status' => 'rejected',
            'notes' => $approval->notes . "\n[Từ chối bởi User #{$rejectorId}]: " . $reason,
        ]);

        AuditLogService::log(
            'dual_approval_rejected',
            $approval,
            null,
            ['rejected_by' => $rejectorId, 'reason' => $reason],
            "Từ chối yêu cầu duyệt #{$approval->id}: {$reason}",
            $rejectorId
        );

        return $approval;
    }
}
