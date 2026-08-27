@extends('layouts.app')

@section('title', 'Lịch Sử Đơn Hàng Của Tôi - FOODDAILY')

@section('content')
<div class="py-8 bg-[#f5f5f5] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Search Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('trangchu') }}" class="hover:text-[#ee4d2d]">Trang chủ</a>
                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                    <span class="text-[#ee4d2d] font-bold">Lịch sử đơn hàng</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fas fa-receipt text-[#ee4d2d]"></i> Đơn Hàng Của Tôi
                </h1>
            </div>

            <!-- Search Form -->
            <form action="{{ route('giohang') }}" method="GET" class="flex gap-2 max-w-md w-full bg-white p-1 rounded-xl shadow-xs border border-slate-200">
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm mã đơn #FDL-, tên món ăn..." class="flex-1 px-4 py-2 text-xs font-semibold text-slate-800 outline-none">
                <button type="submit" class="px-5 py-2 bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs rounded-lg transition-colors shadow-xs">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </form>
        </div>

        <!-- Orders List -->
        <div class="space-y-4">
            @forelse($orders as $order)
                @php
                    $paymentMethodText = 'Tiền mặt (COD)';
                    if ($order->payment_method === 'bank_transfer') {
                        $paymentMethodText = 'Chuyển khoản VietQR';
                    } elseif ($order->payment_method === 'momo') {
                        $paymentMethodText = 'Ví MoMo';
                    }
                @endphp
                
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200 space-y-4 hover:shadow-md transition-all" id="order-card-{{ $order->id }}">
                    <!-- Card Top Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-black uppercase tracking-wider text-[#ee4d2d] bg-rose-50 px-3 py-1 rounded-full border border-rose-100">
                                #FDL-{{ $order->id }}
                            </span>
                            <span class="text-xs font-bold text-slate-500">
                                <i class="far fa-clock mr-1 text-slate-400"></i>{{ $order->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <div>
                            @if($order->order_status === 'pending')
                                <span class="px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-spinner fa-spin text-amber-500"></i> Chờ xác nhận
                                </span>
                            @elseif($order->order_status === 'confirmed')
                                <span class="px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-circle-check text-blue-500"></i> Đã xác nhận
                                </span>
                            @elseif($order->order_status === 'preparing')
                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-fire-burner"></i> Bếp đang chế biến
                                </span>
                            @elseif($order->order_status === 'delivering')
                                <span class="px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-motorcycle text-emerald-600"></i> Shipper đang giao
                                </span>
                            @elseif($order->order_status === 'completed')
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-circle-check text-emerald-600"></i> Đã hoàn thành
                                </span>
                            @elseif($order->order_status === 'cancelled')
                                <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-extrabold inline-flex items-center gap-1.5">
                                    <i class="fas fa-circle-xmark text-rose-500"></i> Đã hủy
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Items Detail -->
                    <div class="space-y-3">
                        @foreach($order->orderItems as $oItem)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-slate-100 overflow-hidden shrink-0 border border-slate-100">
                                        @if($oItem->dish && $oItem->dish->image)
                                            <img src="{{ asset($oItem->dish->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-utensils"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-slate-900">{{ $oItem->dish->dish_name ?? 'Món ăn' }}</h4>
                                        <p class="text-[11px] text-slate-400 font-medium">Số lượng: x{{ $oItem->quantity }}</p>
                                    </div>
                                </div>
                                <span class="font-bold text-slate-800">{{ number_format($oItem->price * $oItem->quantity) }}đ</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Order Total & Actions -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-medium">Tổng thanh toán:</span>
                            <span class="text-base font-black text-[#ee4d2d]">{{ number_format($order->final_amount ?? 0) }}đ</span>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            @if($order->payment_status === 'pending' && $order->payment_method === 'bank_transfer')
                                <a href="{{ route('muahang.thanhtoan', ['id' => $order->id]) }}" class="px-4 py-1.5 rounded-lg bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-bold transition-all shadow-xs">
                                    <i class="fas fa-qrcode mr-1"></i> VietQR Thanh toán
                                </a>
                            @endif

                            @if(in_array($order->order_status, ['pending', 'confirmed']))
                                <form action="{{ route('order.cancel') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 border border-slate-200 text-xs font-bold transition-all">
                                        Hủy đơn
                                    </button>
                                </form>
                            @endif

                            @if($order->order_status === 'completed')
                                <a href="{{ route('yeucauhoan') }}" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                                    <i class="fas fa-rotate-left mr-1"></i> Yêu cầu hoàn tiền
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm space-y-4">
                    <div class="w-16 h-16 rounded-full bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-2xl mx-auto">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3 class="text-base font-extrabold text-slate-800">Chưa Có Đơn Hàng Nào</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Bạn chưa đặt đơn hàng nào hoặc tìm kiếm không khớp. Khám phá menu món ăn tươi ngon ngay hôm nay!</p>
                    <a href="{{ route('trangchu') }}#menu" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-extrabold shadow-sm transition-all">
                        Khám phá thực đơn ngay
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

        fetch("{{ route('api.orders.poll') }}")
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
            fetch("{{ route('api.orders.poll') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.orders) {
                        data.orders.forEach(o => {
                            if (orderStates[o.id] && orderStates[o.id] !== o.order_status) {
                                let statusText = 'Cập nhật';
                                if (o.order_status === 'preparing') statusText = 'Bếp đang chế biến món';
                                else if (o.order_status === 'delivering') statusText = 'Shipper đang giao hàng';
                                else if (o.order_status === 'completed') statusText = 'Đã giao thành công';
                                else if (o.order_status === 'cancelled') statusText = 'Đã hủy';

                                alert(`🔔 [FOODDAILY Notification] Đơn hàng #FDL-${o.id} của bạn vừa chuyển sang trạng thái: ${statusText}!`);
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
