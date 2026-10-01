@extends('layouts.admin')

@section('title', ($isEdit ? 'Chỉnh sửa Vai trò: ' . $role->name : 'Tạo mới Vai trò') . ' - FOODDAILY Admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- Breadcrumb & Top Bar -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                <a href="{{ route('quanly_vaitro') }}" class="hover:text-slate-600 transition-colors">Vai trò & Phân quyền</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-slate-700">{{ $isEdit ? 'Chỉnh sửa vai trò' : 'Tạo vai trò mới' }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">
                {{ $isEdit ? 'Thiết Lập Quyền Hạn Cho: ' . $role->name : 'Tạo Vai Trò & Phân Quyền Mới' }}
            </h1>
        </div>

        <a href="{{ route('quanly_vaitro') }}" class="px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Error Alerts -->
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-semibold shadow-xs">
            <div class="flex items-center gap-2 mb-2 font-bold text-rose-800">
                <i class="fas fa-circle-exclamation text-rose-500"></i> Có lỗi xảy ra, vui lòng kiểm tra lại:
            </div>
            <ul class="list-disc list-inside space-y-1 text-[11px]">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $isEdit ? route('vaitro_chinhsua.post', $role->id) : route('vaitro_them.post') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Card Thông tin vai trò -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-id-card-clip text-[#ee4d2d]"></i> 1. Thông tin cơ bản của Vai trò
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Đặt mã định danh đại diện cho nhóm chức danh này</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Mã định danh Vai trò <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" 
                        placeholder="Ví dụ: thu_ngan, parttime_bep, marketing..."
                        {{ $isEdit && $role->name === 'superadmin' ? 'readonly' : '' }}
                        required
                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-[#ee4d2d]/20 focus:border-[#ee4d2d] transition-all {{ $isEdit && $role->name === 'superadmin' ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-slate-50/50' }}">
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        * Viết liền không dấu, có thể dùng dấu gạch dưới ( _ ) hoặc gạch nối ( - ).
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Loại vai trò / Ghi chú gợi ý
                    </label>
                    <div class="p-3 bg-purple-50/60 rounded-2xl border border-purple-100 text-xs text-purple-900 flex items-start gap-3">
                        <i class="fas fa-info-circle text-purple-600 mt-0.5"></i>
                        <div class="text-[11px] leading-relaxed">
                            @if($role->name === 'superadmin')
                                <strong>Quản trị viên tối cao:</strong> Tự động sở hữu toàn bộ quyền hạn của hệ thống, không thể thu hồi.
                            @elseif($role->name === 'admin')
                                <strong>Quản lý cửa hàng:</strong> Thường được giao quyền quản lý đơn hàng, thực đơn, khuyến mãi.
                            @elseif($role->name === 'kitchen')
                                <strong>Bếp:</strong> Nên chỉ gán quyền trong nhóm "Nghiệp vụ Nhà bếp".
                            @elseif($role->name === 'shipper')
                                <strong>Giao hàng:</strong> Nên chỉ gán quyền trong nhóm "Nghiệp vụ Giao hàng".
                            @else
                                <strong>Vai trò tùy chỉnh:</strong> Hãy tích chọn các quyền tương ứng bên dưới cho nhân sự này.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Ma trận Quyền hạn (Permissions) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-shield-halved text-[#ee4d2d]"></i> 2. Ma trận Danh sách Quyền hạn (Permissions)
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Đánh dấu vào các chức năng mà nhân viên có vai trò này được phép truy cập</p>
                </div>

                @if($role->name !== 'superadmin')
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleSelectAll(true)" class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-xs transition-colors">
                        <i class="fas fa-check-double text-[10px]"></i> Chọn tất cả
                    </button>
                    <button type="button" onclick="toggleSelectAll(false)" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition-colors">
                        <i class="fas fa-xmark text-[10px]"></i> Bỏ chọn tất cả
                    </button>
                </div>
                @endif
            </div>

            @if($role->name === 'superadmin')
                <div class="p-6 rounded-2xl bg-purple-50 border border-purple-200 text-purple-900 text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xl mx-auto">
                        👑
                    </div>
                    <h4 class="font-extrabold text-sm text-purple-950">Quản trị viên tối cao luôn sở hữu toàn bộ quyền hạn</h4>
                    <p class="text-xs text-purple-700/80 max-w-lg mx-auto">
                        Mọi quyền hạn mới thêm vào hệ thống sẽ tự động được cấp cho Quản trị viên tối cao nhằm đảm bảo không bao giờ bị khóa ngoài hệ thống.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($modules as $moduleTitle => $module)
                        <div class="rounded-2xl border border-slate-200/90 p-5 bg-slate-50/40 hover:bg-slate-50 transition-colors flex flex-col justify-between module-card">
                            <div>
                                <!-- Header Phân hệ -->
                                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200/60">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center text-xs shadow-2xs">
                                            <i class="{{ $module['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-extrabold text-xs text-slate-900">{{ $moduleTitle }}</h4>
                                            <span class="text-[10px] text-slate-400 font-medium">{{ count($module['items']) }} quyền</span>
                                        </div>
                                    </div>

                                    <button type="button" onclick="toggleModuleCheckboxes(this)" class="text-[11px] font-bold text-slate-500 hover:text-[#ee4d2d] transition-colors">
                                        Chọn nhóm
                                    </button>
                                </div>

                                <!-- Danh sách checkbox quyền trong Module -->
                                <div class="space-y-2.5">
                                    @foreach($module['items'] as $permName => $permDesc)
                                        @php
                                            $checked = in_array($permName, old('permissions', $assignedPermissions));
                                        @endphp
                                        <label class="flex items-start gap-3 p-2 rounded-xl hover:bg-white transition-colors cursor-pointer group">
                                            <input type="checkbox" name="permissions[]" value="{{ $permName }}" 
                                                {{ $checked ? 'checked' : '' }}
                                                class="perm-checkbox mt-0.5 w-4 h-4 rounded text-[#ee4d2d] focus:ring-[#ee4d2d]/30 border-slate-300">
                                            <div class="text-left">
                                                <p class="text-xs font-bold text-slate-800 group-hover:text-[#ee4d2d] transition-colors">
                                                    {{ $permDesc }}
                                                </p>
                                                <code class="text-[10px] text-slate-400 font-mono">{{ $permName }}</code>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Nút Submit Action -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('quanly_vaitro') }}" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                Hủy bỏ
            </a>
            <button type="submit" class="px-8 py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center gap-2">
                <i class="fas fa-floppy-disk"></i> {{ $isEdit ? 'Cập nhật phân quyền' : 'Tạo vai trò và Lưu quyền' }}
            </button>
        </div>

    </form>
</div>

<script>
function toggleSelectAll(select) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.checked = select;
    });
}

function toggleModuleCheckboxes(btn) {
    const card = btn.closest('.module-card');
    const checkboxes = card.querySelectorAll('.perm-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
}
</script>
@endsection
