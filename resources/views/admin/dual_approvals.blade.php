@extends('layouts.admin')

@section('title', 'Phê duyệt 2 bước (Dual Approval) - FOODDAILY Admin')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-extrabold uppercase tracking-wider">
                    Quy Trình Kiểm Soát Rủi Ro
                </span>
                <span class="text-xs text-slate-400 font-bold">•</span>
                <span class="text-xs text-slate-500 font-semibold">Dual Approval Workflow</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Cơ Chế Phê Duyệt 2 Bước Độc Lập</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Các phiếu nhập giá trị lớn (>= 20 triệu), chênh lệch kiểm kê lớn hoặc hủy hàng bắt buộc qua 2 tài khoản duyệt</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_nhatky') }}" class="px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
                <i class="fas fa-fingerprint text-rose-500"></i> Xem Audit Trail
            </a>
            <a href="{{ route('quanly_vaitro') }}" class="px-4 py-2.5 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-extrabold shadow-lg shadow-rose-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-shield-halved"></i> Ma trận RBAC
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-exclamation text-rose-500 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Danh sách yêu cầu phê duyệt -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    <i class="fas fa-signature"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Danh Sách Yêu Cầu Chờ Duyệt</h3>
                    <p class="text-xs text-slate-400">Yêu cầu duyệt lần 1 và lần 2 phải từ hai cá nhân có thẩm quyền khác nhau</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-5">Mã Yêu Cầu</th>
                        <th class="py-4 px-4">Loại Nghiệp Vụ</th>
                        <th class="py-4 px-4 text-right">Giá Trị Giao Dịch</th>
                        <th class="py-4 px-4">Người Lập Phiếu</th>
                        <th class="py-4 px-4 text-center">Tiến Độ Duyệt 2 Bước</th>
                        <th class="py-4 px-5 text-center">Thao Tác Duyệt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($pendingApprovals as $approval)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-5 font-bold text-slate-900 font-mono">
                            #APPROVAL-{{ str_pad($approval->id, 4, '0', STR_PAD_LEFT) }}
                            <span class="block text-[10px] text-slate-400 font-normal font-sans">{{ $approval->created_at->format('d/m/Y H:i') }}</span>
                        </td>

                        <td class="py-4 px-4 font-semibold text-slate-800">
                            {{ $approval->action_type }}
                            @if($approval->notes)
                                <p class="text-[11px] text-slate-500 font-normal mt-0.5">{{ $approval->notes }}</p>
                            @endif
                        </td>

                        <td class="py-4 px-4 text-right font-extrabold text-rose-600">
                            {{ number_format($approval->amount_value, 0, ',', '.') }}đ
                        </td>

                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-900 block">{{ $approval->requester->fullname ?? 'Nhân sự' }}</span>
                            <span class="text-[10px] text-slate-400 uppercase font-extrabold">{{ $approval->requester->role ?? '' }}</span>
                        </td>

                        <td class="py-4 px-4 text-center">
                            @if($approval->status === 'pending_step_1')
                                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-extrabold">
                                    ⏳ Chờ duyệt Bước 1
                                </span>
                            @elseif($approval->status === 'pending_step_2')
                                <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-extrabold">
                                    ✓ Đã duyệt B1 ({{ $approval->firstApprover->fullname ?? '' }}) • Chờ duyệt Bước 2
                                </span>
                            @elseif($approval->status === 'approved')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold">
                                    ✓ Hoàn tất duyệt 2 bước
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-extrabold">
                                    ✕ Đã từ chối
                                </span>
                            @endif
                        </td>

                        <td class="py-4 px-5 text-center">
                            @if(in_array($approval->status, ['pending_step_1', 'pending_step_2']))
                                <form action="{{ route('quanly_pheduyet.xuly', $approval->id) }}" method="POST" class="flex items-center justify-center gap-2">
                                    @csrf
                                    <button type="submit" name="decision" value="approve" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[11px] shadow-2xs transition-colors flex items-center gap-1">
                                        <i class="fas fa-check"></i> Duyệt
                                    </button>
                                    <button type="submit" name="decision" value="reject" class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-700 font-extrabold text-[11px] transition-colors flex items-center gap-1" onclick="return confirm('Bạn có chắc chắn muốn từ chối yêu cầu này?')">
                                        <i class="fas fa-xmark"></i> Từ chối
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-400 text-xs italic">Đã kết thúc</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="fas fa-clipboard-check text-4xl text-slate-300 mb-2 block"></i>
                            Hiện tại không có yêu cầu nào cần duyệt 2 bước.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
