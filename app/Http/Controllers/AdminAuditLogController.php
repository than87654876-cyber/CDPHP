<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DualApproval;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\DualApprovalService;
use App\Services\ManagerPinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuditLogController extends Controller
{
    /**
     * Hiển thị Nhật ký hoạt động (Audit Trail / Activity Log)
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->latest('created_at')->paginate(20)->withQueryString();
        $users = User::whereIn('role', ['superadmin', 'admin', 'manager', 'accountant', 'warehouse', 'staff'])->get();

        return view('admin.audit_logs', compact('logs', 'users'));
    }

    /**
     * API Ủy quyền qua mã PIN Quản lý trên máy POS (Supervisor PIN Override)
     */
    public function verifyManagerPin(Request $request): JsonResponse
    {
        $request->validate([
            'pin' => 'required|string|min:4|max:10',
            'action_type' => 'nullable|string|in:discount_override,order_cancellation,price_override',
            'notes' => 'nullable|string',
        ]);

        $manager = ManagerPinService::verifyPin($request->pin, Auth::user()?->branch_id);

        if (!$manager) {
            return response()->json([
                'success' => false,
                'message' => 'Mã PIN Quản lý không chính xác hoặc tài khoản không có quyền phê duyệt!',
            ], 403);
        }

        // Ghi nhận sự kiện ủy quyền tạm thời vào Audit Trail
        AuditLogService::log(
            'manager_pin_override',
            null,
            null,
            [
                'action_type' => $request->action_type ?? 'pos_action',
                'authorized_by' => $manager->id,
                'authorized_by_name' => $manager->fullname,
                'executed_by' => Auth::id(),
                'notes' => $request->notes,
            ],
            "Quản lý '{$manager->fullname}' đã ủy quyền thao tác '{$request->action_type}' cho nhân viên " . (Auth::user()?->fullname ?? 'Thu ngân')
        );

        return response()->json([
            'success' => true,
            'message' => "Ủy quyền thành công bởi Quản lý: {$manager->fullname}",
            'manager' => [
                'id' => $manager->id,
                'fullname' => $manager->fullname,
                'role' => $manager->role,
            ],
        ]);
    }

    /**
     * Danh sách yêu cầu phê duyệt 2 bước (Dual Approvals)
     */
    public function dualApprovalsIndex()
    {
        $pendingApprovals = DualApproval::with(['requester', 'firstApprover', 'secondApprover'])
            ->latest('created_at')
            ->paginate(15);

        return view('admin.dual_approvals', compact('pendingApprovals'));
    }

    /**
     * Phê duyệt bước 1 hoặc bước 2
     */
    public function processApproval(Request $request, $id)
    {
        $approval = DualApproval::findOrFail($id);

        $request->validate([
            'decision' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            if ($request->decision === 'approve') {
                DualApprovalService::approve($approval, Auth::id(), $request->notes);
                return redirect()->back()->with('success', "Đã phê duyệt thành công yêu cầu #{$approval->id}!");
            } else {
                DualApprovalService::reject($approval, Auth::id(), $request->notes ?? 'Từ chối bởi Quản lý');
                return redirect()->back()->with('success', "Đã từ chối yêu cầu phê duyệt #{$approval->id}!");
            }
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
