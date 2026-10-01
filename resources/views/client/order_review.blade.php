@extends('layouts.app')

@section('title', 'Đánh Giá Đơn Hàng #FDL-' . $order->id . ' - FOODDAILY')

@section('content')
<div class="py-8 sm:py-12 bg-transparent min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
        
        <!-- Navigation Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('trangchu') }}" class="hover:text-[#ee4d2d] transition-colors">
                <i class="fas fa-home mr-1"></i> Trang chủ
            </a>
            <i class="fas fa-chevron-right text-[9px] text-slate-400"></i>
            <a href="{{ route('giohang') }}" class="hover:text-[#ee4d2d] transition-colors">
                Đơn hàng của tôi
            </a>
            <i class="fas fa-chevron-right text-[9px] text-slate-400"></i>
            <span class="text-[#ee4d2d] font-bold">Đánh giá đơn #FDL-{{ $order->id }}</span>
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

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <i class="fas fa-circle-exclamation text-rose-600 text-sm"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-sm cursor-pointer">&times;</button>
            </div>
        @endif

        <!-- CELEBRATION HEADER CARD -->
        <div class="bg-gradient-to-r from-emerald-500/10 via-amber-500/10 to-rose-500/10 border-2 border-emerald-200/90 rounded-3xl p-6 sm:p-8 text-center space-y-3 relative overflow-hidden backdrop-blur-xs shadow-xs">
            <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center text-3xl mx-auto shadow-md shadow-emerald-500/30">
                <i class="fas fa-circle-check"></i>
            </div>
            
            <div class="space-y-1">
                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider inline-flex items-center gap-1.5">
                    <i class="fas fa-motorcycle text-emerald-600"></i> Giao Đơn Hàng Thành Công
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Đơn Hàng #FDL-{{ $order->id }} Đã Hoàn Tất!
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto font-medium">
                    Shipper đã giao món ăn nóng sốt đến bạn. Hãy chia sẻ cảm nhận trải nghiệm món ăn của bạn nhé!
                </p>
            </div>

            <!-- Tùy chọn rõ ràng: Có thể đánh giá hoặc không đánh giá -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 border border-slate-200 text-[11px] font-bold text-slate-500">
                <i class="fas fa-circle-info text-amber-500"></i>
                <span>Bạn có thể <strong>đánh giá ngay</strong> bên dưới hoặc <strong>bỏ qua không đánh giá</strong> nếu bận.</span>
            </div>
        </div>

        <!-- MAIN REVIEW CARD -->
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">

            <!-- Danh sách các món ăn trong đơn hàng -->
            <div class="space-y-3 pb-5 border-b border-slate-100">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <i class="fas fa-utensils text-[#ee4d2d]"></i> Các món bạn đã thưởng thức ({{ $order->orderItems->count() }} món)
                    </span>
                    <span class="text-slate-800 font-extrabold text-sm">
                        Tổng: {{ number_format($order->final_amount) }}đ
                    </span>
                </h3>

                <div class="divide-y divide-slate-100 bg-slate-50/70 rounded-2xl p-3 border border-slate-100 space-y-2">
                    @foreach($order->orderItems as $item)
                        <div class="flex items-center justify-between py-2 text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-white overflow-hidden shrink-0 border border-slate-200 shadow-2xs">
                                    <img src="{{ $item->dish->display_image ?? asset('images/default-dish.jpg') }}" alt="{{ $item->dish->dish_name ?? 'Món ăn' }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-900 text-xs sm:text-sm">
                                        {{ $item->dish->dish_name ?? 'Món ăn' }}
                                    </h4>
                                    <p class="text-[11px] text-slate-400 font-medium">
                                        {{ $item->dish->category->category_name ?? 'Món ngon' }} • Số lượng: <strong class="text-[#ee4d2d]">x{{ $item->quantity }}</strong>
                                    </p>
                                </div>
                            </div>
                            <span class="font-extrabold text-slate-800 text-xs sm:text-sm">
                                {{ number_format($item->price * $item->quantity) }}đ
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- NỘI DUNG ĐÁNH GIÁ HOẶC THÔNG BÁO ĐÃ ĐÁNH GIÁ -->
            @if($existingReview)
                <!-- Trường hợp: Đơn hàng đã được đánh giá -->
                <div class="p-6 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-amber-500 text-white flex items-center justify-center text-xl mx-auto shadow-xs">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-black text-slate-900 text-sm sm:text-base">Bạn đã gửi đánh giá cho đơn hàng này</h4>
                        <div class="flex items-center justify-center gap-1 text-amber-400 text-lg py-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $existingReview->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star text-slate-300"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-xs text-slate-600 font-medium italic bg-white p-3 rounded-xl border border-amber-100 max-w-md mx-auto">
                            "{{ $existingReview->comment }}"
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                        <a href="{{ route('giohang') }}" class="px-5 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-colors shadow-2xs">
                            <i class="fas fa-receipt mr-1"></i> Xem đơn hàng của tôi
                        </a>
                        <a href="{{ route('trangchu') }}#menu" class="px-5 py-2.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-black transition-colors shadow-xs">
                            <i class="fas fa-utensils mr-1"></i> Khám phá món ngon khác
                        </a>
                    </div>
                </div>
            @else
                <!-- Trường hợp: Khách có thể đánh giá hoặc không đánh giá -->
                <form action="{{ route('order.review') }}" method="POST" class="space-y-6" id="orderReviewForm">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="redirect_to" value="review_page">
                    <input type="hidden" name="rating_stars" id="page-rating-input" value="5">

                    <!-- Bộ chọn sao đánh giá (Interactive Star Rating) -->
                    <div class="text-center space-y-2.5 py-2 bg-slate-50/70 rounded-2xl p-4 border border-slate-100">
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">
                            Độ ngon miệng & chất lượng phục vụ:
                        </label>
                        
                        <div class="flex items-center justify-center gap-3 py-1" id="star-rating-container">
                            @for($s = 1; $s <= 5; $s++)
                                <button type="button" 
                                        onclick="selectPageRating({{ $s }})" 
                                        onmouseover="previewPageRating({{ $s }})"
                                        onmouseout="resetPageRatingPreview()"
                                        class="text-4xl text-amber-400 transition-all hover:scale-125 cursor-pointer page-star-btn" 
                                        data-star="{{ $s }}">
                                    <i class="fas fa-star"></i>
                                </button>
                            @endfor
                        </div>

                        <div class="text-xs sm:text-sm font-black text-amber-600 tracking-wide" id="page-rating-label">
                            ⭐⭐⭐⭐⭐ Tuyệt vời! Rất thơm ngon & hài lòng
                        </div>
                    </div>

                    <!-- Gợi ý nhanh các nhận xét (Quick Tags) -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-600">Chọn nhanh cảm nhận của bạn:</label>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $tags = [
                                    '🍲 Món ăn nóng sốt',
                                    '🛵 Giao hàng siêu nhanh',
                                    '🥗 Nguyên liệu tươi ngon',
                                    '🍱 Đóng gói cẩn thận',
                                    '😋 Chuẩn vị thơm ngon',
                                    '👨‍🍳 Đầu bếp tay nghề cao'
                                ];
                            @endphp
                            @foreach($tags as $t)
                                <button type="button" onclick="appendTag('{{ $t }}')" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-[#ee4d2d] text-slate-700 text-xs font-bold transition-all border border-slate-200 cursor-pointer">
                                    {{ $t }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Nhận xét chi tiết -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Nhận xét chi tiết của bạn:
                        </label>
                        <textarea name="review_comment" id="page-review-comment" rows="3" required
                                  placeholder="Hãy chia sẻ cảm nhận chân thật của bạn về hương vị món ăn, độ tươi ngon và đóng gói nhé..."
                                  class="w-full p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 outline-none focus:border-[#ee4d2d] focus:bg-white transition-all shadow-2xs"></textarea>
                    </div>

                    <!-- 2 LỰA CHỌN RÕ RÀNG: GỬI ĐÁNH GIÁ HOẶC BỎ QUA KHÔNG ĐÁNH GIÁ -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-100">
                        
                        <!-- Lựa chọn 2: Bỏ qua / Không đánh giá -->
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <a href="{{ route('giohang') }}" class="w-full sm:w-auto text-center px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-colors cursor-pointer">
                                <i class="fas fa-forward mr-1"></i> Bỏ qua, không đánh giá
                            </a>
                        </div>

                        <!-- Lựa chọn 1: Gửi đánh giá ngay -->
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-[#ee4d2d] hover:brightness-110 text-white font-black text-xs sm:text-sm shadow-md shadow-rose-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-paper-plane text-xs"></i> GỬI ĐÁNH GIÁ MÓN ĂN
                            </button>
                        </div>

                    </div>
                </form>
            @endif

        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentPageRating = 5;
    const pageRatingLabels = {
        1: '⭐ Rất tệ! Không hài lòng chút nào',
        2: '⭐⭐ Chưa hài lòng! Cần cải thiện chất lượng',
        3: '⭐⭐⭐ Bình thường! Tạm được',
        4: '⭐⭐⭐⭐ Hài lòng! Món ăn ngon miệng',
        5: '⭐⭐⭐⭐⭐ Tuyệt vời! Rất thơm ngon & hài lòng'
    };

    function selectPageRating(stars) {
        currentPageRating = stars;
        document.getElementById('page-rating-input').value = stars;
        updatePageStarDisplay(stars);
        document.getElementById('page-rating-label').innerText = pageRatingLabels[stars] || '';
    }

    function previewPageRating(stars) {
        updatePageStarDisplay(stars);
        document.getElementById('page-rating-label').innerText = pageRatingLabels[stars] || '';
    }

    function resetPageRatingPreview() {
        updatePageStarDisplay(currentPageRating);
        document.getElementById('page-rating-label').innerText = pageRatingLabels[currentPageRating] || '';
    }

    function updatePageStarDisplay(stars) {
        document.querySelectorAll('.page-star-btn').forEach(btn => {
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

    function appendTag(tagText) {
        const textarea = document.getElementById('page-review-comment');
        if (!textarea) return;
        const cleanTag = tagText.replace(/^[^\w\s]+\s*/, ''); // Remove leading emojis if any
        if (textarea.value.trim().length > 0) {
            textarea.value = textarea.value.trim() + ', ' + cleanTag;
        } else {
            textarea.value = cleanTag;
        }
    }
</script>
@endsection
