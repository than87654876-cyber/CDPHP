<div class="dish-card-item bg-white/95 backdrop-blur-sm rounded-xl shadow-xs hover:shadow-md transition-all duration-300 border border-slate-200 overflow-hidden flex flex-col justify-between group {{ ($dIndex ?? 0) >= 15 ? 'hidden' : '' }}" 
     data-index="{{ $dIndex ?? 0 }}"
     data-dish-id="{{ $dish->id }}"
     data-category-id="{{ $dish->category_id }}"
     data-price="{{ $dish->price }}"
     data-name="{{ $dish->dish_name }}">
    <div class="space-y-2">
        <!-- Image Thumbnail with Rating Badge & Favorite Badge -->
        <a href="{{ route('dish.detail', $dish->id) }}" class="aspect-square bg-slate-100 relative overflow-hidden block">
            <img src="{{ $dish->display_image }}" alt="{{ $dish->dish_name }}" class="dish-card-img w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" decoding="async">
            
            <!-- Star Rating Badge on Top Right -->
            <span class="absolute top-2 right-2 bg-slate-900/80 backdrop-blur-xs text-amber-400 text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs flex items-center gap-1 border border-white/20">
                <i class="fas fa-star text-[9px] text-amber-400"></i>
                <span class="dish-rating-score-val">{{ number_format($dish->rating_score, 1) }}</span>
            </span>

            @if($dish->rating_score >= 4.9 || (($dIndex ?? 0) % 3 == 0))
                <span class="absolute top-2 left-0 bg-[#ee4d2d] text-white text-[9px] font-extrabold px-2 py-0.5 rounded-r-md shadow-xs flex items-center gap-1">
                    <i class="fas fa-thumbs-up text-[8px]"></i> Yêu thích
                </span>
            @endif
        </a>

        <!-- Content Details (Checkmark + Title + Address + Rating Stars + Promo Tag) -->
        <div class="p-2.5 space-y-1.5">
            <a href="{{ route('dish.detail', $dish->id) }}" class="flex items-start gap-1 group/title block">
                <span class="text-amber-500 text-xs shrink-0 mt-0.5">✔</span>
                <h4 class="dish-card-name font-extrabold text-slate-900 text-xs line-clamp-1 leading-snug group-hover/title:text-[#ee4d2d] transition-colors">
                    {{ $dish->dish_name }}
                </h4>
            </a>
            <p class="dish-card-desc text-[10px] text-slate-500 font-medium line-clamp-1 leading-tight">
                {{ $dish->description ?? 'FOODDAILY Store - TP. HCM' }}
            </p>
            
            <!-- Rating Stars & Review Count -->
            <div class="flex items-center gap-1.5 pt-0.5">
                <div class="flex items-center gap-1 text-[11px] font-black text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-md border border-amber-200/70">
                    <i class="fas fa-star text-[9px] text-amber-500"></i>
                    <span class="dish-rating-score-text">{{ number_format($dish->rating_score, 1) }}</span>
                </div>
                <span class="dish-reviews-count-text text-[10px] font-semibold text-slate-400">
                    {{ $dish->reviews_count > 0 ? "({$dish->reviews_count} đánh giá)" : '(Chưa có đánh giá)' }}
                </span>
            </div>

            <!-- Red Promo Tag Badge -->
            <div>
                <span class="inline-block px-1.5 py-0.5 rounded bg-rose-50 text-[#ee4d2d] text-[9px] font-extrabold border border-rose-200">
                    🏷️ Mã giảm {{ ((($dIndex ?? 0) % 2 == 0) ? '11%' : '20%') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Card Footer Price & Add Button -->
    <div class="p-2.5 pt-0 flex items-center justify-between border-t border-slate-100 mt-2">
        <span class="dish-card-price text-xs font-black text-slate-900">{{ number_format($dish->price) }}đ</span>
        <form action="{{ route('giohang.add') }}" method="POST">
            @csrf
            <input type="hidden" name="dish_id" value="{{ $dish->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="w-7 h-7 rounded-lg bg-[#ee4d2d] hover:bg-red-600 text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer" title="Thêm vào giỏ hàng">
                +
            </button>
        </form>
    </div>
</div>
