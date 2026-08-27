@extends('layouts.app')

@section('title', 'Lịch sử đơn hàng - FOODDAILY')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Search -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <a href="{{ route('trangchu') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-600 hover:text-emerald-700 mb-2 transition-colors">
                    <i class="fas fa-arrow-left"></i> Quay lại trang chủ
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <i class="fas fa-receipt text-emerald-600"></i> Lịch sử đơn hàng của tôi
                </h1>
            </div>

            <!-- Search Form -->
            <form action="{{ route('giohang') }}" method="GET" class="flex gap-2 max-w-md w-full">
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm mã đơn, món ăn, địa chỉ..." class="flex-1 px-4 py-2.5 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 text-xs font-semibold transition-all outline-none">
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 hover:scale-105 transition-all">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Orders List -->
        <div class="space-y-6">
            @forelse($orders as $order)
                @php
                    $paymentMethodText = 'Tiền mặt (COD)';
                    if ($order->payment_method === 'bank_transfer') {
                        $paymentMethodText = 'Chuyển khoản VietQR';
                    } elseif ($order->payment_method === 'momo') {
                        $paymentMethodText = 'Ví điện tử MoMo';
                    }
                @endphp
                
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-md border border-slate-200/80 transition-all hover:shadow-xl" id="order-card-{{ $order->id }}">
                    <!-- Card Top Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3.5 py-1.5 rounded-full border border-emerald-100">
                                #FDL-{{ $order->id }}
                            </span>
                            <span class="text-xs font-medium text-slate-400">
                                <i class="far fa-clock mr-1"></i>{{ $order->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div>
                            @if($order->order_status === 'pending')
                                <span class="px-3.5 py-1.5 rounded-full bg-amber-50 border border-amber-100 text-amber-700 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-spinner fa-spin text-amber-500"></i> Chờ xác nhận
                                </span>
                            @elseif($order->order_status === 'confirmed')
                                <span class="px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-circle-check text-blue-500"></i> Đã xác nhận
                                </span>
                            @elseif($order->order_status === 'preparing')
                                <span class="px-3.5 py-1.5 rounded-full bg-amber-100 text-amber-800 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-fire-burner"></i> Đang chế biến
                                </span>
                            @elseif($order->order_status === 'delivering')
                                <span class="px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-motorcycle text-emerald-500"></i> Đang giao hàng
                                </span>
                            @elseif($order->order_status === 'completed')
                                <span class="px-3.5 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-circle-check text-emerald-600"></i> Đã hoàn thành
                                </span>
                            @elseif($order->order_status === 'cancelled')
                                <span class="px-3.5 py-1.5 rounded-full bg-rose-100 text-rose-800 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-circle-xmark text-rose-500"></i> Đã hủy
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details Content -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 my-6 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="md:col-span-2">
                            <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                <i class="fas fa-location-dot text-emerald-600 mr-1"></i> Giao đến
                            </h4>
                            <p class="text-sm font-bold text-slate-800">{{ $order->user->fullname ?? 'Khách hàng' }}</p>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $order->health_notes }}</p>
                        </div>
                        <div>
                            <h4 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                <i class="fas fa-wallet text-emerald-600 mr-1"></i> Thanh toán
                            </h4>
                            <p class="text-sm font-bold text-slate-800">{{ $paymentMethodText }}</p>
                            <div class="mt-1">
                                @if($order->payment_status === 'pending')
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-extrabold">Chờ thanh toán</span>
                                @elseif($order->payment_status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-extrabold">Đã thanh toán</span>
                                @elseif($order->payment_status === 'refunded')
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[11px] font-extrabold">Đã hoàn tiền</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Total & Actions Footer -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-medium">Tổng tiền:</span>
                            <span class="text-lg font-extrabold text-slate-900">{{ number_format($order->total_price ?? 0) }}đ</span>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            @if($order->payment_status === 'pending' && $order->order_status === 'pending' && $order->payment_method === 'bank_transfer')
                                <a href="{{ route('muahang.thanhtoan', ['id' => $order->id]) }}" class="px-4 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-sm">
                                    <i class="fas fa-qrcode mr-1"></i> Quét mã VietQR
                                </a>
                            @endif

                            @if(in_array($order->order_status, ['pending', 'confirmed']))
                                <form action="{{ route('order.cancel') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <button type="submit" class="px-4 py-2 rounded-2xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200/80 text-xs font-bold transition-all">
                                        Hủy đơn
                                    </button>
                                </form>
                            @endif

                            @if($order->order_status === 'completed')
                                <a href="{{ route('yeucauhoan') }}" class="px-4 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                                    <i class="fas fa-rotate-left mr-1"></i> Yêu cầu hoàn tiền
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-sm space-y-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Chưa có đơn hàng nào</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Bạn chưa đặt đơn hàng nào hoặc tìm kiếm không khớp. Khám phá menu món ăn tươi ngon ngay hôm nay!</p>
                    <a href="{{ route('trangchu') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-md shadow-emerald-500/20 transition-all">
                        Khám phá thực đơn
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let orderStates = {};

        // Fetch initial order states
        fetch("{{ route('api.client.orders.poll') }}")
            .then(res => res.json())
            .then(data => {
                if (data.orders) {
                    data.orders.forEach(o => {
                        orderStates[o.id] = o.order_status;
                    });
                }
                setInterval(checkUserOrdersRealtime, 3000);
            });

        function checkUserOrdersRealtime() {
            fetch("{{ route('api.client.orders.poll') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.orders) {
                        data.orders.forEach(o => {
                            if (orderStates[o.id] && orderStates[o.id] !== o.order_status) {
                                let statusText = 'Cập nhật';
                                if (o.order_status === 'preparing') statusText = 'Đang chuẩn bị món';
                                else if (o.order_status === 'delivering') statusText = 'Shipper đang giao hàng';
                                else if (o.order_status === 'completed') statusText = 'Đã giao thành công';
                                else if (o.order_status === 'cancelled') statusText = 'Đã hủy';

                                alert(`🔔 [Realtime Notification] Đơn hàng #FDL-${o.id} của bạn vừa chuyển sang trạng thái: ${statusText}!`);
                                window.location.reload();
                            }
                            orderStates[o.id] = o.order_status;
                        });
                    }
                })
                .catch(err => console.error('Error polling user orders:', err));
        }
    });
</script>
@endsection
