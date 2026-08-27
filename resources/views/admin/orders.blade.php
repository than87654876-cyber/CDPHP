@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Danh Sách Đơn Hàng</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Cập nhật thời gian thực trạng thái chế biến & thanh toán</p>
        </div>
    </div>

    <!-- Alert Flash Notifications -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs text-xs font-semibold">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-2 font-medium"><i class="fas fa-circle-exclamation text-rose-500"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Orders Data Card Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-receipt text-[#ee4d2d]"></i> Đơn hàng vừa phát sinh
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="py-4 px-6">Mã đơn</th>
                        <th class="py-4 px-6">Món ăn đặt</th>
                        <th class="py-4 px-6">Khách hàng</th>
                        <th class="py-4 px-6">Thanh toán</th>
                        <th class="py-4 px-6">Trạng thái đơn</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($orders as $order)
                        @php
                            $paymentMethodText = 'COD';
                            if ($order->payment_method === 'bank_transfer') {
                                $paymentMethodText = 'VietQR';
                            } elseif ($order->payment_method === 'momo') {
                                $paymentMethodText = 'MoMo';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Mã đơn -->
                            <td class="py-4 px-6 font-extrabold text-emerald-700">
                                #FDL-{{ $order->id }}
                            </td>
                            <!-- Sản phẩm -->
                            <td class="py-4 px-6 max-w-xs">
                                <p class="font-bold text-slate-900 truncate">
                                    @foreach($order->orderItems as $item)
                                        {{ $item->dish->dish_name ?? 'Món ăn' }} <span class="text-emerald-600 font-extrabold">x{{ $item->quantity }}</span>{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                                <p class="text-[11px] text-slate-400 mt-0.5 font-medium">{{ number_format($order->final_amount ?? $order->total_price ?? 0) }}đ</p>
                            </td>
                            <!-- Khách hàng -->
                            <td class="py-4 px-6">
                                <p class="font-bold text-slate-800 text-xs">{{ $order->user->fullname ?? 'Khách vãng lai' }}</p>
                                <p class="text-[11px] text-slate-400 font-medium">{{ $order->user->phone ?? 'Không có SĐT' }}</p>
                            </td>
                            <!-- Trạng thái Thanh toán -->
                            <td class="py-4 px-6">
                                <select class="payment-status-select px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold transition-all shadow-xs focus:ring-2 focus:ring-emerald-500/20 bg-slate-50 outline-none" 
                                        data-order-id="{{ $order->id }}" 
                                        data-current-val="{{ $order->payment_status }}">
                                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thất bại</option>
                                    <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                                </select>
                                <div class="text-[10px] text-slate-400 font-bold mt-1 uppercase">Hình thức: {{ $paymentMethodText }}</div>
                            </td>
                            <!-- Trạng thái Đơn hàng -->
                            <td class="py-4 px-6">
                                <select class="order-status-select px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold transition-all shadow-xs focus:ring-2 focus:ring-emerald-500/20 bg-slate-50 outline-none" 
                                        data-order-id="{{ $order->id }}" 
                                        data-current-val="{{ $order->order_status }}">
                                    <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }}>🔥 Đang chuẩn bị</option>
                                    <option value="delivering" {{ $order->order_status === 'delivering' ? 'selected' : '' }}>🚚 Đang giao hàng</option>
                                    <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>✅ Đã giao hàng</option>
                                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>❌ Đã hủy</option>
                                </select>
                            </td>
                            <!-- Thao tác -->
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('donhang_xem', ['id' => $order->id]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition-colors">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.order-status-select').forEach(select => {
        select.addEventListener('change', function () {
            const orderId = this.dataset.orderId;
            const newStatus = this.value;
            fetch(`/donhang_chinhsua/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order_status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) alert('Cập nhật trạng thái đơn thành công!');
            })
            .catch(err => console.error(err));
        });
    });
</script>
@endsection
