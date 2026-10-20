@extends('layouts.admin')

@section('title', 'Nhật ký Hoạt động (Audit Trail) - FOODDAILY Admin')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-extrabold uppercase tracking-wider">
                    An Toàn & Chống Gian Lận
                </span>
                <span class="text-xs text-slate-400 font-bold">•</span>
                <span class="text-xs text-slate-500 font-semibold">Audit Trail & Activity Log</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Nhật Ký Hoạt Động Hệ Thống</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Theo dõi chi tiết ai sửa tồn kho, đổi giá, chiết khấu, hủy đơn kèm thời gian và IP</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_vaitro') }}" class="px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
                <i class="fas fa-shield-halved text-purple-600"></i> Ma trận RBAC
            </a>
            <a href="{{ route('quanly_pheduyet') }}" class="px-4 py-2.5 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-extrabold border border-indigo-200 transition-colors flex items-center gap-2">
                <i class="fas fa-signature"></i> Phê duyệt 2 bước
            </a>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng số bản ghi Audit</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($logs->total()) }}</h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                    <i class="fas fa-circle-check text-[9px]"></i> Ghi nhận tự động 24/7
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold shadow-2xs">
                <i class="fas fa-fingerprint"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sửa tồn kho</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">
                    {{ \App\Models\AuditLog::where('action', 'inventory_adjusted')->count() }}
                </h3>
                <p class="text-[11px] text-slate-400 font-medium mt-1">Lượt cân bằng kho</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold shadow-2xs">
                <i class="fas fa-boxes-stacked"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Hủy hóa đơn & Hoàn tiền</p>
                <h3 class="text-2xl font-black text-red-600 mt-1">
                    {{ \App\Models\AuditLog::where('action', 'order_cancelled')->count() }}
                </h3>
                <p class="text-[11px] text-slate-400 font-medium mt-1">Đã kiểm soát gian lận</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl font-bold shadow-2xs">
                <i class="fas fa-ban"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ủy quyền mã PIN POS</p>
                <h3 class="text-2xl font-black text-purple-600 mt-1">
                    {{ \App\Models\AuditLog::where('action', 'manager_pin_override')->count() }}
                </h3>
                <p class="text-[11px] text-purple-600 font-medium mt-1">Giám sát Quản lý tại quầy</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold shadow-2xs">
                <i class="fas fa-key"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('quanly_nhatky') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center">
            <div>
                <select name="action" class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 outline-none">
                    <option value="">-- Tất cả hành động --</option>
                    <option value="inventory_adjusted" {{ request('action') === 'inventory_adjusted' ? 'selected' : '' }}>Sửa tồn kho (Cân bằng)</option>
                    <option value="price_changed" {{ request('action') === 'price_changed' ? 'selected' : '' }}>Đổi giá bán / Giá vốn</option>
                    <option value="discount_applied" {{ request('action') === 'discount_applied' ? 'selected' : '' }}>Áp dụng chiết khấu</option>
                    <option value="order_cancelled" {{ request('action') === 'order_cancelled' ? 'selected' : '' }}>Hủy đơn hàng</option>
                    <option value="manager_pin_override" {{ request('action') === 'manager_pin_override' ? 'selected' : '' }}>Ủy quyền qua mã PIN</option>
                </select>
            </div>

            <div>
                <select name="user_id" class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 outline-none">
                    <option value="">-- Tất cả nhân sự --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->fullname }} ({{ $user->role }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 outline-none" title="Từ ngày">
            </div>

            <div>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 outline-none" title="Đến ngày">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-xs transition-colors flex items-center justify-center gap-1.5">
                    <i class="fas fa-filter"></i> Lọc
                </button>
                @if(request()->anyFilled(['action', 'user_id', 'from_date', 'to_date']))
                    <a href="{{ route('quanly_nhatky') }}" class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition-colors" title="Bỏ lọc">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Audit Logs -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-5">Thời Gian</th>
                        <th class="py-4 px-4">Nhân Sự Thực Hiện</th>
                        <th class="py-4 px-4">Hành Động</th>
                        <th class="py-4 px-5">Chi Tiết / Lý Do</th>
                        <th class="py-4 px-4">Dữ Liệu Trước -> Sau</th>
                        <th class="py-4 px-4 text-right">Địa Chỉ IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- Thời gian -->
                        <td class="py-4 px-5 whitespace-nowrap">
                            <span class="font-bold text-slate-800 block">{{ $log->created_at->format('H:i:s') }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $log->created_at->format('d/m/Y') }}</span>
                        </td>

                        <!-- Người thực hiện -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            @if($log->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[10px] font-extrabold">
                                        {{ strtoupper(substr($log->user->fullname, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 leading-tight">{{ $log->user->fullname }}</p>
                                        <span class="text-[10px] text-purple-600 font-extrabold uppercase">{{ $log->user->role }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-slate-400 italic">Hệ thống tự động</span>
                            @endif
                        </td>

                        <!-- Loại hành động -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            @php
                                $badgeClasses = match($log->action) {
                                    'inventory_adjusted' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'order_cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'discount_applied' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'manager_pin_override' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'price_changed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                                $actionName = match($log->action) {
                                    'inventory_adjusted' => 'Sửa tồn kho',
                                    'order_cancelled' => 'Hủy đơn hàng',
                                    'discount_applied' => 'Chiết khấu POS',
                                    'manager_pin_override' => 'Mã PIN Quản lý',
                                    'price_changed' => 'Đổi giá bán/vốn',
                                    default => $log->action,
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-lg border text-[11px] font-extrabold {{ $badgeClasses }}">
                                {{ $actionName }}
                            </span>
                        </td>

                        <!-- Nội dung chi tiết -->
                        <td class="py-4 px-5">
                            <p class="font-medium text-slate-700 leading-relaxed">{{ $log->notes ?: 'Không có ghi chú' }}</p>
                        </td>

                        <!-- Giá trị cũ -> mới -->
                        <td class="py-4 px-4">
                            @if($log->old_values || $log->new_values)
                                <div class="text-[10px] font-mono space-y-0.5">
                                    @if($log->old_values)
                                        <p class="text-rose-600 font-semibold">- Trước: {{ json_encode($log->old_values, JSON_UNESCAPED_UNICODE) }}</p>
                                    @endif
                                    @if($log->new_values)
                                        <p class="text-emerald-600 font-semibold">+ Sau: {{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}</p>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-400 italic text-[11px]">-</span>
                            @endif
                        </td>

                        <!-- IP Address -->
                        <td class="py-4 px-4 text-right whitespace-nowrap">
                            <span class="font-mono text-[11px] text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-200/60">
                                {{ $log->ip_address }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="fas fa-shield-cat text-4xl text-slate-300 mb-2 block"></i>
                            Chưa có nhật ký hoạt động nào phù hợp với bộ lọc tìm kiếm.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
