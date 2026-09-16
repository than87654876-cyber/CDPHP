@extends('layouts.app')

@section('title', $dish->dish_name . ' - Chi Tiết Món Ăn FOODDAILY')

@section('content')
<div class="py-6 sm:py-8 bg-transparent min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

        <!-- Navigation Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('trangchu') }}" class="hover:text-[#ee4d2d] transition-colors">
                <i class="fas fa-home mr-1"></i> Trang chủ
            </a>
            <i class="fas fa-chevron-right text-[9px] text-slate-400"></i>
            <a href="{{ route('trangchu') }}#menu" class="hover:text-[#ee4d2d] transition-colors">
                {{ $dish->category->category_name ?? 'Thực đơn' }}
            </a>
            <i class="fas fa-chevron-right text-[9px] text-slate-400"></i>
            <span class="text-[#ee4d2d] font-bold truncate max-w-xs">{{ $dish->dish_name }}</span>
        </nav>

        <!-- MAIN DISH DETAIL CARD -->
        <div class="bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-sm border border-slate-200 grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-start">
            
            <!-- LEFT: Big Dish Image Preview -->
            <div class="lg:col-span-5 space-y-3">
                <div class="aspect-square w-full rounded-2xl bg-slate-100 overflow-hidden relative border border-slate-200 shadow-inner group">
                    <img src="{{ $dish->display_image }}" alt="{{ $dish->dish_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    
                    <!-- Favorite Red Badge -->
                    <span class="absolute top-3 left-3 bg-[#ee4d2d] text-white text-xs font-black px-3 py-1 rounded-lg shadow-md flex items-center gap-1.5">
                        <i class="fas fa-thumbs-up text-[10px]"></i> Yêu thích
                    </span>

                    <!-- Category Floating Pill -->
                    <span class="absolute bottom-3 left-3 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-xs text-white text-[11px] font-extrabold tracking-wide uppercase shadow-sm">
                        <i class="fas fa-tag mr-1 text-rose-400"></i> {{ $dish->category->category_name ?? 'Món ngon' }}
                    </span>
                </div>
            </div>

            <!-- RIGHT: Dish Information & Order Form -->
            <div class="lg:col-span-7 space-y-5">
                
                <!-- Category Tag & Status -->
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-[#ee4d2d] text-xs font-black uppercase tracking-wider">
                        <i class="fas fa-layer-group text-[10px]"></i> Danh mục: {{ $dish->category->category_name ?? 'Món Ăn' }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-emerald-600 text-xs font-bold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Còn hàng & Sẵn sàng giao
                    </span>
                </div>

                <!-- Title & Meta -->
                <div class="space-y-2">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 leading-tight">
                        {{ $dish->dish_name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 font-semibold">
                        <div class="flex items-center gap-1.5 text-amber-500 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/80">
                            <i class="fas fa-star text-xs text-amber-400"></i>
                            <span class="font-black text-slate-900 text-sm">{{ number_format($dish->rating_score, 1) }} / 5.0</span>
                            <span class="text-slate-400 font-medium text-xs">({{ $dish->reviews_count }} đánh giá)</span>
                        </div>
                        <span>•</span>
                        <span class="text-slate-600 font-medium"><i class="fas fa-fire text-rose-500 mr-1"></i>Đã bán {{ number_format($dish->reviews_count * 8) }}+</span>
                        <span>•</span>
                        <span class="text-slate-600 font-medium"><i class="fas fa-bolt text-amber-500 mr-1"></i>Giao siêu tốc 20 phút</span>
                    </div>
                </div>

                <!-- Price Box -->
                <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-100 flex flex-wrap items-baseline gap-3">
                    <span class="text-2xl sm:text-3xl font-black text-[#ee4d2d]" id="unit-price-display" data-price="{{ $dish->price }}">
                        {{ number_format($dish->price) }}đ
                    </span>
                    <span class="text-xs text-slate-400 line-through font-semibold">
                        {{ number_format($dish->price * 1.2) }}đ
                    </span>
                    <span class="px-2 py-0.5 rounded bg-rose-100 text-[#ee4d2d] text-[11px] font-extrabold border border-rose-200">
                        🏷️ Giảm 20% hôm nay
                    </span>
                </div>

                <!-- Description -->
                <div class="space-y-1.5 text-xs sm:text-sm text-slate-600 leading-relaxed">
                    <h4 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Mô tả món ăn</h4>
                    <p class="font-medium text-slate-600 bg-slate-50/80 p-3 rounded-xl border border-slate-100">
                        {{ $dish->description ?? 'Món ăn thơm ngon, chuẩn vị được chế biến từ nguyên liệu tươi sạch mỗi ngày, đảm bảo tiêu chuẩn vệ sinh an toàn thực phẩm.' }}
                    </p>
                </div>

                <!-- Order Action Form -->
                <form action="{{ route('giohang.add') }}" method="POST" id="dishOrderForm" class="space-y-4 pt-2">
                    @csrf
                    <input type="hidden" name="dish_id" value="{{ $dish->id }}">

                    <!-- Quantity Control -->
                    <div class="flex items-center gap-4">
                        <label class="text-xs font-bold text-slate-700 uppercase">Số lượng:</label>
                        <div class="flex items-center gap-2 bg-slate-100 rounded-xl p-1 border border-slate-200">
                            <button type="button" onclick="adjustQty(-1)" class="w-8 h-8 rounded-lg bg-white hover:bg-slate-200 text-slate-800 font-black text-sm flex items-center justify-center border border-slate-200 shadow-2xs transition-colors cursor-pointer">-</button>
                            <input type="number" name="quantity" id="detail-qty-input" value="1" min="1" max="99" class="w-12 text-center text-xs font-black text-slate-900 bg-transparent outline-none">
                            <button type="button" onclick="adjustQty(1)" class="w-8 h-8 rounded-lg bg-white hover:bg-slate-200 text-slate-800 font-black text-sm flex items-center justify-center border border-slate-200 shadow-2xs transition-colors cursor-pointer">+</button>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Tạm tính: <strong class="text-slate-900 font-extrabold text-sm" id="calc-subtotal">{{ number_format($dish->price) }}đ</strong></span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                        <button type="submit" class="w-full sm:flex-1 py-3.5 px-6 rounded-xl bg-rose-50 hover:bg-rose-100 text-[#ee4d2d] border border-rose-200 font-black text-xs sm:text-sm shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fas fa-cart-plus text-base"></i> THÊM VÀO GIỎ HÀNG
                        </button>
                        <button type="button" onclick="buyNowSubmit()" class="w-full sm:flex-1 py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#ee4d2d] to-red-600 hover:from-red-600 hover:to-[#ee4d2d] text-white font-black text-xs sm:text-sm shadow-lg shadow-rose-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer uppercase tracking-wider">
                            <i class="fas fa-bolt text-amber-300"></i> ĐẶT MÓN NGAY
                        </button>
                    </div>
                </form>

                <!-- Guarantee Badges -->
                <div class="grid grid-cols-3 gap-2 pt-3 border-t border-slate-100 text-center">
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                        <i class="fas fa-shield-halved text-emerald-600 text-sm mb-1 block"></i>
                        <span class="text-[10px] font-bold text-slate-700 block">Vệ sinh ATTP</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                        <i class="fas fa-truck-fast text-[#ee4d2d] text-sm mb-1 block"></i>
                        <span class="text-[10px] font-bold text-slate-700 block">Giao hỏa tốc</span>
                    </div>
                    <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                        <i class="fas fa-rotate-left text-blue-600 text-sm mb-1 block"></i>
                        <span class="text-[10px] font-bold text-slate-700 block">Hỗ trợ đổi trả</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- KHU VỰC ĐỀ XUẤT: MÓN CÙNG LOẠI BẠN CÓ THỂ THÍCH -->
        @if(isset($relatedDishes) && $relatedDishes->isNotEmpty())
        <section class="space-y-4 pt-2">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-sm border border-rose-200 space-y-4">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-rose-100/70">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#ee4d2d] text-white text-sm font-black shadow-xs shadow-rose-500/20">
                            <i class="fas fa-thumbs-up"></i>
                        </span>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base sm:text-lg">
                                Món Cùng Loại Bạn Có Thể Thích ({{ $dish->category->category_name ?? 'Cùng danh mục' }})
                            </h3>
                            <p class="text-xs text-slate-500 font-medium">
                                Khám phá các món ăn hấp dẫn khác cùng danh mục "{{ $dish->category->category_name ?? 'Món Ăn' }}"
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('trangchu') }}#menu" class="text-xs font-extrabold text-[#ee4d2d] hover:text-red-700 transition-colors flex items-center gap-1 shrink-0">
                        <span>≡ Xem tất cả thực đơn</span>
                    </a>
                </div>

                <!-- Responsive 6-Card Grid Món Cùng Loại -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                    @foreach($relatedDishes as $rDish)
                        <div class="bg-white hover:bg-rose-50/40 rounded-xl p-2.5 sm:p-3 border border-rose-100 hover:border-[#ee4d2d]/40 flex flex-col justify-between shadow-2xs hover:shadow-md transition-all duration-300 group">
                            
                            <a href="{{ route('dish.detail', $rDish->id) }}" class="space-y-2 block" title="{{ $rDish->dish_name }}">
                                <div class="aspect-square w-full rounded-lg bg-slate-100 overflow-hidden relative border border-slate-100">
                                    <img src="{{ $rDish->display_image }}" alt="{{ $rDish->dish_name }}" class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-300" loading="lazy">
                                    <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded bg-white/90 backdrop-blur-xs text-[#ee4d2d] text-[9px] font-black uppercase shadow-2xs">
                                        {{ $rDish->category->category_name ?? 'Cùng loại' }}
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-extrabold text-slate-900 text-xs line-clamp-1 group-hover:text-[#ee4d2d] transition-colors">
                                        {{ $rDish->dish_name }}
                                    </h4>
                                    <p class="text-[10px] text-slate-400 line-clamp-1 font-medium">
                                        {{ $rDish->description ?? 'Món ngon đậm vị' }}
                                    </p>
                                </div>
                            </a>

                            <div class="pt-2 mt-2 border-t border-slate-100 flex items-center justify-between gap-1.5">
                                <span class="text-xs text-[#ee4d2d] font-black">{{ number_format($rDish->price) }}đ</span>
                                <form action="{{ route('giohang.add') }}" method="POST" class="shrink-0">
                                    @csrf
                                    <input type="hidden" name="dish_id" value="{{ $rDish->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="px-2.5 py-1 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-lg text-[11px] font-extrabold shadow-2xs transition-colors flex items-center gap-1 cursor-pointer">
                                        <i class="fas fa-cart-plus text-[10px]"></i> + Đặt
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        </section>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
    const basePrice = {{ (float)$dish->price }};

    function adjustQty(delta) {
        const input = document.getElementById('detail-qty-input');
        let current = parseInt(input.value) || 1;
        current = Math.max(1, Math.min(99, current + delta));
        input.value = current;
        updateSubtotalDisplay(current);
    }

    function updateSubtotalDisplay(qty) {
        const total = basePrice * qty;
        const formatted = total.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + 'đ';
        document.getElementById('calc-subtotal').innerText = formatted;
    }

    document.getElementById('detail-qty-input')?.addEventListener('input', function() {
        let val = parseInt(this.value) || 1;
        val = Math.max(1, Math.min(99, val));
        this.value = val;
        updateSubtotalDisplay(val);
    });

    function buyNowSubmit() {
        const form = document.getElementById('dishOrderForm');
        if (form) {
            form.submit();
        }
    }
</script>
@endsection
