@extends('layouts.admin')

@section('title', 'Thêm Nhân viên mới - FOODDAILY Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Thêm Nhân Viên Mới</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Cấp tài khoản phân quyền cho nhân sự vận hành hoặc quản trị</p>
        </div>
        <a href="{{ route('quanly_nhanvien') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Error Alerts -->
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-2 font-medium"><i class="fas fa-circle-exclamation text-rose-500"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-base font-bold shadow-xs">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Biểu mẫu khởi tạo tài khoản nhân sự</h3>
                <p class="text-[11px] text-slate-400 font-medium">Nhập thông tin cá nhân và thiết lập quyền truy cập cho nhân viên</p>
            </div>
        </div>

        <form action="{{ route('nhanvien_them.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Họ và tên -->
                <div class="space-y-2">
                    <label for="fullname" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Họ và Tên nhân viên <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="text" id="fullname" name="fullname" value="{{ old('fullname') }}" required placeholder="Ví dụ: Nguyễn Văn An" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Số điện thoại -->
                <div class="space-y-2">
                    <label for="phone" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Số điện thoại <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="Ví dụ: 0912345678" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Địa chỉ Email đăng nhập <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="name@fooddaily.com" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Mật khẩu -->
                <div class="space-y-2">
                    <label for="password" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Mật khẩu khởi tạo <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="password" id="password" name="password" required placeholder="Tối thiểu 6 ký tự" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Quyền hạn -->
                <div class="space-y-2">
                    <label for="role" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Quyền hạn hệ thống <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <select id="role" name="role" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>👨‍🍳 Nhân viên vận hành (Staff - Bàn làm việc & Bếp)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>⭐ Quản trị viên (Admin - Quản lý thực đơn, đơn hàng, khách hàng)</option>
                        <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>👑 Quản trị viên tối cao (Super Admin - Toàn quyền & Phân quyền)</option>
                    </select>
                </div>

                <!-- Trạng thái -->
                <div class="space-y-2">
                    <label for="status" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Trạng thái làm việc <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <select id="status" name="status" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                        <option value="1" {{ old('status') === '1' ? 'selected' : '' }}>✔ Đang hoạt động (Active)</option>
                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>✕ Tạm đình chỉ (Suspended)</option>
                    </select>
                </div>

                <!-- Chức vụ / Ghi chú -->
                <div class="space-y-2 sm:col-span-2">
                    <label for="notes" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Chức vụ cụ thể / Ghi chú công việc
                    </label>
                    <input type="text" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Ví dụ: Bếp chính, Thu ngân ca sáng, Điều phối shipper..." class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-medium text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('quanly_nhanvien') }}" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-8 py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer">
                    <i class="fas fa-save mr-1.5"></i> Tạo tài khoản
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
