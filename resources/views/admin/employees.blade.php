@extends('layouts.admin')

@section('title', 'Quản lý Tài khoản Nhân viên - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tài Khoản Nhân Viên & Phân Quyền</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Quản lý tài khoản truy cập hệ thống của nhân viên và ban quản trị</p>
        </div>

        <a href="{{ route('nhanvien_them') }}" class="px-5 py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-extrabold shadow-lg shadow-rose-500/20 transition-all flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i> Thêm nhân viên mới
        </a>
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

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-id-card text-[#ee4d2d]"></i> Danh sách tài khoản nhân viên hệ thống
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="py-4 px-6">Mã NV</th>
                        <th class="py-4 px-6">Họ và Tên</th>
                        <th class="py-4 px-6">Chức vụ / Ghi chú</th>
                        <th class="py-4 px-6">Email đăng nhập</th>
                        <th class="py-4 px-6">Quyền hạn</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-bold text-slate-400">NV-{{ sprintf('%03d', $employee->id) }}</td>
                            <td class="py-4 px-6 font-extrabold text-slate-900">
                                {{ $employee->fullname ?? $employee->name }}
                            </td>
                            <td class="py-4 px-6 text-slate-700 font-medium">
                                {{ $employee->notes ?? 'Chưa ghi chú chức vụ' }}
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $employee->email }}
                            </td>
                            <td class="py-4 px-6">
                                @if($employee->role === 'superadmin')
                                    <span class="px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-[10px] font-black border border-purple-200 shadow-xs"><i class="fas fa-crown text-amber-500 mr-1"></i> Quản trị viên tối cao</span>
                                @elseif($employee->role === 'admin')
                                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-[#ee4d2d] text-[10px] font-black border border-rose-200">Quản trị viên (Admin)</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200">Nhân viên (Staff)</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('nhanvien_xem', $employee->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="Xem chi tiết">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('nhanvien_chinhsua', $employee->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition-colors" title="Chỉnh sửa & phân quyền">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                @if($employee->role !== 'superadmin')
                                    <form action="{{ route('nhanvien_xoa', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản nhân sự này?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition-colors cursor-pointer" title="Xóa tài khoản">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-50 text-slate-300 cursor-not-allowed" title="Không thể xóa Quản trị viên tối cao">
                                        <i class="fas fa-shield-halved text-xs"></i>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 font-medium">Chưa có tài khoản nhân viên nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
