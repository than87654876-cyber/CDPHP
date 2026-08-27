@extends('layouts.app')

@section('title', 'Danh sách đơn hàng & Tiến độ - FOODDAILY')

@section('content')
<div class="py-12 bg-transparent min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Title Banner -->
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center font-bold text-xl shadow-xs">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Đơn hàng của bạn</h2>
                        <p class="text-xs text-slate-500">Xem danh sách các đơn hàng đã đặt và theo dõi tiến độ giao hàng hỏa tốc</p>
                    </div>
                </div>
                <!-- Quick Search Toggle for Guests -->
                <button type="button" onclick="toggleGuestLookup()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-1.5">
                    <i class="fas fa-search"></i> <span>Tra cứu mã khác</span>
                </button>
            </div>

            <!-- Optional Guest Lookup Form -->
            <form id="guest-lookup-form" action="{{ route('tracuu') }}" method="GET" class="hidden mt-6 pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="text" name="order_id" placeholder="Mã đơn (Ví dụ: FDL-12)" value="{{ $orderIdInput }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]">
                <input type="tel" name="phone" placeholder="Số điện thoại" value="{{ $phone }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]">
                <button type="submit" class="px-5 py-2.5 bg-[#ee4d2d] text-white rounded-xl font-extrabold text-xs hover:bg-red-600 transition-colors">
                    Tìm kiếm ngay
                </button>
            </form>
        </div>

        <!-- 1. DANH SÁCH CÁC ĐƠN HÀNG ĐÃ MUA (LIST ORDER) -->
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list-check text-[#ee4d2d]"></i> Danh sách đơn hàng đã mua ({{ $myOrders->count() }})
            </h3>

            @if($myOrders->isNotEmpty())
                <div class="space-y-3">
                    @foreach($myOrders as $ord)
                        @php
                            $isSelected = $selectedOrder && ($selectedOrder->id === $ord->id);
                        @endphp
                        <div class="p-4 rounded-2xl border transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 {{ $isSelected ? 'bg-rose-50/60 border-[#ee4d2d] shadow-sm' : 'bg-white border-slate-200 hover:border-slate-300' }}">
                            
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-900 text-sm">#FDL-{{ $ord->id }}</span>
                                    <span class="text-[11px] text-slate-400 font-medium">• {{ $ord->created_at ? $ord->created_at->format('H:i d/m/Y') : 'Vừa xong' }}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="font-extrabold text-[#ee4d2d] text-sm">{{ number_format($ord->final_amount, 0, ',', '.') }}đ</span>
                                    <span class="text-slate-300">|</span>
                                    
                                    <!-- Payment Badge -->
                                    @if($ord->payment_status === 'paid')
                                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 font-extrabold text-[10px] rounded-full">Đã thanh toán</span>
                                    @elseif($ord->payment_status === 'refunded')
                                        <span class="px-2.5 py-0.5 bg-rose-100 text-rose-700 font-extrabold text-[10px] rounded-full">Đã hoàn tiền</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-amber-100 text-amber-700 font-extrabold text-[10px] rounded-full">Chờ thanh toán</span>
                                    @endif

                                    <!-- Status Badge -->
                                    @php $st = $ord->order_status; @endphp
                                    @if($st === 'pending')
                                        <span class="px-2.5 py-0.5 bg-blue-50 text-blue-600 font-bold text-[10px] rounded-full">Tiếp nhận</span>
                                    @elseif(in_array($st, ['preparing', 'cooked']))
                                        <span class="px-2.5 py-0.5 bg-purple-50 text-purple-600 font-bold text-[10px] rounded-full">Đang chế biến</span>
                                    @elseif($st === 'shipping')
                                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-600 font-bold text-[10px] rounded-full">Đang giao</span>
                                    @elseif($st === 'completed')
                                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-600 font-bold text-[10px] rounded-full">Hoàn tất</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-slate-100 text-slate-500 font-bold text-[10px] rounded-full">Đã hủy</span>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('tracuu', ['order_id' => 'FDL-' . $ord->id]) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $isSelected ? 'bg-[#ee4d2d] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-[#ee4d2d] hover:text-white' }}">
                                <i class="fas fa-eye text-[11px]"></i> <span>Xem tiến độ</span>
                            </a>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-slate-400 space-y-3">
                    <i class="fas fa-basket-shopping text-4xl text-slate-300"></i>
                    <p class="text-xs font-semibold">Bạn chưa có đơn hàng nào. Hãy đặt món ngay để thưởng thức nhé!</p>
                    <a href="{{ route('trangchu') }}" class="inline-block px-5 py-2.5 bg-[#ee4d2d] text-white rounded-xl font-extrabold text-xs shadow-md">Khám phá menu món ăn</a>
                </div>
            @endif
        </div>

        <!-- 2. CHI TIẾT & TIẾN ĐỘ ĐƠN HÀNG ĐƯỢC CHỌN -->
        @if($selectedOrder)
            <div id="order-details" class="space-y-6">
                <!-- Progress Timeline Card -->
                <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                        <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-truck-fast text-[#ee4d2d]"></i> Tiến độ giao hàng đơn #FDL-{{ $selectedOrder->id }}
                        </h3>
                        <span class="text-xs font-bold text-slate-400">{{ $selectedOrder->created_at ? $selectedOrder->created_at->format('H:i - d/m/Y') : '' }}</span>
                    </div>

                    @php $status = $selectedOrder->order_status; @endphp

                    <div class="grid grid-cols-4 gap-2 relative my-6">
                        @if($status === 'cancelled')
                            <div class="col-span-4 bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-center font-bold text-sm flex items-center justify-center gap-2">
                                <i class="fas fa-circle-xmark text-lg"></i> Đơn hàng đã bị hủy
                            </div>
                        @else
                            <!-- Step 1 -->
                            <div class="text-center">
                                <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center font-bold text-lg transition-all {{ in_array($status, ['pending', 'preparing', 'cooked', 'shipping', 'completed']) ? 'bg-emerald-500 text-white shadow-md' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <p class="text-xs font-bold mt-3 {{ $status === 'pending' ? 'text-[#ee4d2d]' : 'text-slate-700' }}">1. Tiếp nhận</p>
                            </div>
                            <!-- Step 2 -->
                            <div class="text-center">
                                <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center font-bold text-lg transition-all {{ in_array($status, ['preparing', 'cooked', 'shipping', 'completed']) ? 'bg-emerald-500 text-white shadow-md' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="fas fa-fire-burner"></i>
                                </div>
                                <p class="text-xs font-bold mt-3 {{ in_array($status, ['preparing', 'cooked']) ? 'text-[#ee4d2d]' : 'text-slate-700' }}">2. Chế biến</p>
                            </div>
                            <!-- Step 3 -->
                            <div class="text-center">
                                <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center font-bold text-lg transition-all {{ in_array($status, ['shipping', 'completed']) ? 'bg-emerald-500 text-white shadow-md' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="fas fa-motorcycle"></i>
                                </div>
                                <p class="text-xs font-bold mt-3 {{ $status === 'shipping' ? 'text-[#ee4d2d]' : 'text-slate-700' }}">3. Đang giao</p>
                            </div>
                            <!-- Step 4 -->
                            <div class="text-center">
                                <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center font-bold text-lg transition-all {{ $status === 'completed' ? 'bg-emerald-500 text-white shadow-md' : 'bg-slate-100 text-slate-400' }}">
                                    <i class="fas fa-check"></i>
                                </div>
                                <p class="text-xs font-bold mt-3 {{ $status === 'completed' ? 'text-emerald-600' : 'text-slate-700' }}">4. Hoàn tất</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Order Details Card -->
                <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100 mb-6">
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Thông tin nhận hàng</h4>
                            <p class="text-sm font-bold text-slate-800">{{ $selectedOrder->user ? $selectedOrder->user->fullname : 'Khách vãng lai' }}</p>
                            <p class="text-xs text-slate-500 mt-1">Ghi chú: {{ $selectedOrder->health_notes ?? 'Không có' }}</p>
                        </div>
                        <div class="md:text-right">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Thanh toán</h4>
                            <p class="text-sm font-bold text-slate-800">
                                @if($selectedOrder->payment_method === 'cash')
                                    Tiền mặt khi nhận hàng (COD)
                                @elseif($selectedOrder->payment_method === 'bank_transfer')
                                    Chuyển khoản VietQR Hỏa Tốc
                                @else
                                    Ví MoMo
                                @endif
                            </p>
                            <div class="mt-1">
                                @if($selectedOrder->payment_status === 'paid')
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 font-extrabold text-xs rounded-full">Đã thanh toán</span>
                                @elseif($selectedOrder->payment_status === 'refunded')
                                    <span class="px-3 py-1 bg-rose-100 text-rose-700 font-extrabold text-xs rounded-full">Đã hoàn tiền</span>
                                @else
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 font-extrabold text-xs rounded-full">Chờ thanh toán</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <h4 class="text-sm font-bold text-slate-800 mb-4">Danh sách món ăn chi tiết</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-xs text-slate-400 uppercase font-bold">
                                    <th class="py-3">Món ăn</th>
                                    <th class="py-3 text-center">Số lượng</th>
                                    <th class="py-3 text-right">Đơn giá</th>
                                    <th class="py-3 text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($selectedOrder->orderItems as $item)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-800">
                                        {{ $item->dish ? $item->dish->dish_name : 'Món ăn không tồn tại' }}
                                    </td>
                                    <td class="py-3 text-center font-bold">{{ $item->quantity }}</td>
                                    <td class="py-3 text-right text-slate-600">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                    <td class="py-3 text-right font-bold text-slate-800">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</td>
                                </tr>
                                @endforeach
                                <tr class="bg-slate-50 font-extrabold">
                                    <td colspan="3" class="py-4 px-3 text-right text-slate-700">Tổng thanh toán:</td>
                                    <td class="py-4 px-3 text-right text-[#ee4d2d] text-lg">{{ number_format($selectedOrder->final_amount, 0, ',', '.') }}đ</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
@if(isset($selectedOrder) && $selectedOrder)
<script>
    function toggleGuestLookup() {
        const form = document.getElementById('guest-lookup-form');
        if (form) form.classList.toggle('hidden');
    }

    document.addEventListener("DOMContentLoaded", function () {
        const orderId = "{{ $selectedOrder->id }}";
        const lastStatus = "{{ $selectedOrder->order_status }}";
        const lastPaymentStatus = "{{ $selectedOrder->payment_status }}";

        function pollOrderStatus() {
            const queryParams = new URLSearchParams({
                order_id: orderId,
                last_status: lastStatus,
                last_payment_status: lastPaymentStatus
            });

            fetch(`{{ route('api.orders.track.poll') }}?${queryParams.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.changed) {
                        window.location.reload();
                    }
                })
                .catch(err => console.error('Error polling order status:', err));
        }
        setInterval(pollOrderStatus, 2000);
    });
</script>
@endif
@endsection
