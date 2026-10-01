@extends('layouts.admin')

@section('title', 'Quản Lý Nhà Cung Cấp & Nguồn Hàng - FOODDAILY Admin')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-black">
                    <i class="fas fa-handshake"></i> Đối Tác Cung Ứng
                </span>
                <span>•</span>
                <span>Danh bạ nguồn hàng & đơn vị cung cấp</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fas fa-truck-field text-amber-500 text-3xl"></i> Nhà Cung Cấp (Nguồn Nhập Hàng)
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Danh sách các đối tác cung ứng thịt tươi, rau củ quả, đồ uống đóng lon để nhân viên và admin nắm rõ nguồn nhập khi cần lấy hàng.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('nhacungcap_them') }}" class="px-4 py-2.5 rounded-2xl bg-[#ee4d2d] hover:bg-[#d73211] text-white text-xs font-bold transition-all shadow-md shadow-rose-500/20 flex items-center gap-2">
                <i class="fas fa-plus"></i> Thêm Nhà Cung Cấp Mới
            </a>
            <a href="{{ route('quanly_kho') }}" class="px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold transition-all shadow-2xs flex items-center gap-2">
                <i class="fas fa-boxes-stacked text-blue-600"></i> Xem Hàng Tồn Kho
            </a>
            <a href="{{ route('quanly_nhaphang') }}" class="px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold transition-all shadow-2xs flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-emerald-600"></i> Lịch Sử Nhập Hàng
            </a>
        </div>
    </div>

    <!-- Alert thông báo -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 cursor-pointer">&times;</button>
        </div>
    @endif

    <!-- Tìm kiếm NCC -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('quanly_nhacungcap') }}" class="flex items-center gap-3">
            <div class="relative flex-grow">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên nhà cung cấp, mã NCC, số điện thoại, người liên hệ..." 
                       class="w-full pl-9 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                <i class="fas fa-filter"></i> Tìm Kiếm
            </button>
            @if(request()->filled('q'))
                <a href="{{ route('quanly_nhacungcap') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs font-bold transition-colors">
                    Xóa lọc
                </a>
            @endif
        </form>
    </div>

    <!-- Danh Sách Nhà Cung Cấp -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse($suppliers as $supplier)
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 flex flex-col justify-between hover:border-amber-300 hover:shadow-md transition-all group">
            
            <div class="space-y-4">
                <!-- Header Card: Mã & Trạng thái -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-400 text-white flex items-center justify-center text-xl font-bold shadow-sm shadow-amber-500/30">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 uppercase tracking-wider">
                                {{ $supplier->supplier_code }}
                            </span>
                            <h3 class="text-base font-black text-slate-900 mt-1 leading-snug group-hover:text-amber-600 transition-colors">
                                {{ $supplier->name }}
                            </h3>
                        </div>
                    </div>

                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $supplier->status === 'active' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                        {{ $supplier->status === 'active' ? 'Đang hợp tác' : 'Tạm ngưng' }}
                    </span>
                </div>

                <!-- Ngành hàng cung ứng -->
                @if($supplier->supply_category)
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold text-slate-400">Cung cấp:</span>
                    <span class="px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-extrabold border border-blue-200/60">
                        {{ $supplier->supply_category }}
                    </span>
                </div>
                @endif

                <!-- Thông tin liên hệ nhanh để gọi điện đặt hàng -->
                <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Hotline / Đặt hàng:</span>
                        <a href="tel:{{ $supplier->phone }}" class="text-[#ee4d2d] font-black text-sm hover:underline flex items-center gap-1.5">
                            <i class="fas fa-phone-volume"></i> {{ $supplier->phone }}
                        </a>
                    </div>

                    @if($supplier->contact_person)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Người phụ trách:</span>
                        <span class="text-slate-800 font-bold">{{ $supplier->contact_person }}</span>
                    </div>
                    @endif

                    @if($supplier->email)
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Email:</span>
                        <a href="mailto:{{ $supplier->email }}" class="text-slate-700 hover:text-blue-600 font-semibold">{{ $supplier->email }}</a>
                    </div>
                    @endif

                    @if($supplier->address)
                    <div class="pt-1 border-t border-slate-200/60 flex items-start gap-2 text-[11px] text-slate-500">
                        <i class="fas fa-location-dot text-slate-400 mt-0.5 shrink-0"></i>
                        <span>{{ $supplier->address }}</span>
                    </div>
                    @endif
                </div>

                <!-- Thống kê nhanh: Số mặt hàng & Số phiếu nhập -->
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60 text-center">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Mặt hàng cung ứng</span>
                        <span class="text-base font-black text-slate-800 mt-0.5 block">{{ $supplier->inventory_items_count }} loại</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60 text-center">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Đợt nhập kho</span>
                        <span class="text-base font-black text-emerald-600 mt-0.5 block">{{ $supplier->goods_receipts_count }} phiếu</span>
                    </div>
                </div>

                @if($supplier->notes)
                <p class="text-[11px] text-slate-500 italic bg-amber-50/50 p-2.5 rounded-xl border border-amber-200/40">
                    <i class="fas fa-circle-info text-amber-500 mr-1"></i> {{ $supplier->notes }}
                </p>
                @endif
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-between pt-5 mt-4 border-t border-slate-100">
                <a href="{{ route('nhaphang_them', ['supplier_id' => $supplier->id]) }}" 
                   class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-black transition-all shadow-xs flex items-center gap-1.5">
                    <i class="fas fa-cart-arrow-down"></i> Lập Phiếu Nhập
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('nhacungcap_chinhsua', $supplier->id) }}" 
                       class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors flex items-center gap-1">
                        <i class="fas fa-pen-to-square"></i> Sửa
                    </a>
                    
                    <form method="POST" action="{{ route('nhacungcap_xoa', $supplier->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này?')">
                        @csrf
                        <button type="submit" class="p-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 text-xs transition-colors" title="Xóa nhà cung cấp">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
        @empty
        <div class="col-span-2 py-16 text-center bg-white rounded-3xl border border-slate-200">
            <i class="fas fa-truck-ramp-box text-5xl text-slate-300 mb-3 block"></i>
            <span class="font-bold text-slate-700 text-base">Chưa có nhà cung cấp nào!</span>
            <p class="text-xs text-slate-400 mt-1">Bấm nút "Thêm Nhà Cung Cấp Mới" để khai báo nguồn nhập hàng.</p>
        </div>
        @endforelse
    </div>

    @if($suppliers->hasPages())
    <div class="p-4 bg-white rounded-3xl border border-slate-200">
        {{ $suppliers->links() }}
    </div>
    @endif

</div>
@endsection
