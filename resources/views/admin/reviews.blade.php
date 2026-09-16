@extends('layouts.admin')

@section('title', 'Quản lý Đánh giá - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Đánh Giá Chất Lượng Dịch Vụ</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-600 font-extrabold border border-amber-200">
                    {{ count($reviews) }} lượt nhận xét
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Lắng nghe ý kiến phản hồi và chất lượng trải nghiệm của khách hàng</p>
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

    <!-- STATS CARDS -->
    @php
        $totalReviews = count($reviews);
        $avgRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 5.0;
        $fiveStarCount = $reviews->where('rating', 5)->count();
        $fiveStarPercent = $totalReviews > 0 ? round(($fiveStarCount / $totalReviews) * 100) : 100;
        $withCommentCount = $reviews->filter(fn($r) => !empty($r->comment))->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl font-bold border border-amber-100">
                <i class="fas fa-star"></i>
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Điểm trung bình</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5 flex items-center gap-1.5">
                    <span>{{ $avgRating }}</span>
                    <span class="text-xs text-amber-500 font-bold">/ 5.0 ★</span>
                </h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100">
                <i class="fas fa-comments"></i>
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Tổng lượt đánh giá</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">{{ $totalReviews }} lượt</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-xl font-bold border border-rose-100">
                <i class="fas fa-heart"></i>
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Tỷ lệ 5 sao</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">{{ $fiveStarPercent }}% <span class="text-xs text-slate-400 font-normal">({{ $fiveStarCount }})</span></h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold border border-blue-100">
                <i class="fas fa-quote-left"></i>
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Có nhận xét chi tiết</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">{{ $withCommentCount }} phản hồi</h3>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <!-- Card Filter Header -->
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="text-sm font-extrabold text-slate-900">Danh sách phản hồi & nhận xét từ khách hàng</h3>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs pointer-events-none">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="reviewSearch" placeholder="Tìm theo tên, nhận xét, mã đơn..." onkeyup="filterReviewsTable()" class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:border-[#ee4d2d] outline-none transition-all">
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="reviewTable">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="py-4 px-6 w-16 text-center">STT</th>
                        <th class="py-4 px-6">Khách hàng</th>
                        <th class="py-4 px-6">Mã đơn</th>
                        <th class="py-4 px-6">Đánh giá</th>
                        <th class="py-4 px-6">Nội dung nhận xét</th>
                        <th class="py-4 px-6">Ngày gửi</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reviews as $index => $review)
                        <tr class="review-row hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-center font-bold text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-extrabold flex items-center justify-center text-xs">
                                        {{ mb_substr($review->user->fullname ?? 'K', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 text-xs">{{ $review->user->fullname ?? 'Khách vãng lai' }}</p>
                                        <p class="text-[11px] text-slate-400 font-medium">{{ $review->user->phone ?? 'Chưa có SĐT' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if($review->order_id)
                                    <a href="{{ route('donhang_xem', $review->order_id) }}" class="inline-flex items-center gap-1 font-extrabold text-emerald-700 hover:underline bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                        #FDL-{{ $review->order_id }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Đánh giá chung</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-1 text-amber-400 text-xs">
                                    @for($s = 1; $s <= 5; $s++)
                                        @if($s <= $review->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star text-slate-200"></i>
                                        @endif
                                    @endfor
                                    <span class="font-black text-slate-700 text-xs ml-1">({{ $review->rating }}/5)</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 max-w-sm">
                                @if($review->comment)
                                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-2.5 text-xs text-slate-700 font-medium leading-relaxed">
                                        <i class="fas fa-quote-left text-[10px] text-slate-300 mr-1"></i>
                                        {{ $review->comment }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">(Không để lại nhận xét)</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-medium whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-xs text-slate-600 font-semibold">
                                    <i class="far fa-clock text-slate-400 text-[11px]"></i>
                                    <span>{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <form action="{{ route('quanly_reviews.xoa', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này không?');" class="inline-block">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition-all cursor-pointer shadow-2xs" title="Xóa đánh giá">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto">
                                        <i class="fas fa-star-half-stroke"></i>
                                    </div>
                                    <h4 class="font-extrabold text-slate-800 text-sm">Chưa có đánh giá nào từ khách hàng</h4>
                                    <p class="text-xs text-slate-400">Các đánh giá mới của khách hàng sau khi hoàn tất đơn sẽ hiển thị tại đây.</p>
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
    function filterReviewsTable() {
        const input = document.getElementById('reviewSearch');
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('.review-row');

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
