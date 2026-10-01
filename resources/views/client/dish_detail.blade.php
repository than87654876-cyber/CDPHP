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

        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-sm cursor-pointer">&times;</button>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <i class="fas fa-circle-exclamation text-rose-600 text-sm"></i>
                    <span>{{ session('error') ?? $errors->first() }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-sm cursor-pointer">&times;</button>
            </div>
        @endif

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
                        <a href="#dish-reviews-section" class="flex items-center gap-1.5 text-amber-500 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/80 hover:bg-amber-100 transition-colors">
                            <i class="fas fa-star text-xs text-amber-400"></i>
                            <span class="font-black text-slate-900 text-sm">{{ number_format($dish->rating_score, 1) }} / 5.0</span>
                            <span class="text-slate-400 font-medium text-xs">({{ $dish->reviews_count }} đánh giá)</span>
                        </a>
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
                        <button type="button" onclick="buyNowSubmit()" class="w-full sm:flex-1 py-3.5 px-6 rounded-xl text-white font-black text-xs sm:text-sm shadow-lg shadow-rose-500/25 hover:brightness-110 transition-all flex items-center justify-center gap-2 cursor-pointer uppercase tracking-wider" style="background: linear-gradient(135deg, #ee4d2d 0%, #dc2626 100%) !important; color: #ffffff !important; border: none !important;">
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

        <!-- SECTION: ĐÁNH GIÁ & NHẬN XÉT CỦA KHÁCH HÀNG VỀ MÓN ĂN NÀY -->
        <section class="space-y-6 pt-2" id="dish-reviews-section">
            <div class="bg-white/95 backdrop-blur-md rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-sm border border-slate-200 space-y-6">
                
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-500 border border-amber-200 flex items-center justify-center text-lg font-black shrink-0">
                            <i class="fas fa-star"></i>
                        </span>
                        <div>
                            <h2 class="font-black text-slate-900 text-lg sm:text-xl flex items-center gap-2">
                                Đánh Giá & Nhận Xét Món Ăn
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs font-black border border-amber-200">
                                    {{ $dish->reviews_count }} đánh giá
                                </span>
                            </h2>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">
                                Khách hàng nhận xét thực tế sau khi đặt và thưởng thức món "{{ $dish->dish_name }}"
                            </p>
                        </div>
                    </div>

                    @auth
                        <a href="#write-dish-review" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs font-black shadow-xs transition-all">
                            <i class="fas fa-pen-to-square"></i> Viết đánh giá món này
                        </a>
                    @else
                        <a href="{{ route('dangnhap') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                            <i class="fas fa-arrow-right-to-bracket text-slate-400"></i> Đăng nhập để đánh giá
                        </a>
                    @endauth
                </div>

                <!-- Rating Overview Statistics Grid -->
                <div class="bg-gradient-to-br from-amber-50/60 via-orange-50/40 to-rose-50/40 rounded-2xl p-5 border border-amber-200/70 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                    
                    <!-- Left: Average Score -->
                    <div class="md:col-span-4 text-center md:text-left space-y-2 md:border-r md:border-amber-200/80 pr-4">
                        <div class="flex items-baseline justify-center md:justify-start gap-2">
                            <span class="text-4xl sm:text-5xl font-black text-[#ee4d2d]">
                                {{ number_format($dish->rating_score, 1) }}
                            </span>
                            <span class="text-base text-slate-400 font-bold">/ 5.0</span>
                        </div>
                        <div class="flex items-center justify-center md:justify-start gap-1 text-amber-400 text-lg">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($dish->rating_score))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star text-slate-300"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-xs font-bold text-slate-600">
                            Dựa trên <strong class="text-slate-900">{{ $dish->reviews_count }}</strong> lượt đánh giá từ người mua
                        </p>
                    </div>

                    <!-- Middle: Star Distribution Bars -->
                    <div class="md:col-span-5 space-y-1.5">
                        @php
                            $ratingDist = [
                                5 => 86,
                                4 => 10,
                                3 => 3,
                                2 => 1,
                                1 => 0,
                            ];
                        @endphp
                        @foreach($ratingDist as $star => $pct)
                            <div class="flex items-center gap-2 text-xs">
                                <span class="font-extrabold text-slate-600 w-8">{{ $star }} ★</span>
                                <div class="flex-1 bg-white rounded-full h-2 overflow-hidden border border-amber-200/80">
                                    <div class="bg-amber-400 h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%;"></div>
                                </div>
                                <span class="text-[11px] font-bold text-slate-400 w-10 text-right">{{ $pct }}%</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Right: Quality Highlights -->
                    <div class="md:col-span-3 space-y-2 text-xs text-slate-600 border-t md:border-t-0 md:border-l md:border-amber-200/80 pt-3 md:pt-0 pl-0 md:pl-4">
                        <div class="font-black text-slate-800 uppercase tracking-wider text-[11px]">Đặc điểm nổi bật:</div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                            <span class="font-semibold">Hương vị chuẩn vị, nóng sốt</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                            <span class="font-semibold">Đóng gói sạch sẽ, cẩn thận</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                            <span class="font-semibold">Giao hàng đúng hẹn</span>
                        </div>
                    </div>

                </div>

                <!-- Form Gửi Đánh Giá Trực Tiếp Cho Món Ăn Này -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4" id="write-dish-review">
                    <div class="flex items-center justify-between">
                        <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                            <i class="fas fa-comment-dots text-[#ee4d2d]"></i> Viết Đánh Giá Của Bạn Cho Món Này
                        </h3>
                        <span class="text-[11px] text-slate-400 font-medium">Đánh giá giúp cộng đồng chọn món ngon hơn</span>
                    </div>

                    @auth
                        <form action="{{ route('dish.review', $dish->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="rating" id="direct-rating-input" value="5">

                            <!-- Interactive Stars -->
                            <div class="flex flex-wrap items-center gap-4">
                                <label class="text-xs font-bold text-slate-700">Chấm điểm món ăn:</label>
                                <div class="flex items-center gap-1.5" id="direct-stars-picker">
                                    @for($s = 1; $s <= 5; $s++)
                                        <button type="button" 
                                                onclick="selectDirectRating({{ $s }})" 
                                                class="text-2xl text-amber-400 transition-transform hover:scale-125 cursor-pointer direct-star-btn"
                                                data-star="{{ $s }}">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    @endfor
                                </div>
                                <span class="text-xs font-black text-amber-600" id="direct-rating-text">⭐⭐⭐⭐⭐ Tuyệt vời! Rất hài lòng</span>
                            </div>

                            <!-- Comment Input -->
                            <div>
                                <textarea name="comment" rows="3" required
                                          placeholder="Chia sẻ cảm nhận chi tiết của bạn về hương vị món ăn, nguyên liệu, độ ngon miệng..."
                                          class="w-full p-3.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 outline-none focus:border-[#ee4d2d] transition-all shadow-2xs"></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:brightness-110 text-white font-black text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer">
                                    <i class="fas fa-paper-plane text-xs"></i> Gửi Đánh Giá Ngay
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="py-4 text-center space-y-2">
                            <p class="text-xs text-slate-500 font-medium">Vui lòng đăng nhập để có thể gửi đánh giá cho món ăn này.</p>
                            <a href="{{ route('dangnhap') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-black shadow-xs transition-all">
                                <i class="fas fa-right-to-bracket text-xs"></i> Đăng nhập ngay
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Danh Sách Đánh Giá Khách Hàng (Customer Reviews List) -->
                <div class="space-y-4 pt-2">
                    <h3 class="font-extrabold text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2">
                        <span>Nhận xét gần đây</span>
                        <span class="text-slate-400 text-xs font-normal">({{ $reviews->count() }} nhận xét)</span>
                    </h3>

                    @if($reviews->isEmpty())
                        <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-xl mx-auto">
                                <i class="far fa-comment-dots"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700">Chưa có đánh giá nào cho món ăn này</h4>
                            <p class="text-[11px] text-slate-400">Hãy là người đầu tiên trải nghiệm và chia sẻ nhận xét của bạn về món ăn này nhé!</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100 space-y-3">
                            @foreach($reviews as $rev)
                                <div class="pt-3.5 first:pt-0 space-y-2">
                                    <!-- Review Header -->
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <!-- User Avatar -->
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#ee4d2d] to-amber-500 text-white font-black text-xs flex items-center justify-center shadow-2xs">
                                                {{ strtoupper(substr($rev->user->fullname ?? $rev->user->name ?? 'K', 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-black text-slate-900 text-xs flex items-center gap-2">
                                                    <span>{{ $rev->user->fullname ?? $rev->user->name ?? 'Khách hàng FOODDAILY' }}</span>
                                                    <span class="px-2 py-0.2 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                                                        <i class="fas fa-check-circle text-[9px] mr-0.5"></i> Đã mua món
                                                    </span>
                                                </h4>
                                                <!-- Stars -->
                                                <div class="flex items-center gap-2 text-xs pt-0.5">
                                                    <div class="flex items-center text-amber-400 text-[11px]">
                                                        @for($s = 1; $s <= 5; $s++)
                                                            @if($s <= $rev->rating)
                                                                <i class="fas fa-star"></i>
                                                            @else
                                                                <i class="far fa-star text-slate-300"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span class="text-[11px] text-slate-400 font-medium">
                                                        {{ $rev->created_at ? $rev->created_at->format('d/m/Y H:i') : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Review Content -->
                                    <div class="pl-12">
                                        <p class="text-xs text-slate-700 leading-relaxed font-medium bg-slate-50/80 p-3 rounded-xl border border-slate-100">
                                            {{ $rev->comment }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </section>

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

    // Direct Star Rating on Dish Detail Page
    const directRatingLabels = {
        1: '⭐ Rất tệ! Không hài lòng',
        2: '⭐⭐ Tệ! Cần cải thiện',
        3: '⭐⭐⭐ Bình thường! Tạm được',
        4: '⭐⭐⭐⭐ Ngon! Khá hài lòng',
        5: '⭐⭐⭐⭐⭐ Tuyệt vời! Rất hài lòng'
    };

    function selectDirectRating(stars) {
        document.getElementById('direct-rating-input').value = stars;
        document.getElementById('direct-rating-text').innerText = directRatingLabels[stars] || '';
        document.querySelectorAll('.direct-star-btn').forEach(btn => {
            const s = parseInt(btn.getAttribute('data-star'));
            if (s <= stars) {
                btn.classList.add('text-amber-400');
                btn.classList.remove('text-slate-200');
            } else {
                btn.classList.remove('text-amber-400');
                btn.classList.add('text-slate-200');
            }
        });
    }
</script>
@endsection
