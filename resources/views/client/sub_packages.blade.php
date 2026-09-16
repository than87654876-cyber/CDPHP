@extends('layouts.app')

@section('title', 'Gói dịch vụ của tôi - FOODDAILY')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('trangchu') }}" class="inline-flex items-center gap-2 text-xs font-bold text-rose-500 hover:text-rose-600 mb-2 transition-colors">
                    <i class="fas fa-arrow-left"></i> Quay lại cửa hàng
                </a>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                    <i class="fas fa-box-open text-rose-500"></i> Gói dịch vụ ăn uống của tôi
                </h1>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2 mb-6">
                <i class="fas fa-circle-check text-emerald-500 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2 mb-6">
                <i class="fas fa-circle-exclamation text-rose-500 text-sm"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Subscriptions List -->
        <div class="space-y-6">
            @forelse($subscriptions as $subscription)
                <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-100 space-y-6">
                    <!-- Top Info -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800">
                                Gói: {{ $subscription->package->package_name }} <span class="text-xs font-bold text-slate-400">({{ $subscription->package->duration_days }} Ngày)</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-1 font-semibold">
                                Mã đơn đăng ký: <span class="text-rose-600 font-extrabold">#FDL-{{ $subscription->order_id }}</span>
                            </p>
                        </div>
                        <div>
                            @if($subscription->status === 'active')
                                <span class="px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-circle-check"></i> Đang hoạt động
                                </span>
                            @elseif($subscription->status === 'paused')
                                <span class="px-3.5 py-1.5 rounded-full bg-amber-50 text-amber-700 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-circle-pause"></i> Tạm ngưng
                                </span>
                            @elseif($subscription->status === 'expired')
                                <span class="px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-600 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-circle-xmark"></i> Hết hạn
                                </span>
                            @else
                                <span class="px-3.5 py-1.5 rounded-full bg-rose-50 text-rose-700 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-clock"></i> Chờ duyệt
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Date Progress Timeline -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs font-bold text-slate-700">
                        <div>
                            <i class="fas fa-calendar-day text-emerald-500 mr-1.5"></i>
                            Ngày bắt đầu: <span class="text-slate-900">{{ $subscription->start_date->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-calendar-check text-rose-500 mr-1.5"></i>
                            Ngày kết thúc: <span class="text-slate-900">{{ $subscription->end_date->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <!-- Details Footer -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
                        <div>
                            <span class="text-xs text-slate-400 font-medium">Giá trọn gói:</span>
                            <span class="text-xl font-extrabold text-rose-600 ml-2">{{ number_format($subscription->package->price, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-3xl shadow-sm border border-slate-100 space-y-3">
                    <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto text-2xl">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-500">Bạn chưa đăng ký gói dịch vụ nào.</p>
                    <a href="{{ route('trangchu') }}#thuc-don" class="inline-block px-6 py-2.5 rounded-xl food-gradient text-white text-xs font-bold shadow-md">
                        Khám phá gói ăn dài hạn
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
