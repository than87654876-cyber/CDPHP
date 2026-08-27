@extends('layouts.app')

@section('title', 'Tra cứu đơn hàng - FOODDAILY')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Search Card -->
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-100 mb-8">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-5 mb-6">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold">
                    <i class="fas fa-search text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800">Tra cứu tiến độ đơn hàng</h2>
                    <p class="text-xs text-slate-500">Kiểm tra thông tin và vị trí giao hàng theo mã đơn của bạn</p>
                </div>
            </div>

            @if($error)
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl flex items-center gap-3 text-sm font-medium">
                    <i class="fas fa-triangle-exclamation text-rose-500 text-base"></i>
                    <span>{{ $error }}</span>
                </div>
            @endif

            <form action="{{ route('tracuu') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="order_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Mã đơn hàng <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="order_id" name="order_id" placeholder="Ví dụ: FDL-123" value="{{ $orderIdInput }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 text-sm font-semibold transition-all">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" placeholder="VD: 0901234567" value="{{ $phone }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 text-sm font-semibold transition-all">
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Địa chỉ Email</label>
                    <input type="email" id="email" name="email" placeholder="VD: name@example.com" value="{{ $email }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 text-sm font-semibold transition-all">
                </div>

                <div class="md:col-span-3 text-center mt-2">
                    <button type="submit" class="px-8 py-3.5 rounded-2xl food-gradient text-white font-extrabold text-sm shadow-lg shadow-rose-500/25 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 mx-auto">
                        <i class="fas fa-magnifying-glass"></i> Tra cứu ngay
                    </button>
                </div>
            </form>
        </div>

        @if($searched && $order)
            <!-- Progress Timeline Card -->
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-100 mb-8">
                <h3 class="text-base font-extrabold text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fas fa-truck-fast text-rose-500"></i> Tiến độ đơn hàng #FDL-{{ $order->id }}
                </h3>

                @php $status = $order->order_status; @endphp

                <div class="grid grid-cols-4 gap-2 relative my-6">
                    @if($status === 'cancelled')
                        <div class="col-span-4 bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-center font-bold text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-circle-xmark text-lg"></i> Đơn hàng đã bị hủy
                        </div>
                    @else
                        <!-- Step 1 -->
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center font-bold text-lg transition-all {{ in_array($status, ['pending', 'preparing', 'cooked', 'shipping', 'completed']) ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-slate-100 text-slate-400' }}">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <p class="text-xs font-bold mt-3 {{ $status === 'pending' ? 'text-rose-600' : 'text-slate-700' }}">1. Tiếp nhận</p>
                        </div>
                        <!-- Step 2 -->
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center font-bold text-lg transition-all {{ in_array($status, ['preparing', 'cooked', 'shipping', 'completed']) ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-slate-100 text-slate-400' }}">
                                <i class="fas fa-fire-burner"></i>
                            </div>
                            <p class="text-xs font-bold mt-3 {{ in_array($status, ['preparing', 'cooked']) ? 'text-rose-600' : 'text-slate-700' }}">2. Chế biến</p>
                        </div>
                        <!-- Step 3 -->
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center font-bold text-lg transition-all {{ in_array($status, ['shipping', 'completed']) ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-slate-100 text-slate-400' }}">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <p class="text-xs font-bold mt-3 {{ $status === 'shipping' ? 'text-rose-600' : 'text-slate-700' }}">3. Đang giao</p>
                        </div>
                        <!-- Step 4 -->
                        <div class="text-center">
                            <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center font-bold text-lg transition-all {{ $status === 'completed' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-slate-100 text-slate-400' }}">
                                <i class="fas fa-check"></i>
                            </div>
                            <p class="text-xs font-bold mt-3 {{ $status === 'completed' ? 'text-emerald-600' : 'text-slate-700' }}">4. Hoàn tất</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Details Card -->
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100 mb-6">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Thông tin nhận hàng</h4>
                        <p class="text-sm font-bold text-slate-800">{{ $order->user ? $order->user->fullname : 'Khách vãng lai' }}</p>
                        <p class="text-xs text-slate-500 mt-1">Ghi chú: {{ $order->health_notes ?? 'Không có' }}</p>
                    </div>
                    <div class="md:text-right">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Thanh toán</h4>
                        <p class="text-sm font-bold text-slate-800">
                            @if($order->payment_method === 'cash')
                                Tiền mặt (COD)
                            @elseif($order->payment_method === 'bank_transfer')
                                Chuyển khoản VietQR
                            @else
                                Ví MoMo
                            @endif
                        </p>
                        <div class="mt-1">
                            @if($order->payment_status === 'paid')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 font-extrabold text-xs rounded-full">Đã thanh toán</span>
                            @elseif($order->payment_status === 'refunded')
                                <span class="px-3 py-1 bg-rose-100 text-rose-700 font-extrabold text-xs rounded-full">Đã hoàn tiền</span>
                            @else
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 font-extrabold text-xs rounded-full">Chờ thanh toán</span>
                            @endif
                        </div>
                    </div>
                </div>

                <h4 class="text-sm font-bold text-slate-800 mb-4">Chi tiết món ăn</h4>
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
                            @foreach($order->orderItems as $item)
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
                                <td class="py-4 px-3 text-right text-rose-600 text-lg">{{ number_format($order->final_amount, 0, ',', '.') }}đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
@if(isset($order) && $order)
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const orderId = "{{ $order->id }}";
        const userEmail = "{{ $email ?? '' }}";
        const userPhone = "{{ $phone ?? '' }}";
        const lastStatus = "{{ $order->order_status }}";
        const lastPaymentStatus = "{{ $order->payment_status }}";

        function pollOrderStatus() {
            const queryParams = new URLSearchParams({
                order_id: orderId,
                last_status: lastStatus,
                last_payment_status: lastPaymentStatus
            });
            if (userEmail) queryParams.append('email', userEmail);
            if (userPhone) queryParams.append('phone', userPhone);

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

