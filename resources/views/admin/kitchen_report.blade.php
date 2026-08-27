@extends('layouts.admin')

@section('title', 'Màn Hình Bếp Nấu - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fas fa-fire-burner text-amber-500"></i> Bảng Chuẩn Bị Món Ăn Bếp Nấu
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Tổng hợp số lượng suất ăn lẻ & gói combo cần chế biến trong ngày</p>
        </div>

        <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-2xl border border-slate-200 text-xs font-bold text-slate-700 shadow-xs">
            <i class="far fa-calendar-alt text-[#ee4d2d]"></i> Ngày giao: {{ \Carbon\Carbon::parse($todayStr)->format('d/m/Y') }}
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Panel 1: Single Dishes -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 bg-rose-50 border-b border-rose-100 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-utensils text-[#ee4d2d]"></i> Món Ăn Cho Đơn Hàng Lẻ
                    </h3>
                    <span class="px-3 py-1 rounded-full bg-[#ee4d2d] text-white text-xs font-black">
                        {{ $singleDishes->sum('total_qty') }} Suất
                    </span>
                </div>

                <div class="p-6">
                    @if($singleDishes->isEmpty())
                        <div class="text-center py-10 space-y-2">
                            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold mx-auto">
                                <i class="fas fa-check"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700">Đã chế biến hoàn tất!</p>
                            <p class="text-[11px] text-slate-400">Không có đơn lẻ nào cần làm lúc này.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                                        <th class="py-3.5 px-4">Tên Món Ăn</th>
                                        <th class="py-3.5 px-4 text-center">Số Lượng Cần Làm</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($singleDishes as $item)
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-3.5 px-4 font-extrabold text-slate-900">
                                                {{ $item->dish->dish_name ?? 'Món ăn không tồn tại' }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                <span class="px-3 py-1 rounded-xl bg-rose-50 text-[#ee4d2d] text-xs font-black border border-rose-200">
                                                    {{ $item->total_qty }} suất
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel 2: Subscriptions -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-5 bg-amber-50 border-b border-amber-100 flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-box-archive text-amber-600"></i> Món Ăn Cho Gói Combo Định Kỳ
                    </h3>
                    <span class="px-3 py-1 rounded-full bg-amber-500 text-white text-xs font-black">
                        {{ $subscriptionDishes->sum('total_qty') }} Suất
                    </span>
                </div>

                <div class="p-6">
                    @if($subscriptionDishes->isEmpty())
                        <div class="text-center py-10 space-y-2">
                            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold mx-auto">
                                <i class="fas fa-check"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700">Đã nấu xong gói định kỳ!</p>
                            <p class="text-[11px] text-slate-400">Không có lịch giao gói combo nào hôm nay.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                                        <th class="py-3.5 px-4">Tên Món Ăn</th>
                                        <th class="py-3.5 px-4 text-center">Số Lượng Cần Làm</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($subscriptionDishes as $item)
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-3.5 px-4 font-extrabold text-slate-900">
                                                {{ $item->dish->dish_name ?? 'Món ăn không tồn tại' }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center">
                                                <span class="px-3 py-1 rounded-xl bg-amber-50 text-amber-700 text-xs font-black border border-amber-200">
                                                    {{ $item->total_qty }} suất
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let lastCheckedTime = null;

        fetch("{{ route('api.orders.poll') }}")
            .then(response => response.json())
            .then(data => {
                lastCheckedTime = data.timestamp;
                setInterval(pollKitchenUpdates, 2500);
            })
            .catch(err => console.error('Error initializing kitchen polling:', err));

        function pollKitchenUpdates() {
            if (!lastCheckedTime) return;

            fetch(`{{ route('api.orders.poll') }}?since=${encodeURIComponent(lastCheckedTime)}`)
                .then(response => response.json())
                .then(data => {
                    lastCheckedTime = data.timestamp;
                    if (data.updates && data.updates.length > 0) {
                        let shouldReload = false;
                        data.updates.forEach(e => {
                            if (e.action === 'created' || e.action === 'status_updated' || e.action === 'daily_dispatch') {
                                shouldReload = true;
                            }
                        });

                        if (shouldReload) {
                            window.location.reload();
                        }
                    }
                })
                .catch(err => console.error('Error during kitchen polling:', err));
        }
    });
</script>
@endsection
