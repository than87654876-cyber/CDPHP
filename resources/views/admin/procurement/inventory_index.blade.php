@extends('layouts.admin')

@section('title', 'Quản Lý Kho & Hàng Tồn - FOODDAILY Admin')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-black">
                    <i class="fas fa-boxes-stacked"></i> Kho Vận & Cung Ứng
                </span>
                <span>•</span>
                <span>Kiểm soát hàng tồn & cảnh báo thiếu hụt</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fas fa-warehouse text-blue-600 text-3xl"></i> Quản Lý Kho & Hàng Tồn Thực Tế
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Theo dõi số lượng hàng tồn, nguyên liệu tươi sống, đồ uống đóng chai và nguồn nhà cung cấp để chuẩn bị nhập hàng.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('nhaphang_them') }}" class="px-4 py-2.5 rounded-2xl bg-[#ee4d2d] hover:bg-[#d73211] text-white text-xs font-bold transition-all shadow-md shadow-rose-500/20 flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar"></i> Tạo Phiếu Nhập Kho
            </a>
            <a href="{{ route('kho_them') }}" class="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-2">
                <i class="fas fa-plus"></i> Thêm Mặt Hàng Mới
            </a>
            <a href="{{ route('quanly_nhacungcap') }}" class="px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold transition-all shadow-2xs flex items-center gap-2">
                <i class="fas fa-truck-field text-amber-500"></i> Nguồn Nhập Hàng (NCC)
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

    <!-- 4 Thẻ Thống Kê Tổng Quan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Tổng mặt hàng -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Tổng mặt hàng trong kho</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalItems) }} <span class="text-xs font-bold text-slate-400">loại</span></h3>
                <p class="text-[11px] font-semibold text-slate-500 mt-1">Bao gồm nguyên liệu & đồ bán sẵn</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-cubes"></i>
            </div>
        </div>

        <!-- Hết hàng khẩn cấp -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between {{ $outOfStockCount > 0 ? 'ring-2 ring-rose-500/30 bg-rose-50/20' : '' }}">
            <div>
                <p class="text-[11px] font-extrabold text-rose-500 uppercase tracking-wider">Hết hàng (Tồn = 0)</p>
                <h3 class="text-2xl font-black text-rose-600 mt-1">{{ number_format($outOfStockCount) }} <span class="text-xs font-bold text-rose-400">mặt hàng</span></h3>
                <p class="text-[11px] font-semibold text-rose-600/80 mt-1">Cần lập đơn đặt hàng gấp</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
        </div>

        <!-- Sắp hết hàng -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between {{ $lowStockCount > 0 ? 'ring-1 ring-amber-500/30' : '' }}">
            <div>
                <p class="text-[11px] font-extrabold text-amber-500 uppercase tracking-wider">Sắp hết (Dưới mức Min)</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ number_format($lowStockCount) }} <span class="text-xs font-bold text-amber-400">mặt hàng</span></h3>
                <p class="text-[11px] font-semibold text-amber-600/80 mt-1">Chuẩn bị liên hệ nhà cung cấp</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-battery-quarter"></i>
            </div>
        </div>

        <!-- Tổng giá trị vốn kho -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Giá trị tồn kho ước tính</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($totalInventoryValue, 0, ',', '.') }} <span class="text-xs font-bold text-emerald-500">đ</span></h3>
                <p class="text-[11px] font-semibold text-slate-500 mt-1">Dựa trên giá vốn nhập gần nhất</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-coins"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('quanly_kho') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            
            <!-- Ô tìm kiếm -->
            <div class="md:col-span-4 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên hàng hoặc mã (NL-..., SP-...)" 
                       class="w-full pl-9 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
            </div>

            <!-- Lọc Danh mục -->
            <div class="md:col-span-2">
                <select name="category" class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Lọc Trạng thái tồn kho -->
            <div class="md:col-span-3">
                <select name="stock_status" class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30">
                    <option value="">Tất cả mức tồn</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>🔴 Hết hàng (Cần nhập ngay)</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>🟡 Sắp hết (Dưới mức Min)</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>🟢 Tồn kho an toàn</option>
                </select>
            </div>

            <!-- Lọc Nhà cung cấp -->
            <div class="md:col-span-2">
                <select name="supplier_id" class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30">
                    <option value="">Tất cả NCC</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nút Lọc & Reset -->
            <div class="md:col-span-1 flex items-center gap-1.5">
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs font-bold transition-all shadow-xs flex items-center justify-center">
                    <i class="fas fa-filter"></i>
                </button>
                @if(request()->anyFilled(['q', 'category', 'stock_status', 'supplier_id']))
                    <a href="{{ route('quanly_kho') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs transition-colors flex items-center justify-center" title="Xóa lọc">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bảng Danh Sách Hàng Tồn Kho -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-5">Mã & Mặt Hàng</th>
                        <th class="py-4 px-4">Danh Mục</th>
                        <th class="py-4 px-4 text-center">Tồn Kho Thực Tế</th>
                        <th class="py-4 px-4 text-center">Mức Tối Thiểu</th>
                        <th class="py-4 px-4 text-center">Trạng Thái Kho</th>
                        <th class="py-4 px-5">Nguồn Nhập Hàng (Nhà Cung Cấp)</th>
                        <th class="py-4 px-4 text-right">Giá Vốn Nhập</th>
                        <th class="py-4 px-5 text-center">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($items as $item)
                    <tr class="hover:bg-slate-50/80 transition-colors {{ $item->stock_status === 'out_of_stock' ? 'bg-rose-50/30' : ($item->stock_status === 'low_stock' ? 'bg-amber-50/20' : '') }}">
                        
                        <!-- Mã & Tên mặt hàng -->
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 
                                    {{ $item->stock_status === 'out_of_stock' ? 'bg-rose-100 text-rose-600' : ($item->stock_status === 'low_stock' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600') }}">
                                    @if(str_contains(strtolower($item->category ?? ''), 'thịt'))
                                        <i class="fas fa-drumstick-bite"></i>
                                    @elseif(str_contains(strtolower($item->category ?? ''), 'uống') || str_contains(strtolower($item->category ?? ''), 'lon'))
                                        <i class="fas fa-wine-bottle"></i>
                                    @elseif(str_contains(strtolower($item->category ?? ''), 'rau'))
                                        <i class="fas fa-carrot"></i>
                                    @else
                                        <i class="fas fa-box"></i>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-800 text-[13px] block">{{ $item->name }}</span>
                                    <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider font-bold">Mã: {{ $item->item_code }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Danh mục -->
                        <td class="py-4 px-4">
                            <span class="inline-block px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 text-[11px] font-bold">
                                {{ $item->category ?? 'Chưa phân loại' }}
                            </span>
                        </td>

                        <!-- Tồn kho thực tế -->
                        <td class="py-4 px-4 text-center">
                            <span class="text-sm font-black {{ $item->stock_status === 'out_of_stock' ? 'text-rose-600' : ($item->stock_status === 'low_stock' ? 'text-amber-600' : 'text-slate-800') }}">
                                {{ rtrim(rtrim(number_format($item->current_stock, 2, '.', ','), '0'), '.') }}
                            </span>
                            <span class="text-[11px] font-bold text-slate-400 lowercase ml-0.5">{{ $item->unit }}</span>
                        </td>

                        <!-- Mức tối thiểu (Min stock) -->
                        <td class="py-4 px-4 text-center">
                            <span class="text-xs font-semibold text-slate-500">
                                {{ rtrim(rtrim(number_format($item->min_stock, 2, '.', ','), '0'), '.') }} {{ $item->unit }}
                            </span>
                        </td>

                        <!-- Trạng thái kho -->
                        <td class="py-4 px-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] {{ $item->status_badge_class }}">
                                @if($item->stock_status === 'out_of_stock')
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                    <span>Hết hàng</span>
                                @elseif($item->stock_status === 'low_stock')
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Sắp hết</span>
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Đủ tồn kho</span>
                                @endif
                            </span>
                        </td>

                        <!-- Nguồn nhập hàng (Nhà cung cấp) -->
                        <td class="py-4 px-5">
                            @if($item->supplier)
                                <div class="space-y-0.5">
                                    <a href="{{ route('quanly_nhacungcap', ['q' => $item->supplier->supplier_code]) }}" class="font-bold text-slate-800 hover:text-blue-600 transition-colors block leading-tight">
                                        {{ $item->supplier->name }}
                                    </a>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                        <a href="tel:{{ $item->supplier->phone }}" class="text-[#ee4d2d] font-bold hover:underline flex items-center gap-1">
                                            <i class="fas fa-phone-alt text-[9px]"></i> {{ $item->supplier->phone }}
                                        </a>
                                        @if($item->supplier->contact_person)
                                            <span class="text-slate-400">• {{ Str::limit($item->supplier->contact_person, 18) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-slate-400 italic text-[11px]">Chưa gắn NCC</span>
                            @endif
                        </td>

                        <!-- Giá vốn nhập gần nhất -->
                        <td class="py-4 px-4 text-right">
                            <span class="font-bold text-slate-800">
                                {{ number_format($item->cost_price, 0, ',', '.') }}đ
                            </span>
                            <span class="text-[10px] text-slate-400 block font-normal">/ {{ $item->unit }}</span>
                        </td>

                        <!-- Thao tác -->
                        <td class="py-4 px-5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                
                                <!-- Nút Nhập Hàng Nhanh -->
                                <a href="{{ route('nhaphang_them', ['supplier_id' => $item->supplier_id, 'item_id' => $item->id]) }}" 
                                   class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-[#ee4d2d] text-[#ee4d2d] hover:text-white text-[11px] font-extrabold transition-all border border-rose-200 shadow-2xs flex items-center gap-1"
                                   title="Tạo phiếu nhập mặt hàng này">
                                    <i class="fas fa-cart-arrow-down"></i> Nhập
                                </a>

                                <!-- Nút Điều Chỉnh Tồn Kho (Kiểm kê) -->
                                <button type="button" 
                                        onclick="openAdjustModal('{{ $item->id }}', '{{ addslashes($item->name) }}', '{{ $item->current_stock }}', '{{ $item->unit }}')" 
                                        class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition-colors" 
                                        title="Kiểm kê / Cân bằng tồn kho">
                                    <i class="fas fa-sliders"></i>
                                </button>

                                <!-- Nút Sửa -->
                                <a href="{{ route('kho_chinhsua', $item->id) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition-colors" title="Sửa thông tin">
                                    <i class="fas fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <i class="fas fa-box-open text-4xl mb-3 text-slate-300 block"></i>
                            <span class="font-bold text-sm">Chưa tìm thấy mặt hàng nào trong kho!</span>
                            <p class="text-xs text-slate-400 mt-1">Hãy thử xóa bộ lọc tìm kiếm hoặc bấm nút "Thêm Mặt Hàng Mới".</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $items->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal Điều Chỉnh Tồn Kho Nhanh -->
<div id="adjustModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-sliders text-blue-600"></i> Điều Chỉnh Tồn Kho (Kiểm Kê)
            </h3>
            <button onclick="closeAdjustModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
        </div>

        <form id="adjustForm" method="POST" action="">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Mặt hàng kiểm kê</label>
                    <p id="modalItemName" class="text-sm font-extrabold text-slate-900 bg-slate-50 p-3 rounded-2xl border border-slate-200"></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Tồn kho hiện tại</label>
                        <p id="modalCurrentStock" class="text-sm font-bold text-slate-600 bg-slate-50 p-3 rounded-2xl border border-slate-200"></p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Số lượng thực tế mới *</label>
                        <input type="number" step="0.01" min="0" name="new_stock" id="modalNewStock" required 
                               class="w-full p-3 rounded-2xl bg-white border border-slate-300 text-sm font-black text-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Lý do điều chỉnh *</label>
                    <input type="text" name="reason" placeholder="Ví dụ: Kiểm kê định kỳ, hao hụt sơ chế, đổ vỡ..." required
                           class="w-full p-3 rounded-2xl bg-white border border-slate-300 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeAdjustModal()" class="px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-colors">
                    Hủy bỏ
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-md shadow-blue-500/20 transition-all">
                    Lưu Kết Quả Kiểm Kê
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAdjustModal(itemId, itemName, currentStock, unit) {
        document.getElementById('adjustForm').action = "{{ url('/quanly/kho/dieuchinh') }}/" + itemId;
        document.getElementById('modalItemName').textContent = itemName;
        document.getElementById('modalCurrentStock').textContent = currentStock + ' ' + unit;
        document.getElementById('modalNewStock').value = currentStock;
        document.getElementById('adjustModal').classList.remove('hidden');
    }

    function closeAdjustModal() {
        document.getElementById('adjustModal').classList.add('hidden');
    }
</script>
@endsection
