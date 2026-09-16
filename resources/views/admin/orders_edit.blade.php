@extends('layouts.admin')

@section('title', 'Cập nhật Đơn hàng - FOODDAILY Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Cập Nhật Đơn Hàng</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-rose-50 text-[#ee4d2d] font-extrabold border border-rose-200">
                    #FDL-{{ $order->id }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Điều phối tiến trình xử lý, trạng thái chế biến và thanh toán đơn hàng</p>
        </div>
        <a href="{{ route('donhang_xem', $order->id) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại chi tiết
        </a>
    </div>

    <!-- Error Alerts -->
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-2 font-medium"><i class="fas fa-circle-exclamation text-rose-500"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-base font-bold shadow-xs">
                <i class="fas fa-sliders"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Biểu mẫu điều phối tiến trình đơn</h3>
                <p class="text-[11px] text-slate-400 font-medium">Khách hàng: <strong class="text-slate-800">{{ $order->user->fullname ?? 'Khách vãng lai' }}</strong> • Tổng tiền: <strong class="text-rose-600">{{ number_format($order->final_amount, 0, ',', '.') }}đ</strong></p>
            </div>
        </div>

        <form action="{{ route('donhang_chinhsua.post', $order->id) }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Trạng thái đơn hàng -->
                <div class="space-y-2">
                    <label for="order_status" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Trạng thái đơn hàng <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <select id="order_status" name="order_status" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                        <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>⏳ Chờ xác nhận đơn (Pending)</option>
                        <option value="confirmed" {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>✔ Đã xác nhận đơn (Confirmed)</option>
                        <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }}>🔥 Đang chuẩn bị món (Preparing)</option>
                        <option value="delivering" {{ $order->order_status === 'delivering' ? 'selected' : '' }}>🛵 Đang giao hàng (Delivering)</option>
                        <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>🎉 Đã hoàn thành (Completed)</option>
                        <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>✕ Hủy đơn hàng (Cancelled)</option>
                    </select>
                </div>

                <!-- Trạng thái thanh toán -->
                <div class="space-y-2">
                    <label for="payment_status" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Trạng thái thanh toán <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <select id="payment_status" name="payment_status" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>⏳ Chờ thanh toán (Pending)</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>✔ Đã thanh toán (Paid)</option>
                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>✕ Thanh toán thất bại (Failed)</option>
                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>↺ Đã hoàn tiền (Refunded)</option>
                    </select>
                </div>

                <!-- Ghi chú điều phối -->
                <div class="space-y-2 sm:col-span-2">
                    <label for="admin_notes" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Ghi chú điều phối / Lý do cập nhật (Nếu có)
                    </label>
                    <textarea id="admin_notes" name="admin_notes" rows="3" placeholder="Nhập ghi chú cho nhân viên bếp, shipper hoặc lý do hủy đơn..." class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs"></textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('donhang_xem', $order->id) }}" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-8 py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer">
                    <i class="fas fa-save mr-1.5"></i> Cập nhật đơn hàng
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
