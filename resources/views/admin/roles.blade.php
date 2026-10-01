@extends('layouts.admin')

@section('title', 'Quản lý Vai trò & Phân quyền (RBAC) - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-md bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-extrabold uppercase tracking-wider">
                    Bảo mật RBAC
                </span>
                <span class="text-xs text-slate-400 font-bold">•</span>
                <span class="text-xs text-slate-500 font-semibold">Role-Based Access Control</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Vai Trò & Ma Trận Phân Quyền</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Tạo các vai trò công việc và tích chọn từng quyền hạn chi tiết cho nhân sự hệ thống</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_nhanvien') }}" class="px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
                <i class="fas fa-users text-slate-400"></i> Danh sách nhân viên
            </a>
            <a href="{{ route('vaitro_them') }}" class="px-5 py-2.5 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-extrabold shadow-lg shadow-rose-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> Thêm vai trò mới
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

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng số Vai trò</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $roles->count() }}</h3>
                <p class="text-[11px] text-purple-600 font-semibold mt-1 flex items-center gap-1">
                    <i class="fas fa-shield-halved"></i> Đã cấu hình RBAC
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                <i class="fas fa-user-gear"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Quyền hạn hệ thống</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalPermissions }}</h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                    <i class="fas fa-cubes"></i> {{ count($modules) }} Phân hệ nghiệp vụ
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                <i class="fas fa-key"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nhân sự gán quyền</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">
                    {{ $roles->sum('users_count') }}
                </h3>
                <p class="text-[11px] text-blue-600 font-semibold mt-1 flex items-center gap-1">
                    <i class="fas fa-users-gear"></i> Nhân sự hoạt động
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                <i class="fas fa-id-badge"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kiến trúc bảo mật</p>
                <h3 class="text-lg font-black text-slate-900 mt-1">Spatie Dynamic</h3>
                <p class="text-[11px] text-amber-600 font-semibold mt-1 flex items-center gap-1">
                    <i class="fas fa-lock"></i> Chuẩn công nghiệp
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                <i class="fas fa-fingerprint"></i>
            </div>
        </div>
    </div>

    <!-- Roles Table Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-user-shield text-[#ee4d2d]"></i> Danh sách các Vai trò quản trị & nghiệp vụ
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Mỗi vai trò bao gồm các quyền truy cập các màn hình và hành động cụ thể</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="py-4 px-6">Tên Vai trò</th>
                        <th class="py-4 px-6">Mã định danh</th>
                        <th class="py-4 px-6 text-center">Thành viên</th>
                        <th class="py-4 px-6">Số lượng Quyền hạn</th>
                        <th class="py-4 px-6">Phạm vi quyền chính</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roles as $role)
                        @php
                            $roleBadges = [
                                'superadmin' => ['label' => 'Quản trị viên tối cao (Super Admin)', 'bg' => 'bg-purple-100 text-purple-800 border-purple-200', 'icon' => '👑'],
                                'admin' => ['label' => 'Quản trị viên / Quản lý quán', 'bg' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => '🛡️'],
                                'staff' => ['label' => 'Nhân viên CSKH / Thu ngân', 'bg' => 'bg-blue-100 text-blue-800 border-blue-200', 'icon' => '🎧'],
                                'kitchen' => ['label' => 'Nhân viên Bếp (Chef)', 'bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'icon' => '🍳'],
                                'shipper' => ['label' => 'Nhân viên Giao hàng (Shipper)', 'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'icon' => '🛵'],
                            ];
                            $badgeInfo = $roleBadges[$role->name] ?? ['label' => 'Vai trò tùy chỉnh', 'bg' => 'bg-slate-100 text-slate-800 border-slate-200', 'icon' => '⚙️'];
                            $permCount = $role->permissions->count();
                            $permPercent = $totalPermissions > 0 ? round(($permCount / $totalPermissions) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">{{ $badgeInfo['icon'] }}</span>
                                    <div>
                                        <p class="font-extrabold text-slate-900 text-sm">{{ $badgeInfo['label'] }}</p>
                                        <p class="text-[11px] text-slate-400 font-semibold">Tạo ngày: {{ $role->created_at ? $role->created_at->format('d/m/Y') : 'Mặc định' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <code class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-[11px] font-bold">
                                    {{ $role->name }}
                                </code>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black {{ $role->users_count > 0 ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="fas fa-user text-[10px] mr-1.5"></i> {{ $role->users_count }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $role->name === 'superadmin' ? 'bg-purple-600' : 'bg-[#ee4d2d]' }}" style="width: {{ $permPercent }}%"></div>
                                    </div>
                                    <span class="font-extrabold text-slate-800 text-xs">
                                        {{ $permCount }} / {{ $totalPermissions }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-wrap gap-1 max-w-md">
                                    @if($role->name === 'superadmin')
                                        <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200 text-[10px] font-bold">
                                            ✨ Toàn quyền toàn bộ hệ thống
                                        </span>
                                    @else
                                        @foreach($role->permissions->take(4) as $p)
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-semibold">
                                                {{ $p->name }}
                                            </span>
                                        @endforeach
                                        @if($permCount > 4)
                                            <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-400 text-[10px] font-bold">
                                                +{{ $permCount - 4 }} quyền
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('vaitro_chinhsua', $role->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-[#ee4d2d] text-slate-700 hover:text-white font-bold text-xs transition-colors flex items-center gap-1.5">
                                        <i class="fas fa-sliders text-[11px]"></i> Phân quyền
                                    </a>

                                    @if(!in_array($role->name, ['superadmin', 'admin', 'staff', 'kitchen', 'shipper']))
                                        <form action="{{ route('vaitro_xoa', $role->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vai trò [{{ $role->name }}] không?');" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-xl hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition-colors" title="Xóa vai trò">
                                                <i class="fas fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fas fa-shield-slash text-3xl mb-2"></i>
                                <p>Chưa có vai trò nào được cấu hình</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Instruction Help Box -->
    <div class="bg-gradient-to-r from-purple-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-3xl space-y-3">
            <span class="px-3 py-1 rounded-full bg-white/10 text-purple-200 text-[11px] font-extrabold uppercase tracking-wider backdrop-blur-xs">
                💡 Hướng dẫn Vận hành RBAC
            </span>
            <h2 class="text-xl font-black tracking-tight text-white">Cách phân công và gán vai trò tối ưu nhất</h2>
            <p class="text-xs text-purple-100/80 leading-relaxed">
                Hệ thống FOODDAILY đã tách biệt rõ rệt từng phân hệ. Bạn có thể tạo thêm các vai trò đặc thù (ví dụ: <code class="bg-white/10 px-2 py-0.5 rounded text-amber-300">parttime_chef</code>, <code class="bg-white/10 px-2 py-0.5 rounded text-amber-300">marketing_lead</code>) và chỉ tích chọn những quyền liên quan. Sau khi lưu, hãy vào mục <strong>Tài khoản Nhân viên</strong> để gán vai trò này cho nhân sự tương ứng.
            </p>
        </div>
        <div class="absolute -right-8 -bottom-8 opacity-10 text-9xl">
            <i class="fas fa-lock"></i>
        </div>
    </div>

</div>
@endsection
