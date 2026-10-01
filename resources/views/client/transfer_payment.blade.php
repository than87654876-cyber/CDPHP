@extends('layouts.app')

@section('title', 'Thanh toán Quét mã VietQR - FOODDAILY')

@section('content')
<div class="py-12 bg-transparent min-h-screen">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @php
            $displayAmount = (float)($amount ?? ($order->final_amount ?? 0));
            $isPaid = isset($order) && $order->payment_status === 'paid';
        @endphp

        <!-- Main QR Payment Card -->
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200 text-center space-y-6">
            
            @if($isPaid)
                <!-- Card state: Already Paid -->
                <div class="space-y-6 py-4">
                    <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-500 mx-auto flex items-center justify-center text-4xl shadow-sm">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-full text-xs font-black mb-2 uppercase tracking-wider">
                            <i class="fas fa-circle-check"></i> Thanh Toán Thành Công
                        </div>
                        <h2 class="text-2xl font-black text-slate-900">Đơn hàng đã được thanh toán!</h2>
                        <p class="text-xs text-slate-500 mt-1">Mã đơn hàng: <span class="font-extrabold text-[#ee4d2d]">#FDL-{{ $order_id }}</span></p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 text-left text-xs space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                            <span class="text-slate-500 font-bold">Số tiền đã trả:</span>
                            <span class="font-black text-slate-900 text-sm text-[#ee4d2d]">{{ number_format($displayAmount, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                            <span class="text-slate-500 font-bold">Phương thức:</span>
                            <span class="font-bold text-slate-800">{{ ($order->payment_method ?? 'bank_transfer') === 'momo' ? 'Ví MoMo' : 'Chuyển khoản VietQR' }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                            <span class="text-slate-500 font-bold">Trạng thái bếp:</span>
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-extrabold rounded-lg border border-amber-200">
                                {{ $order->order_status === 'confirmed' ? 'Đã tiếp nhận - Bếp đang chuẩn bị' : ($order->order_status === 'preparing' ? 'Đang nấu món' : 'Đang xử lý') }}
                            </span>
                        </div>
                        @if($order->payment_paid_at)
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 font-bold">Thời gian nhận tiền:</span>
                            <span class="font-mono text-slate-700">{{ \Carbon\Carbon::parse($order->payment_paid_at)->format('H:i:s d/m/Y') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <a href="{{ route('tracuu', ['order_id' => 'FDL-' . $order_id]) }}" class="flex-1 py-3 px-4 bg-[#ee4d2d] hover:bg-[#d03e1f] text-white rounded-xl font-extrabold text-xs transition-all flex items-center justify-center gap-2 shadow-md">
                            <i class="fas fa-route"></i> Theo dõi tiến độ đơn hàng
                        </a>
                        <a href="{{ route('trangchu') }}" class="py-3 px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-extrabold text-xs transition-all flex items-center justify-center gap-1.5">
                            <i class="fas fa-home"></i> Trang chủ
                        </a>
                    </div>
                </div>
            @else
                <!-- Header Title -->
                <div class="border-b border-slate-100 pb-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-rose-50 border border-rose-100 text-[#ee4d2d] rounded-full text-xs font-black mb-2 uppercase tracking-wider">
                        <i class="fas fa-qrcode"></i> {{ ($payment_type ?? 'bank') === 'momo' ? 'Thanh toán MoMo / QR' : 'Thanh toán VietQR Hỏa Tốc' }}
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-900">Quét mã QR để chuyển khoản</h2>
                    <p class="text-xs text-slate-500 mt-1">Mã đơn hàng: <span class="font-extrabold text-[#ee4d2d]">#FDL-{{ $order_id }}</span></p>
                </div>

                <!-- VietQR Image Box -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 max-w-xs mx-auto shadow-inner relative group">
                    <img id="vietqr-image"
                         src="https://img.vietqr.io/image/BIDV-8899408675-compact2.jpg?amount={{ (int)$displayAmount }}&addInfo=FDL-{{ $order_id }}&accountName=Tran%20Le%20Than"
                         onerror="this.onerror=null; this.src='{{ asset('qr_code.png') }}';"
                         alt="Mã VietQR Thanh Toán"
                         class="w-full h-auto rounded-xl shadow-xs transition-transform duration-300 group-hover:scale-102">
                    <div class="mt-2 text-[11px] font-bold text-slate-500 flex items-center justify-center gap-1">
                        <i class="fas fa-camera text-slate-400"></i> Mở App Ngân Hàng hoặc MoMo quét mã
                    </div>
                </div>

                <!-- Bank Transfer Info Table -->
                <div class="bg-white rounded-2xl p-4 border border-slate-200 text-left text-xs space-y-2.5 shadow-xs">
                    <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                        <span class="text-slate-500 font-bold">Ngân hàng:</span>
                        <span class="font-extrabold text-slate-900 flex items-center gap-1.5">
                            <i class="fas fa-building-columns text-[#ee4d2d]"></i> BIDV (Đầu tư & Phát triển)
                        </span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                        <span class="text-slate-500 font-bold">Chủ tài khoản:</span>
                        <span class="font-extrabold text-slate-900 uppercase">TRAN LE THAN</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                        <span class="text-slate-500 font-bold">Số tài khoản:</span>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-slate-900 text-sm tracking-wider font-mono">8899408675</span>
                            <button type="button" onclick="copyToClipboard('8899408675', this)" class="px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-bold transition-all cursor-pointer flex items-center gap-1">
                                <i class="far fa-copy text-[10px]"></i> <span>Chép</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                        <span class="text-slate-500 font-bold">Số tiền thanh toán:</span>
                        <div class="flex items-center gap-2">
                            <span class="font-black text-[#ee4d2d] text-base">{{ number_format($displayAmount, 0, ',', '.') }}đ</span>
                            <button type="button" onclick="copyToClipboard('{{ (int)$displayAmount }}', this)" class="px-2 py-0.5 bg-rose-50 hover:bg-rose-100 text-[#ee4d2d] rounded text-[11px] font-bold transition-all cursor-pointer flex items-center gap-1">
                                <i class="far fa-copy text-[10px]"></i> <span>Chép</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-bold">Nội dung chuyển khoản:</span>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-mono font-black text-xs rounded-lg border border-emerald-200">
                                FDL-{{ $order_id }}
                            </span>
                            <button type="button" onclick="copyToClipboard('FDL-{{ $order_id }}', this)" class="px-2 py-0.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 rounded text-[11px] font-bold transition-all cursor-pointer flex items-center gap-1">
                                <i class="far fa-copy text-[10px]"></i> <span>Chép</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Guidance Alert -->
                <div class="p-3 bg-amber-50/80 border border-amber-200/80 rounded-xl text-left flex items-start gap-2.5 text-[11px] text-amber-900 leading-relaxed">
                    <i class="fas fa-circle-exclamation text-amber-500 mt-0.5 shrink-0"></i>
                    <span><strong>Lưu ý:</strong> Vui lòng giữ nguyên nội dung chuyển khoản <strong>FDL-{{ $order_id }}</strong> để hệ thống đối soát Webhook kích hoạt đơn tự động.</span>
                </div>

                <!-- Automatic Realtime Status Box -->
                <div id="payment-status-box" class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl flex items-center justify-center gap-3 font-extrabold text-xs shadow-xs transition-all">
                    <i id="status-icon" class="fas fa-spinner fa-spin text-amber-500 text-base"></i>
                    <span id="status-text">Đang chờ nhận chuyển khoản từ Ngân Hàng qua Webhook API...</span>
                </div>

                <!-- Countdown Timer -->
                <div class="text-xs font-bold text-slate-400 flex items-center justify-center gap-1.5">
                    <i class="far fa-clock"></i> Thời gian giữ mã giao dịch: <span id="timer" class="font-mono text-[#ee4d2d] font-black text-sm">10:00</span>
                </div>

                <!-- Check Payment Status Button & Simulator -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <button type="button" id="manual-check-btn" onclick="manualCheckStatus(this)" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold text-xs transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer active:scale-98">
                        <i class="fas fa-rotate"></i>
                        <span>Tôi đã chuyển khoản xong (Kiểm tra giao dịch)</span>
                    </button>

                    <button type="button" id="simulate-btn" onclick="simulateBankWebhook(this)" class="w-full py-3 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-extrabold text-xs transition-all flex items-center justify-center gap-2 shadow-sm cursor-pointer active:scale-98">
                        <i class="fas fa-bolt text-amber-400"></i>
                        <span>Mô Phỏng Ngân Hàng Gửi Webhook Báo Tiền Vào (Bấm để thử nghiệm)</span>
                    </button>
                    <div class="text-[11px] text-slate-400 leading-relaxed text-left">
                        💡 <strong>Cách thức hoạt động:</strong> Sau khi bạn chuyển tiền thật trên App ngân hàng, ngân hàng sẽ tự động gửi Webhook về hệ thống. Với môi trường thử nghiệm localhost, bạn có thể ấn nút màu đen ở trên để mô phỏng Webhook thành công ngay lập tức!
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>

<!-- Success Modal Overlay -->
<div id="success-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl border border-slate-100 space-y-4">
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

<!-- Toast Feedback Notification -->
<div id="copy-toast" class="fixed bottom-6 right-6 z-50 hidden bg-slate-900 text-white px-4 py-2.5 rounded-xl shadow-lg text-xs font-bold items-center gap-2 transition-all duration-300">
    <i class="fas fa-circle-check text-emerald-400"></i>
    <span id="toast-message">Đã sao chép vào bộ nhớ tạm!</span>
</div>
@endsection

@section('scripts')
<script>
    // Hàm sao chép vào clipboard
    function copyToClipboard(text, btn) {
        if (!text) return;

        const originalHtml = btn ? btn.innerHTML : '';
        const setCopied = () => {
            if (btn) {
                btn.innerHTML = '<i class="fas fa-check text-emerald-500 text-[10px]"></i> <span class="text-emerald-600">Đã chép</span>';
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                }, 1800);
            }
            showToast('Đã sao chép: ' + text);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(setCopied).catch(() => fallbackCopy(text, setCopied));
        } else {
            fallbackCopy(text, setCopied);
        }
    }

    function fallbackCopy(text, callback) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            if (callback) callback();
        } catch (err) {
            console.error('Fallback copy failed', err);
        }
        document.body.removeChild(textArea);
    }

    function showToast(msg) {
        const toast = document.getElementById('copy-toast');
        const msgSpan = document.getElementById('toast-message');
        if (toast && msgSpan) {
            msgSpan.textContent = msg;
            toast.classList.remove('hidden');
            toast.classList.add('flex');
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('flex');
            }, 2000);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        const orderId = "{{ $order_id ?? 0 }}";
        const isAlreadyPaid = {{ $isPaid ? 'true' : 'false' }};

        if (isAlreadyPaid || !orderId || orderId === "0") {
            return;
        }
        
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
        const statusUrl = "{{ route('api.orders.payment-status', ['id' => $order_id ?? 0]) }}";

        window.checkPaymentStatus = function(manualTrigger = false) {
            if (isConfirmed) return;

            return fetch(statusUrl)
                .then(response => response.json())
                .then(data => {
                    if (data && data.payment_status === 'paid') {
                        isConfirmed = true;
                        
                        const statusBox = document.getElementById('payment-status-box');
                        const statusText = document.getElementById('status-text');
                        const statusIcon = document.getElementById('status-icon');
                        
                        if (statusBox) {
                            statusBox.className = "p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-center gap-3 font-extrabold text-xs shadow-xs";
                        }
                        if (statusIcon) {
                            statusIcon.className = "fas fa-circle-check text-emerald-500 text-base";
                        }
                        if (statusText) {
                            statusText.innerHTML = "✅ Thanh toán thành công! Đơn hàng đang được nhà bếp chế biến.";
                        }

                        const successModal = document.getElementById('success-modal');
                        if (successModal) successModal.classList.remove('hidden');

                        localStorage.removeItem('fooddelicious_cart');

                        setTimeout(() => {
                            window.location.href = "{{ route('tracuu') }}?order_id=FDL-" + orderId;
                        }, 2200);
                        return true;
                    } else {
                        if (manualTrigger) {
                            showToast('Hệ thống chưa nhận được tiền. Nếu vừa chuyển khoản, vui lòng đợi 5-15 giây.');
                        }
                        return false;
                    }
                })
                .catch(err => {
                    console.error('Error polling payment status:', err);
                    if (manualTrigger) {
                        showToast('Không thể kết nối đến máy chủ, vui lòng thử lại sau.');
                    }
                    return false;
                });
        };

        window.manualCheckStatus = function(btn) {
            if (btn) {
                btn.disabled = true;
                const origHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đối soát với ngân hàng...';
                window.checkPaymentStatus(true).finally(() => {
                    setTimeout(() => {
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = origHtml;
                        }
                    }, 1200);
                });
            } else {
                window.checkPaymentStatus(true);
            }
        };

        window.simulateBankWebhook = function(btn) {
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin text-amber-400"></i> Đang gửi Webhook ngân hàng...';
            }

            fetch("{{ route('api.payments.bank-transfer.notify') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    order_id: orderId,
                    amount: "{{ (int)$displayAmount }}",
                    content: "FDL-" + orderId,
                    status: "success",
                    transaction_id: "TX_SIMULATED_" + Date.now(),
                    bank_code: "BIDV",
                    bank_reference: "REF" + Date.now()
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log('Simulated Bank Webhook response:', data);
                if (data.success) {
                    showToast('Đã nhận phản hồi thành công từ Ngân hàng!');
                    window.checkPaymentStatus();
                } else {
                    showToast(data.message || 'Lỗi mô phỏng chuyển khoản');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-bolt text-amber-400"></i> <span>Mô Phỏng Ngân Hàng Gửi Webhook Báo Tiền Vào (Thử lại)</span>';
                    }
                }
            })
            .catch(err => {
                console.error('Simulation error:', err);
                showToast('Lỗi gửi Webhook mô phỏng');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-bolt text-amber-400"></i> <span>Mô Phỏng Ngân Hàng Gửi Webhook Báo Tiền Vào (Thử lại)</span>';
                }
            });
        };

        // Bắt đầu kiểm tra ngay và lặp lại mỗi 2 giây
        checkPaymentStatus();
        const pollInterval = setInterval(function() {
            if (isConfirmed) {
                clearInterval(pollInterval);
            } else {
                checkPaymentStatus();
            }
        }, 2000);
    });
</script>
@endsection
