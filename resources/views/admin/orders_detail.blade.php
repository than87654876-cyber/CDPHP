@extends('layouts.admin')

@section('title', 'Chi tiết Đơn hàng - FOODDAILY Admin')

@section('content')
@php
    $notes = $order->health_notes;
    $paymentMethodText = 'COD (Tiền mặt)';
    if ($order->payment_method === 'bank_transfer') {
        $paymentMethodText = 'Chuyển khoản VietQR';
    } elseif ($order->payment_method === 'momo') {
        $paymentMethodText = 'Ví điện tử MoMo';
    } elseif ($order->payment_method === 'vnpay') {
        $paymentMethodText = 'Cổng VNPAY';
    }
@endphp

<div class="max-w-6xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Chi Tiết Hóa Đơn #FDL-{{ $order->id }}</span>
                @if($order->order_status === 'completed')
                    <span class="text-xs px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-extrabold border border-emerald-200">
                        ✔ Đã hoàn thành
                    </span>
                @elseif($order->order_status === 'cancelled')
                    <span class="text-xs px-3 py-1 rounded-full bg-rose-50 text-rose-700 font-extrabold border border-rose-200">
                        ✕ Đã hủy đơn
                    </span>
                @elseif($order->order_status === 'delivering')
                    <span class="text-xs px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-extrabold border border-blue-200">
                        🛵 Đang giao hàng
                    </span>
                @elseif($order->order_status === 'preparing')
                    <span class="text-xs px-3 py-1 rounded-full bg-amber-50 text-amber-700 font-extrabold border border-amber-200 animate-pulse">
                        🔥 Đang chuẩn bị món
                    </span>
                @else
                    <span class="text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-extrabold border border-slate-200">
                        ⏳ {{ ucfirst($order->order_status) }}
                    </span>
                @endif
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Thời gian đặt: {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_donhang') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <a href="{{ route('donhang_chinhsua', $order->id) }}" class="px-4 py-2.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-pen-to-square"></i> Cập nhật trạng thái
            </a>
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

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer & Delivery Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-user"></i>
                </div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Thông tin khách hàng & Giao nhận</h3>
            </div>

            <div class="space-y-3.5 text-xs">
                <div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Khách hàng</p>
                    <p class="font-black text-slate-900 text-sm mt-0.5">{{ $order->user->fullname ?? 'Khách vãng lai' }}</p>
                    <p class="text-[11px] text-slate-400 font-medium">Mã thành viên: KH-{{ $order->user_id ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Số điện thoại</p>
                    <p class="font-extrabold text-slate-800 mt-0.5">{{ $order->user->phone ?? 'Chưa có SĐT' }}</p>
                </div>

                <div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Hình thức thanh toán</p>
                    <div class="mt-1">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-800 font-extrabold text-xs">
                            <i class="fas fa-wallet text-[#ee4d2d]"></i> {{ $paymentMethodText }}
                        </span>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Trạng thái thanh toán</p>
                    <div class="mt-1">
                        @if($order->payment_status === 'paid')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-extrabold text-xs border border-emerald-200">
                                ✔ Đã thanh toán
                            </span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-rose-50 text-rose-700 font-extrabold text-xs border border-rose-200">
                                ↺ Đã hoàn tiền
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-50 text-amber-700 font-extrabold text-xs border border-amber-200">
                                ⏳ Chờ thanh toán
                            </span>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Địa chỉ giao hàng & Ghi chú</p>
                    <div class="p-3 bg-slate-50 rounded-2xl text-xs font-medium text-slate-700 mt-1.5 leading-relaxed whitespace-pre-line border border-slate-100">
                        {{ $order->health_notes ?? 'Không có ghi chú thêm.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Ordered Items Table Card -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Sản phẩm thực tế trong đơn</h3>
                    </div>
                    <span class="text-xs font-bold text-slate-400">{{ count($order->orderItems) }} món</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider border-b border-slate-100">
                                <th class="py-3 px-4">Món ăn</th>
                                <th class="py-3 px-4 text-center">Số lượng</th>
                                <th class="py-3 px-4 text-right">Đơn giá</th>
                                <th class="py-3 px-4 text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($order->orderItems as $item)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-3">
                                            @if($item->dish && $item->dish->image_url)
                                                <img src="{{ Str::startsWith($item->dish->image_url, 'http') ? $item->dish->image_url : asset($item->dish->image_url) }}" alt="" class="w-10 h-10 rounded-xl object-cover border border-slate-100">
                                            @else
                                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-xs">
                                                    <i class="fas fa-bowl-food"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-extrabold text-slate-900">{{ $item->dish->dish_name ?? 'Món đã bị xóa' }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $item->dish->category->category_name ?? 'Thực đơn' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-black text-slate-800">
                                        x{{ $item->quantity }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right text-slate-600 font-semibold">
                                        {{ number_format($item->price, 0, ',', '.') }}đ
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Price Breakdown Summary Footer -->
            <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-500 font-medium">
                    <span>Tổng tiền món ăn:</span>
                    <span class="font-bold text-slate-800">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                </div>
                @if($order->coupon)
                    <div class="flex items-center justify-between text-emerald-600 font-medium">
                        <span>Mã giảm giá ({{ $order->coupon->coupon_code }}):</span>
                        <span class="font-bold">-{{ number_format($order->total_amount - $order->final_amount, 0, ',', '.') }}đ</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-100">
                    <span>Tổng thanh toán thực tế:</span>
                    <span class="text-rose-600 text-xl font-black">{{ number_format($order->final_amount, 0, ',', '.') }}đ</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
