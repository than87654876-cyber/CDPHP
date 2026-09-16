@extends('layouts.admin')

@section('title', 'Quản lý Khách vãng lai - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Quản Lý Khách Vãng Lai (Guests)</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-extrabold border border-slate-200">
                    {{ count($guests) }} khách
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Danh sách thông tin các phiên mua hàng nhanh không qua đăng ký tài khoản</p>
        </div>
    </div>

    <!-- Alert Flash Notifications -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs text-xs font-semibold">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-lg leading-none">&times;</button>
        </div>
    @endif

    <!-- MAIN TABLE CARD -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <!-- Card Filter Header -->
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-sm font-extrabold text-slate-900">Danh sách phiên mua hàng của khách vãng lai</h3>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs pointer-events-none">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="guestSearch" placeholder="Tìm theo tên, SĐT, email..." onkeyup="filterGuestsTable()" class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:border-[#ee4d2d] outline-none transition-all">
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="guestTable">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="py-4 px-6 w-16 text-center">STT</th>
                        <th class="py-4 px-6">Mã Guest</th>
                        <th class="py-4 px-6">Tên khách hàng</th>
                        <th class="py-4 px-6">Số điện thoại</th>
                        <th class="py-4 px-6">Địa chỉ Email</th>
                        <th class="py-4 px-6 text-center">Đơn đã đặt</th>
                        <th class="py-4 px-6">Ngày phát sinh</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($guests as $index => $guest)
                        <tr class="guest-row hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-center font-bold text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-extrabold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 font-mono">
                                    GST-{{ sprintf('%03d', $guest->id) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-extrabold flex items-center justify-center text-xs">
                                        {{ mb_substr($guest->fullname, 0, 1) }}
                                    </div>
                                    <p class="font-extrabold text-slate-900 text-xs">{{ $guest->fullname }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-slate-700 font-bold">
                                {{ $guest->phone ?? 'Chưa cập nhật' }}
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-medium">
                                {{ $guest->email }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="px-3 py-1 rounded-full bg-rose-50 text-[#ee4d2d] font-black text-xs border border-rose-100">
                                    {{ $guest->orders_count ?? $guest->orders()->count() }} đơn
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-medium">
                                <div class="flex items-center gap-1.5 text-xs text-slate-600">
                                    <i class="far fa-clock text-slate-400 text-[11px]"></i>
                                    <span>{{ $guest->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('quanly_donhang') }}?search={{ $guest->phone ?? $guest->email }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-900 text-slate-700 hover:text-white font-extrabold text-xs transition-all shadow-2xs">
                                    <i class="fas fa-receipt text-xs"></i>
                                    <span>Xem các đơn</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h4 class="font-extrabold text-slate-800 text-sm">Chưa có khách vãng lai nào</h4>
                                    <p class="text-xs text-slate-400">Các đơn mua hàng nhanh không đăng nhập sẽ xuất hiện tại đây.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterGuestsTable() {
        const input = document.getElementById('guestSearch');
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('.guest-row');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
