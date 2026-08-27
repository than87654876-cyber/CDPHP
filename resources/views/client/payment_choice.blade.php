@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng #' . $order->id . ' - FOODDAILY')

@section('content')
<section class="py-8 min-h-[75vh] flex items-center justify-center bg-transparent">
    <div class="max-w-2xl w-full mx-auto px-4 sm:px-6">
        
        <!-- White Glassmorphism Card Container -->
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200 space-y-6">
            
            <!-- Header: Title & Order Tag -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-[#ee4d2d] text-xs font-black uppercase tracking-wider border border-rose-100">
                            <i class="fas fa-receipt"></i> Đơn Hàng #FDL-{{ $order->id }}
                        </span>
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200">
                            Chờ thanh toán
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900">
                        Xác Nhận & Thanh Toán
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">
                        Chọn phương thức thanh toán phù hợp để hoàn tất đơn hàng của bạn
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#ee4d2d] hidden sm:flex items-center justify-center text-xl shadow-xs">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            <!-- Amount & Order Info Box -->
            <div class="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600 uppercase tracking-wide">Tổng tiền cần thanh toán</span>
                    <span class="text-xl sm:text-2xl font-black text-[#ee4d2d]">
                        {{ number_format($order->final_amount, 0, ',', '.') }}đ
                    </span>
                </div>
                <div class="pt-2 border-t border-slate-200 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600">
                    <div>
                        <span class="text-slate-400 font-medium">Khách hàng:</span>
                        <span class="font-bold text-slate-800 ml-1">{{ $order->user->fullname ?? $order->user->name ?? 'Khách hàng' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium">Số điện thoại:</span>
                        <span class="font-bold text-slate-800 ml-1">{{ $order->user->phone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Method Form -->
            <form action="{{ route('muahang.thanhtoan.select', $order->id) }}" method="POST" class="space-y-5" onsubmit="try { localStorage.removeItem('fooddelicious_cart'); } catch(e){}">
                @csrf
                
                <div class="space-y-3">
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">
                        Chọn phương thức thanh toán:
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Option 1: COD -->
                        <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-all bg-white hover:border-[#ee4d2d]/60 has-[:checked]:border-[#ee4d2d] has-[:checked]:bg-rose-50/40 shadow-xs">
                            <input type="radio" name="payment_method" value="cash" {{ $order->payment_method === 'cash' ? 'checked' : '' }} class="w-4 h-4 text-[#ee4d2d] focus:ring-[#ee4d2d] border-slate-300">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 font-black text-slate-900 text-xs">
                                    <span>💵</span> Tiền mặt (COD)
                                </div>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Thanh toán khi nhận hàng</p>
                            </div>
                        </label>

                        <!-- Option 2: VietQR Transfer -->
                        <label class="relative flex items-center gap-3 p-4 rounded-2xl border-2 cursor-pointer transition-all bg-white hover:border-[#ee4d2d]/60 has-[:checked]:border-[#ee4d2d] has-[:checked]:bg-rose-50/40 shadow-xs">
                            <input type="radio" name="payment_method" value="bank_transfer" {{ $order->payment_method === 'bank_transfer' ? 'checked' : '' }} class="w-4 h-4 text-[#ee4d2d] focus:ring-[#ee4d2d] border-slate-300">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 font-black text-slate-900 text-xs">
                                    <span>⚡</span> Chuyển khoản VietQR
                                </div>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">Quét mã QR tự động xác nhận</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Guidance Note -->
                <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex items-start gap-3">
                    <i class="fas fa-circle-info text-amber-500 text-base shrink-0 mt-0.5"></i>
                    <p class="text-xs text-amber-900 leading-relaxed font-medium">
                        Nếu chọn <strong>Tiền mặt (COD)</strong>, shipper sẽ thu tiền khi giao món. Nếu chọn <strong>Chuyển khoản VietQR</strong>, hệ thống sẽ hiển thị mã QR để bạn quét thanh toán tự động trong tích tắc.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                    <a href="{{ route('trangchu') }}" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs text-center transition-colors">
                        ← Về trang chủ
                    </a>
                    <button type="submit" class="w-full sm:flex-1 py-3 px-6 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                        <span>XÁC NHẬN PHƯƠNG THỨC THANH TOÁN</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>

            </form>

        </div>

    </div>
</section>
@endsection
