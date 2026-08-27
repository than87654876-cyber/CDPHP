@extends('layouts.app')

@section('title', 'Thanh toán Quét mã VietQR - FOODDAILY')

@section('content')
<div class="py-12 bg-transparent min-h-screen">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @php
            $displayAmount = $amount < 1000 ? $amount * 100 : $amount;
        @endphp

        <!-- Main QR Payment Card -->
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200 text-center space-y-6">
            
            <!-- Header Title -->
            <div class="border-b border-slate-100 pb-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-rose-50 border border-rose-100 text-[#ee4d2d] rounded-full text-xs font-black mb-2 uppercase tracking-wider">
                    <i class="fas fa-qrcode"></i> Thanh toán VietQR Hỏa Tốc
                </div>
                <h2 class="text-xl font-extrabold text-slate-900">Quét mã QR để chuyển khoản</h2>
                <p class="text-xs text-slate-500 mt-1">Mã đơn hàng: <span class="font-extrabold text-[#ee4d2d]">#FDL-{{ $order_id }}</span></p>
            </div>

            <!-- VietQR Image Box -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 max-w-xs mx-auto shadow-inner relative group">
                <img src="https://img.vietqr.io/image/BIDV-8899408675-compact2.jpg?amount={{ $displayAmount }}&addInfo=FDL-{{ $order_id }}&accountName=Tran%20Le%20Than"
                     alt="Mã VietQR Thanh Toán"
                     class="w-full h-auto rounded-xl shadow-xs transition-transform duration-300 group-hover:scale-102">
                <div class="mt-2 text-[11px] font-bold text-slate-400">Tự động nhận diện số tiền & nội dung</div>
            </div>

            <!-- Bank Transfer Info Table -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200 text-left text-xs space-y-2.5 shadow-xs">
                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold">Ngân hàng:</span>
                    <span class="font-extrabold text-slate-900 flex items-center gap-1">
                        <i class="fas fa-building-columns text-[#ee4d2d]"></i> BIDV (Ngân hàng TMCP Đầu tư & Phát triển)
                    </span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold">Chủ tài khoản:</span>
                    <span class="font-extrabold text-slate-900 uppercase">TRAN LE THAN</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold">Số tài khoản:</span>
                    <span class="font-black text-slate-900 text-sm tracking-wider font-mono">8899408675</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold">Số tiền thanh toán:</span>
                    <span class="font-black text-[#ee4d2d] text-base">{{ number_format($displayAmount, 0, ',', '.') }}đ</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-bold">Nội dung chuyển khoản:</span>
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-mono font-black text-xs rounded-lg border border-emerald-200">
                        FDL-{{ $order_id }}
                    </span>
                </div>
            </div>

            <!-- Automatic Realtime Status Box -->
            <div id="payment-status-box" class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl flex items-center justify-center gap-3 font-extrabold text-xs shadow-xs">
                <i class="fas fa-spinner fa-spin text-amber-500 text-base"></i>
                <span id="status-text">Đang chờ nhận chuyển khoản từ Ngân Hàng qua Webhook API...</span>
            </div>

            <!-- Countdown Timer -->
            <div class="text-xs font-bold text-slate-400 flex items-center justify-center gap-1.5">
                <i class="far fa-clock"></i> Thời gian giữ mã giao dịch: <span id="timer" class="font-mono text-[#ee4d2d] font-black text-sm">10:00</span>
            </div>

            <!-- Localhost / Dev Webhook Simulator Button -->
            <div class="pt-4 border-t border-slate-100 space-y-2">
                <button type="button" onclick="simulateBankWebhook(this)" class="w-full py-3 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-extrabold text-xs transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer active:scale-98">
                    <i class="fas fa-bolt text-amber-400"></i>
                    <span>Mô Phỏng Ngân Hàng Gửi Webhook Báo Tiền Vào (Bấm để thử nghiệm)</span>
                </button>
                <div class="text-[11px] text-slate-400 leading-relaxed text-left">
                    💡 <strong>Vì sao chuyển tiền xong cần Webhook?</strong> Khi bạn dùng app Ngân hàng chuyển tiền, ngân hàng sẽ gọi một <strong>API Webhook</strong> tự động về server để báo tiền đã khớp mã <strong>#FDL-{{ $order_id }}</strong>. Vì trang web đang chạy môi trường thử nghiệm (Localhost), bạn có thể bấm nút màu đen ở trên để mô phỏng Webhook của ngân hàng gửi về!
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Success Modal Overlay -->
<div id="success-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl border border-slate-100 animate-bounce-once space-y-4">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-500 mx-auto flex items-center justify-center text-3xl font-black">
            <i class="fas fa-check"></i>
        </div>
        <h3 class="text-lg font-extrabold text-slate-900">Thanh Toán Thành Công!</h3>
        <p class="text-xs text-slate-500 leading-relaxed">
            Đơn hàng <span class="font-extrabold text-slate-800">#FDL-{{ $order_id }}</span> đã được xác nhận thanh toán thành công và đang chuyển cho nhà bếp chế biến.
        </p>
        <div class="pt-2 text-xs font-bold text-[#ee4d2d]">
            Đang chuyển tới trang theo dõi đơn... <i class="fas fa-spinner fa-spin ml-1"></i>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const orderId = "{{ $order_id }}";
        
        // 1. Countdown Timer (10 phút)
        let duration = 60 * 10;
        const timerDisplay = document.getElementById('timer');
        
        const countdown = setInterval(function () {
            let minutes = parseInt(duration / 60, 10);
            let seconds = parseInt(duration % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            if (timerDisplay) timerDisplay.textContent = minutes + ":" + seconds;

            if (--duration < 0) {
                clearInterval(countdown);
                if (timerDisplay) timerDisplay.textContent = "00:00 (Hết hạn)";
            }
        }, 1000);

        // 2. Realtime Webhook Polling
        let isConfirmed = false;
        window.checkPaymentStatus = function() {
            if (isConfirmed) return;

            fetch(`{{ route('api.orders.payment-status', ['id' => $order_id]) }}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.payment_status === 'paid') {
                        isConfirmed = true;
                        
                        const statusBox = document.getElementById('payment-status-box');
                        const statusText = document.getElementById('status-text');
                        
                        if (statusBox) {
                            statusBox.className = "p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-center gap-3 font-extrabold text-xs shadow-xs";
                        }
                        if (statusText) {
                            statusText.innerHTML = "✅ Thanh toán thành công! Đơn hàng đang được nhà bếp chế biến.";
                        }

                        const successModal = document.getElementById('success-modal');
                        if (successModal) successModal.classList.remove('hidden');

                        localStorage.removeItem('fooddelicious_cart');

                        setTimeout(() => {
                            window.location.href = "{{ route('tracuu') }}?order_id=FDL-" + orderId;
                        }, 2500);
                    }
                })
                .catch(err => console.error('Error polling payment status:', err));
        };

        window.simulateBankWebhook = function(btn) {
            if (btn) btn.disabled = true;

            fetch("{{ route('api.payments.bank-transfer.notify') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    order_id: orderId,
                    amount: "{{ $displayAmount }}",
                    content: "FDL-" + orderId,
                    status: "success",
                    transaction_id: "TX_SIMULATED_" + Date.now()
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log('Simulated Bank Webhook response:', data);
                checkPaymentStatus();
            })
            .catch(err => {
                console.error('Simulation error:', err);
                if (btn) btn.disabled = false;
            });
        };

        checkPaymentStatus();
        setInterval(checkPaymentStatus, 2000);
    });
</script>
@endsection
