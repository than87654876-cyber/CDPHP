/**
 * FOODDAILY - Client Features Bundle
 * Gộp toàn bộ tính năng Client: Live Search, Header Cart Badge, AI Chatbot,
 * Vòng quay Hôm nay ăn gì, Kho ưu đãi & Ví voucher toàn trang.
 */

// Helper lấy cấu hình từ Server hoặc Fallback
function getConfig() {
    return window.FoodDailyConfig || {
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        isLoggedIn: false,
        routes: {
            spinDishes: '/api/dishes/spin',
            dishSearch: '/api/dishes/search',
            chatbotAsk: '/api/chatbot/ask',
            couponsMy: '/api/coupons/my',
            couponsSave: '/api/coupons/save',
            couponsSaveAll: '/api/coupons/save-all',
            cartAdd: '/giohang/them',
            login: '/dang-nhap',
            buy: '/mua-hang'
        }
    };
}

function fmtMoney(n) {
    return Number(n).toLocaleString('vi-VN') + 'đ';
}

// =========================================================================
// 1. HEADER CART BADGE
// =========================================================================
export function updateGlobalHeaderCartBadge() {
    try {
        const cart = JSON.parse(localStorage.getItem('fooddelicious_cart') || '[]');
        const badge = document.getElementById('global-header-cart-badge');
        const mobileBadge = document.getElementById('mobile-nav-cart-badge');
        const count = cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 1), 0);
        const totalPrice = cart.reduce((sum, item) => sum + (parseFloat(item.price) || 0) * (parseInt(item.quantity) || 1), 0);
        
        [badge, mobileBadge].forEach(el => {
            if (el) {
                if (count > 0) {
                    el.innerText = count > 99 ? '99+' : count;
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            }
        });

        // Cập nhật Mobile Floating Quick Cart Bar (Chuẩn ShopeeFood / GrabFood)
        const floatCartBar = document.getElementById('mobile-floating-cart-bar');
        const floatCartCount = document.getElementById('mobile-float-cart-count');
        const floatCartTotal = document.getElementById('mobile-float-cart-total');
        const chatbotBtn = document.getElementById('chatbot-toggle-btn');
        const chatbotModal = document.getElementById('chatbot-modal');

        if (floatCartBar) {
            const isCheckoutPage = window.location.pathname.includes('/muahang') || window.location.pathname.includes('/giohang');
            if (count > 0 && !isCheckoutPage) {
                if (floatCartCount) floatCartCount.innerText = count > 99 ? '99+' : count;
                if (floatCartTotal) floatCartTotal.innerText = fmtMoney(totalPrice);
                floatCartBar.classList.remove('hidden');

                // Tự động đẩy Chatbot lên cao để không che nút "Đặt món"
                if (chatbotBtn) {
                    chatbotBtn.classList.remove('bottom-20');
                    chatbotBtn.classList.add('bottom-32');
                }
                if (chatbotModal) {
                    chatbotModal.classList.remove('bottom-24');
                    chatbotModal.classList.add('bottom-36');
                }
            } else {
                floatCartBar.classList.add('hidden');
                if (chatbotBtn) {
                    chatbotBtn.classList.remove('bottom-32');
                    chatbotBtn.classList.add('bottom-20');
                }
                if (chatbotModal) {
                    chatbotModal.classList.remove('bottom-36');
                    chatbotModal.classList.add('bottom-24');
                }
            }
        }
    } catch (e) {}
}

// =========================================================================
// 2. HEADER LIVE SEARCH MODAL & AJAX
// =========================================================================
export function toggleHeaderSearch() {
    const modal = document.getElementById('header-search-modal');
    if (!modal) return;
    modal.classList.toggle('hidden');
    if (!modal.classList.contains('hidden')) {
        const input = document.getElementById('header-search-input');
        if (input) input.focus();
    }
}

let liveSearchTimer = null;
export function handleLiveSearch(query) {
    clearTimeout(liveSearchTimer);
    const resultsBox = document.getElementById('live-search-results');
    if (!resultsBox) return;

    if (!query || !query.trim()) {
        resultsBox.classList.add('hidden');
        resultsBox.innerHTML = '';
        return;
    }

    const cfg = getConfig();
    const searchUrl = cfg.routes.dishSearch || '/api/dishes/search';
    const cartAddUrl = cfg.routes.cartAdd || '/giohang/them';
    const csrfToken = cfg.csrfToken;

    liveSearchTimer = setTimeout(() => {
        fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data.dishes && data.dishes.length > 0) {
                    resultsBox.innerHTML = data.dishes.map(d => `
                        <div class="p-3 flex items-center justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden border border-slate-100 shrink-0">
                                    ${d.image ? `<img src="${d.image}" class="w-full h-full object-cover">` : `<div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-utensils text-xs"></i></div>`}
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-xs">${d.dish_name}</h4>
                                    <p class="text-[11px] text-[#ee4d2d] font-black">${d.formatted_price}</p>
                                </div>
                            </div>
                            <form action="${cartAddUrl}" method="POST">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="dish_id" value="${d.id}">
                                <button type="submit" class="px-3 py-1.5 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-lg text-xs font-bold shadow-xs">
                                    + Đặt món
                                </button>
                            </form>
                        </div>
                    `).join('');
                    resultsBox.classList.remove('hidden');
                } else {
                    resultsBox.innerHTML = `<div class="p-4 text-center text-slate-400 text-xs font-semibold">Không tìm thấy món nào có tên "${query}"</div>`;
                    resultsBox.classList.remove('hidden');
                }
            })
            .catch(err => console.error('Live search error:', err));
    }, 250);
}

// =========================================================================
// 3. VÒNG QUAY "HÔM NAY MÌNH ĂN GÌ?"
// =========================================================================
let spinDishes = [];
let spinRotation = 0;
let isSpinning = false;
let spinMaxPrice = 100000;
let spinAnimationId = null;
const spinSegmentColors = [
    '#ee4d2d', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6',
    '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#84cc16',
    '#a855f7', '#ef4444'
];

export function openSpinWheelModal() {
    const modal = document.getElementById('spin-wheel-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    loadSpinDishes(spinMaxPrice);
}

export function closeSpinWheelModal() {
    const modal = document.getElementById('spin-wheel-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    if (spinAnimationId) {
        cancelAnimationFrame(spinAnimationId);
        spinAnimationId = null;
    }
}

export function onPriceSliderChange(val) {
    spinMaxPrice = parseInt(val);
    const pct = ((spinMaxPrice - 20000) / (400000 - 20000)) * 100;
    const slider = document.getElementById('spin-price-slider');
    if (slider) slider.style.background = `linear-gradient(to right, #ee4d2d 0%, #ee4d2d ${pct}%, #e2e8f0 ${pct}%, #e2e8f0 100%)`;
    const display = document.getElementById('spin-price-display');
    if (display) display.textContent = fmtMoney(spinMaxPrice);
    loadSpinDishes(spinMaxPrice);
}

function loadSpinDishes(maxPrice) {
    const cfg = getConfig();
    const apiUrl = (cfg.routes.spinDishes || '/api/spin-dishes') + `?max_price=${maxPrice}`;
    fetch(apiUrl)
        .then(r => r.json())
        .then(data => {
            spinDishes = data.dishes || [];
            if (spinDishes.length === 0) {
                spinDishes = [{ dish_name: 'Thử ngân sách cao hơn nhé!', price: 0, id: null }];
            }
            drawWheel(spinRotation);
            const resArea = document.getElementById('spin-result-area');
            if (resArea) resArea.classList.add('hidden');
        })
        .catch(() => {
            spinDishes = [{ dish_name: 'Không tải được món', price: 0, id: null }];
            drawWheel(spinRotation);
        });
}

function drawWheel(rotateDeg) {
    const canvas = document.getElementById('spin-wheel-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const W = canvas.width, H = canvas.height;
    const cx = W / 2, cy = H / 2, R = Math.min(cx, cy) - 4;
    const n = spinDishes.length;
    if (n === 0) return;
    const arc = (2 * Math.PI) / n;

    ctx.clearRect(0, 0, W, H);

    ctx.save();
    ctx.shadowColor = 'rgba(0,0,0,0.18)';
    ctx.shadowBlur = 14;
    ctx.beginPath();
    ctx.arc(cx, cy, R + 3, 0, 2 * Math.PI);
    ctx.fillStyle = '#fff';
    ctx.fill();
    ctx.restore();

    const offsetRad = (rotateDeg - 90) * (Math.PI / 180);

    for (let i = 0; i < n; i++) {
        const startAngle = offsetRad + i * arc;
        const endAngle = startAngle + arc;
        const color = spinSegmentColors[i % spinSegmentColors.length];

        ctx.beginPath();
        ctx.moveTo(cx, cy);
        ctx.arc(cx, cy, R, startAngle, endAngle);
        ctx.closePath();
        ctx.fillStyle = color;
        ctx.fill();
        ctx.strokeStyle = 'rgba(255,255,255,0.5)';
        ctx.lineWidth = 1.5;
        ctx.stroke();

        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(startAngle + arc / 2);
        ctx.textAlign = 'right';
        ctx.fillStyle = '#fff';
        ctx.font = `bold ${n > 8 ? 9 : 11}px "Plus Jakarta Sans", sans-serif`;
        ctx.shadowColor = 'rgba(0,0,0,0.4)';
        ctx.shadowBlur = 3;

        const name = spinDishes[i].dish_name || '';
        const label = name.length > 16 ? name.slice(0, 14) + '…' : name;
        const price = spinDishes[i].price > 0 ? fmtMoney(spinDishes[i].price) : '';

        ctx.fillText(label, R - 8, -3);
        if (price) {
            ctx.font = `${n > 8 ? 8 : 9}px "Plus Jakarta Sans", sans-serif`;
            ctx.fillStyle = 'rgba(255,255,255,0.85)';
            ctx.fillText(price, R - 8, 9);
        }
        ctx.restore();
    }

    ctx.beginPath();
    ctx.arc(cx, cy, 26, 0, 2 * Math.PI);
    ctx.fillStyle = '#1e293b';
    ctx.fill();
    ctx.beginPath();
    ctx.arc(cx, cy, 20, 0, 2 * Math.PI);
    const grad = ctx.createRadialGradient(cx - 4, cy - 4, 2, cx, cy, 20);
    grad.addColorStop(0, '#f59e0b');
    grad.addColorStop(1, '#ee4d2d');
    ctx.fillStyle = grad;
    ctx.fill();

    ctx.fillStyle = '#fff';
    ctx.font = '14px Arial';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText('🎡', cx, cy);
}

export function startSpin() {
    if (isSpinning) return;
    if (spinDishes.length === 0) return;

    isSpinning = true;
    const resArea = document.getElementById('spin-result-area');
    if (resArea) resArea.classList.add('hidden');
    const btn = document.getElementById('spin-btn');
    const btnText = document.getElementById('spin-btn-text');
    if (btn) btn.disabled = true;
    if (btnText) btnText.textContent = 'Đang quay...';

    const extraDeg = 360 * (5 + Math.floor(Math.random() * 5));
    const n = spinDishes.length;
    const arc = 360 / n;
    const winnerIdx = Math.floor(Math.random() * n);
    const targetAngle = (360 - (winnerIdx * arc + arc / 2)) % 360;
    const totalDeg = extraDeg + targetAngle;

    const startRot = spinRotation;
    const endRot = startRot + totalDeg;
    const duration = 4000 + Math.random() * 1200;
    const startTime = performance.now();

    function easeOut(t) {
        return 1 - Math.pow(1 - t, 4);
    }

    function animate(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        spinRotation = startRot + (endRot - startRot) * easeOut(progress);
        drawWheel(spinRotation);

        if (progress < 1) {
            spinAnimationId = requestAnimationFrame(animate);
        } else {
            spinRotation = spinRotation % 360;
            isSpinning = false;
            if (btn) btn.disabled = false;
            if (btnText) btnText.textContent = 'Quay lại!';
            showSpinResult(winnerIdx);
        }
    }

    spinAnimationId = requestAnimationFrame(animate);
}

function showSpinResult(idx) {
    const dish = spinDishes[idx];
    if (!dish) return;

    const resultArea = document.getElementById('spin-result-area');
    const resultCard = document.getElementById('spin-result-card');
    const orderBtn = document.getElementById('spin-order-btn');
    const cfg = getConfig();

    if (resultCard) {
        resultCard.innerHTML = `
            <div class="flex items-center gap-3 bg-white rounded-xl p-3 border border-amber-200 shadow-xs">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"
                     style="background: linear-gradient(135deg, #fef3c7, #fee2e2);">
                    🍽️
                </div>
                <div class="min-w-0">
                    <p class="font-black text-slate-900 text-sm leading-tight">${dish.dish_name}</p>
                    ${dish.price > 0 ? `<p class="text-[#ee4d2d] font-extrabold text-xs mt-0.5">${fmtMoney(dish.price)}</p>` : ''}
                    ${dish.description ? `<p class="text-slate-400 text-[10px] mt-0.5 truncate">${dish.description}</p>` : ''}
                </div>
            </div>
        `;
    }

    if (dish.id && orderBtn) {
        orderBtn.href = cfg.routes.buy || '/mua-hang';
    }

    if (resultArea) {
        resultArea.classList.remove('hidden');
        resultArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// =========================================================================
// 4. FLOATING AI CHATBOT WIDGET
// =========================================================================
export function toggleChatbot() {
    const modal = document.getElementById('chatbot-modal');
    if (!modal) return;
    modal.classList.toggle('hidden');
    if (!modal.classList.contains('hidden')) {
        const input = document.getElementById('chatbot-input');
        if (input) input.focus();
    }
}

export function sendChatbotMessage(e) {
    if (e) e.preventDefault();
    const input = document.getElementById('chatbot-input');
    if (!input) return;
    const msg = input.value.trim();
    if (!msg) return;

    const msgContainer = document.getElementById('chatbot-messages');
    if (!msgContainer) return;

    msgContainer.innerHTML += `
        <div class="flex items-start justify-end gap-2">
            <div class="bg-[#ee4d2d] text-white p-3 rounded-2xl text-xs max-w-[85%] font-semibold shadow-xs">
                ${msg}
            </div>
        </div>
    `;
    input.value = '';
    msgContainer.scrollTop = msgContainer.scrollHeight;

    const loadingId = 'cb-loading-' + Date.now();
    msgContainer.innerHTML += `
        <div id="${loadingId}" class="flex items-start gap-2">
            <div class="w-7 h-7 rounded-lg bg-rose-100 text-[#ee4d2d] flex items-center justify-center font-bold text-xs shrink-0">AI</div>
            <div class="bg-white p-3 rounded-2xl border border-slate-100 text-slate-400 italic text-xs">
                <i class="fas fa-spinner fa-spin mr-1"></i> Trợ lý AI đang tìm món...
            </div>
        </div>
    `;
    msgContainer.scrollTop = msgContainer.scrollHeight;

    const cfg = getConfig();
    fetch(cfg.routes.chatbotAsk || '/api/chatbot/ask', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': cfg.csrfToken
        },
        body: JSON.stringify({ message: msg })
    })
    .then(res => res.json())
    .then(data => {
        const loader = document.getElementById(loadingId);
        if (loader) loader.remove();

        let dishesHtml = '';
        if (data.dishes && data.dishes.length > 0) {
            dishesHtml = `<div class="mt-2.5 space-y-2">` + data.dishes.map(d => `
                <div class="bg-slate-50 p-2 rounded-xl border border-slate-100 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 truncate">
                        ${d.image ? `<img src="${d.image}" class="w-8 h-8 rounded-lg object-cover">` : ''}
                        <div class="truncate">
                            <h5 class="font-bold text-[11px] text-slate-800 truncate">${d.dish_name}</h5>
                            <p class="text-[10px] text-[#ee4d2d] font-extrabold">${d.price}</p>
                        </div>
                    </div>
                    <form action="${cfg.routes.cartAdd || '/giohang/them'}" method="POST">
                        <input type="hidden" name="_token" value="${cfg.csrfToken}">
                        <input type="hidden" name="dish_id" value="${d.id}">
                        <button type="submit" class="px-2.5 py-1 bg-[#ee4d2d] text-white rounded-md text-[10px] font-bold cursor-pointer">+ Đặt</button>
                    </form>
                </div>
            `).join('') + `</div>`;
        }

        msgContainer.innerHTML += `
            <div class="flex items-start gap-2">
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-[#ee4d2d] flex items-center justify-center font-bold text-xs shrink-0">AI</div>
                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs text-slate-800 max-w-[85%] leading-relaxed">
                    ${data.reply}
                    ${dishesHtml}
                </div>
            </div>
        `;
        msgContainer.scrollTop = msgContainer.scrollHeight;
    })
    .catch(err => {
        const loader = document.getElementById(loadingId);
        if (loader) loader.remove();
        msgContainer.innerHTML += `
            <div class="flex items-start gap-2">
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-[#ee4d2d] flex items-center justify-center font-bold text-xs shrink-0">AI</div>
                <div class="bg-rose-50 text-rose-600 p-3 rounded-2xl border border-rose-200 text-xs">
                    Có lỗi khi kết nối với AI. Vui lòng thử lại sau!
                </div>
            </div>
        `;
        msgContainer.scrollTop = msgContainer.scrollHeight;
    });
}

// =========================================================================
// 5. KHO ƯU ĐÃI & VÍ VOUCHER TOÀN TRANG (GLOBAL MODAL)
// =========================================================================
let globalCouponsData = [];
let currentVoucherTab = 'all';

export function openGlobalUserVoucherModal(tab = 'all') {
    currentVoucherTab = tab;
    const modal = document.getElementById('globalUserVoucherModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    loadGlobalCoupons();
}

export function closeGlobalUserVoucherModal() {
    const modal = document.getElementById('globalUserVoucherModal');
    if (modal) modal.classList.add('hidden');
}

export function switchGlobalVoucherTab(tab) {
    currentVoucherTab = tab;
    document.querySelectorAll('.global-tab-btn').forEach(btn => {
        const isActive = btn.getAttribute('data-tab') === tab;
        btn.className = `global-tab-btn flex-1 py-2 rounded-xl text-xs font-black transition-all ${
            isActive ? 'bg-[#ee4d2d] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
        }`;
    });
    renderGlobalCoupons();
}

export function loadGlobalCoupons() {
    const listDiv = document.getElementById('global-voucher-list');
    if (!listDiv) return;

    listDiv.innerHTML = '<div class="py-12 text-center text-xs text-slate-400 font-bold"><i class="fas fa-spinner fa-spin mr-2"></i>Đang nạp kho voucher...</div>';

    const cfg = getConfig();
    fetch(cfg.routes.couponsMy || '/api/coupons/my')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                globalCouponsData = data.coupons || [];
                const savedCount = globalCouponsData.filter(c => c.is_saved).length;
                const expiringCount = globalCouponsData.filter(c => c.is_expiring_soon).length;

                const savedCountBadge = document.getElementById('global-badge-saved');
                const expiringCountBadge = document.getElementById('global-badge-expiring');
                if (savedCountBadge) savedCountBadge.innerText = savedCount;
                if (expiringCountBadge) expiringCountBadge.innerText = expiringCount;

                switchGlobalVoucherTab(currentVoucherTab);
            } else {
                listDiv.innerHTML = '<div class="p-6 text-center text-xs text-rose-500 font-bold">Không thể tải kho ưu đãi. Vui lòng thử lại.</div>';
            }
        })
        .catch(() => {
            listDiv.innerHTML = '<div class="p-6 text-center text-xs text-rose-500 font-bold">Lỗi kết nối máy chủ.</div>';
        });
}

export function renderGlobalCoupons() {
    const listDiv = document.getElementById('global-voucher-list');
    if (!listDiv) return;

    const cfg = getConfig();
    let filtered = [...globalCouponsData];
    if (currentVoucherTab === 'saved') {
        filtered = filtered.filter(c => c.is_saved);
    } else if (currentVoucherTab === 'expiring') {
        filtered = filtered.filter(c => c.is_expiring_soon);
    }

    if (filtered.length === 0) {
        let emptyMsg = 'Chưa có voucher nào trong mục này.';
        if (currentVoucherTab === 'saved') {
            emptyMsg = cfg.isLoggedIn
                ? 'Bạn chưa lưu mã ưu đãi nào. Hãy chuyển sang tab "Tất cả" để lưu mã nhé!'
                : 'Bạn chưa đăng nhập. Vui lòng đăng nhập để lưu và quản lý mã giảm giá của bạn!';
        } else if (currentVoucherTab === 'expiring') {
            emptyMsg = 'Hiện không có mã nào sắp hết hạn trong 3 ngày tới.';
        }

        listDiv.innerHTML = `
            <div class="py-12 px-4 text-center">
                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-orange-50 text-orange-400 flex items-center justify-center text-2xl">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <p class="text-xs font-bold text-slate-600 max-w-sm mx-auto leading-relaxed">${emptyMsg}</p>
                ${currentVoucherTab === 'saved' && !cfg.isLoggedIn ? `
                    <a href="${cfg.routes.login || '/dang-nhap'}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-[#ee4d2d] text-white rounded-xl text-xs font-black shadow-xs hover:bg-red-600">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập ngay
                    </a>
                ` : ''}
            </div>
        `;
        return;
    }

    let html = '';
    filtered.forEach(c => {
        html += `
            <div class="p-3.5 bg-white rounded-2xl border border-slate-200 hover:border-orange-300 transition-all flex items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-13 h-13 rounded-2xl bg-rose-50 border border-rose-200 text-[#ee4d2d] flex flex-col items-center justify-center font-black text-center shrink-0">
                        <span class="text-xs leading-none">${c.discount_type === 'percent' ? c.discount_value + '%' : (c.discount_value / 1000) + 'K'}</span>
                        <span class="text-[8px] uppercase tracking-wider text-slate-500 font-bold mt-0.5">GIẢM</span>
                    </div>
                    <div class="min-w-0 space-y-0.5">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-black text-slate-900 text-xs tracking-wider">#${c.coupon_code}</span>
                            <button type="button" onclick="navigator.clipboard.writeText('${c.coupon_code}'); alert('Đã sao chép mã #${c.coupon_code}');" class="text-[10px] text-slate-400 hover:text-slate-700 cursor-pointer" title="Sao chép mã">
                                <i class="far fa-copy"></i>
                            </button>
                            ${c.is_saved ? '<span class="px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 text-[9px] font-black">Trong ví</span>' : ''}
                            ${c.is_expiring_soon ? `<span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 text-[9px] font-black animate-pulse">⏰ Còn ${c.days_remaining} ngày</span>` : ''}
                        </div>
                        <p class="text-xs font-bold text-slate-700 truncate">${c.formatted_discount} - ${c.formatted_min_order}</p>
                        <p class="text-[10px] text-slate-400">HSD: ${c.end_date}</p>
                    </div>
                </div>

                <div class="shrink-0 flex items-center gap-2">
                    ${c.is_saved ? `
                        <a href="${cfg.routes.buy || '/mua-hang'}" class="px-3 py-1.5 bg-[#ee4d2d] hover:bg-red-600 text-white font-black text-xs rounded-xl shadow-xs inline-flex items-center gap-1">
                            Dùng <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    ` : `
                        <button type="button" onclick="handleGlobalSaveCoupon(${c.id}, '${c.coupon_code}', this)" class="px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#ee4d2d] border border-orange-200 font-black text-xs rounded-xl transition-all cursor-pointer">
                            + Lưu mã
                        </button>
                    `}
                </div>
            </div>
        `;
    });

    listDiv.innerHTML = html;
}

export function handleGlobalSaveCoupon(couponId, couponCode, btn) {
    const cfg = getConfig();
    if (!cfg.isLoggedIn) {
        alert('Mã khuyến mãi chỉ sử dụng được sau khi đăng nhập thành công. Vui lòng đăng nhập!');
        window.location.href = cfg.routes.login || '/dang-nhap';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(cfg.routes.couponsSave || '/api/coupons/save', {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": cfg.csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({ coupon_id: couponId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            btn.className = "px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-300 font-bold text-xs rounded-xl";
            btn.innerText = "Đã lưu ✓";
            loadGlobalCoupons();
        } else {
            btn.disabled = false;
            btn.innerText = "+ Lưu mã";
            alert(data.message || 'Lưu mã thất bại.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerText = "+ Lưu mã";
        alert('Có lỗi kết nối máy chủ.');
    });
}

export function handleGlobalSaveAllCoupons() {
    const cfg = getConfig();
    if (!cfg.isLoggedIn) {
        alert('Mã khuyến mãi chỉ sử dụng được sau khi đăng nhập thành công. Vui lòng đăng nhập!');
        window.location.href = cfg.routes.login || '/dang-nhap';
        return;
    }

    const btn = document.getElementById('global-save-all-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Đang thu thập...';
    }

    fetch(cfg.routes.couponsSaveAll || '/api/coupons/save-all', {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": cfg.csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(`Đã lưu thành công ${data.saved_count} mã vào ví ưu đãi của bạn!`);
            loadGlobalCoupons();
        } else {
            alert(data.message || 'Không thể lưu mã.');
        }
    })
    .catch(() => alert('Lỗi kết nối máy chủ.'))
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt mr-1"></i> Thu thập tất cả mã';
        }
    });
}

// =========================================================================
// 6. GẮN CÁC HÀM VÀO WINDOW CHO CÁC SỰ KIỆN INLINE ONCLICK / ONINPUT
// =========================================================================
window.updateGlobalHeaderCartBadge = updateGlobalHeaderCartBadge;
window.toggleHeaderSearch = toggleHeaderSearch;
window.handleLiveSearch = handleLiveSearch;
window.openSpinWheelModal = openSpinWheelModal;
window.closeSpinWheelModal = closeSpinWheelModal;
window.onPriceSliderChange = onPriceSliderChange;
window.startSpin = startSpin;
window.toggleChatbot = toggleChatbot;
window.sendChatbotMessage = sendChatbotMessage;
window.openGlobalUserVoucherModal = openGlobalUserVoucherModal;
window.closeGlobalUserVoucherModal = closeGlobalUserVoucherModal;
window.switchGlobalVoucherTab = switchGlobalVoucherTab;
window.loadGlobalCoupons = loadGlobalCoupons;
window.renderGlobalCoupons = renderGlobalCoupons;
window.handleGlobalSaveCoupon = handleGlobalSaveCoupon;
window.handleGlobalSaveAllCoupons = handleGlobalSaveAllCoupons;

// Khởi chạy khi DOM sẵn sàng
document.addEventListener('DOMContentLoaded', () => {
    updateGlobalHeaderCartBadge();
    const canvas = document.getElementById('spin-wheel-canvas');
    if (canvas) {
        drawWheel(0);
        onPriceSliderChange(100000);
    }
});

window.addEventListener('storage', updateGlobalHeaderCartBadge);

// Ngăn chặn Back-Forward Cache khi đăng xuất
window.addEventListener('pageshow', function (event) {
    if (event.persisted || (window.performance && window.performance.navigation && window.performance.navigation.type === 2)) {
        window.location.reload();
    }
});
