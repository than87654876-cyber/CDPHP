@extends('layouts.admin')

@section('title', 'Chi tiết Nhân viên - FOODDAILY Admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Hồ Sơ Nhân Viên</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold border border-slate-200">
                    NV-{{ sprintf('%03d', $employee->id) }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Thông tin tài khoản nội bộ và quyền hạn truy cập</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_nhanvien') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <a href="{{ route('nhanvien_chinhsua', $employee->id) }}" class="px-4 py-2.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-pen-to-square"></i> Chỉnh sửa nhân viên
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Staff Profile Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-6 text-center">
            <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-slate-800 to-slate-600 text-white flex items-center justify-center text-2xl font-black mx-auto shadow-md">
                {{ mb_substr($employee->fullname, 0, 1) }}
            </div>
            
            <div class="space-y-1">
                <h3 class="font-extrabold text-slate-900 text-base">{{ $employee->fullname }}</h3>
                <p class="text-xs text-slate-400 font-semibold">NV-{{ sprintf('%03d', $employee->id) }}</p>
                <div class="pt-2">
                    @if($employee->role === 'admin')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-[#ee4d2d] text-xs font-black border border-rose-200">
                            ★ Quản trị viên (Admin)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-black border border-amber-200">
                            👨‍🍳 Nhân viên Vận hành (Staff)
                        </span>
                    @endif
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase">Trạng thái công việc</p>
                <div class="mt-1">
                    @if($employee->status)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Đang làm việc
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-extrabold border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span> Đã khóa tài khoản
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Staff Detail Info Card -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Thông tin nhân sự chi tiết</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Họ và tên nhân viên</p>
                    <p class="text-sm font-black text-slate-900 mt-1">{{ $employee->fullname }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Số điện thoại</p>
                    <p class="text-sm font-black text-slate-800 mt-1">{{ $employee->phone ?? 'Chưa cập nhật' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Địa chỉ Email đăng nhập</p>
                    <p class="text-sm font-extrabold text-slate-800 mt-1">{{ $employee->email }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Ghi chú nhân sự</p>
                    <p class="text-xs font-medium text-slate-700 mt-1 leading-relaxed">{{ $employee->notes ?? 'Không có ghi chú' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Ngày tạo tài khoản</p>
                    <p class="text-xs font-semibold text-slate-600 mt-1">{{ $employee->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
