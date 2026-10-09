/**
 * FOODDAILY - Shop & Realtime Sync Engine
 * Quản lý danh mục, infinite scroll, modal vouchers, và bộ đồng bộ realtime món ăn (WebSocket + Fast-Sync).
 */

const INITIAL_LIMIT = 15;
const BATCH_SIZE = 10;
let currentActiveTab = 'all';
let visibleLimits = { 'all': INITIAL_LIMIT };
let isLoadingMore = false;
let scrollObserver = null;

// =========================================================================
// 1. DANH MỤC & PHÂN TRANG INFINITE SCROLL
// =========================================================================
export function filterCategory(targetId) {
    let key = targetId;
    if (key !== 'all' && !String(key).startsWith('cat-')) {
        key = 'cat-' + key;
    }

    currentActiveTab = key;
    if (!visibleLimits[key]) {
        visibleLimits[key] = INITIAL_LIMIT;
    }

    // Ẩn tất cả các khung hiển thị món
    document.querySelectorAll('.cat-view-pane').forEach(pane => pane.classList.add('hidden'));

    // Hiển thị khung danh mục được chọn
    const targetPane = document.getElementById('cat-view-' + key);
    if (targetPane) {
        targetPane.classList.remove('hidden');
    }

    // Cập nhật kiểu dáng nút tab bên dưới
    document.querySelectorAll('.cat-tab-btn').forEach(btn => {
        btn.classList.remove('bg-[#ee4d2d]', 'text-white');
        btn.classList.add('bg-white', 'text-slate-700', 'hover:bg-slate-100', 'border', 'border-slate-200');
    });

    const activeBtn = document.getElementById('tab-btn-' + key);
    if (activeBtn) {
        activeBtn.classList.remove('bg-white', 'text-slate-700', 'hover:bg-slate-100', 'border', 'border-slate-200');
        activeBtn.classList.add('bg-[#ee4d2d]', 'text-white');
    }

    updateActiveTabVisibility();
}

export function updateActiveTabVisibility() {
    const activePane = document.getElementById('cat-view-' + currentActiveTab);
    if (!activePane) return;

    const items = activePane.querySelectorAll('.dish-card-item');
    const total = items.length;
    const currentLimit = visibleLimits[currentActiveTab] || INITIAL_LIMIT;

    items.forEach((item, idx) => {
        if (idx < currentLimit) {
            if (item.classList.contains('hidden')) {
                item.classList.remove('hidden');
                item.classList.add('dish-card-appear');
            }
        } else {
            item.classList.add('hidden');
            item.classList.remove('dish-card-appear');
        }
    });

    const sentinel = document.getElementById('scroll-sentinel');
    const btnMore = document.getElementById('btn-load-more');
    const allLoaded = document.getElementById('all-loaded-indicator');

    if (total === 0 || currentLimit >= total) {
        if (sentinel) sentinel.classList.add('hidden');
        if (btnMore) btnMore.classList.add('hidden');
        if (allLoaded) {
            if (total > INITIAL_LIMIT) {
                allLoaded.classList.remove('hidden');
            } else {
                allLoaded.classList.add('hidden');
            }
        }
    } else {
        if (sentinel) sentinel.classList.remove('hidden');
        if (btnMore) btnMore.classList.add('hidden');
        if (allLoaded) allLoaded.classList.add('hidden');
    }
}

export function loadMoreDishes() {
    if (isLoadingMore) return;

    const activePane = document.getElementById('cat-view-' + currentActiveTab);
    if (!activePane) return;

    const items = activePane.querySelectorAll('.dish-card-item');
    const total = items.length;
    const currentLimit = visibleLimits[currentActiveTab] || INITIAL_LIMIT;

    if (currentLimit >= total) return;

    isLoadingMore = true;
    const sentinel = document.getElementById('scroll-sentinel');
    if (sentinel) {
        sentinel.classList.remove('hidden');
        sentinel.classList.add('opacity-100');
    }

    setTimeout(() => {
        visibleLimits[currentActiveTab] = (visibleLimits[currentActiveTab] || INITIAL_LIMIT) + BATCH_SIZE;
        updateActiveTabVisibility();
        isLoadingMore = false;
    }, 300);
}

export function initInfiniteScrollObserver() {
    const sentinel = document.getElementById('scroll-sentinel');
    if (!sentinel) return;

    if ('IntersectionObserver' in window) {
        scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !isLoadingMore) {
                    loadMoreDishes();
                }
            });
        }, {
            rootMargin: '250px 0px',
            threshold: 0.1
        });

        scrollObserver.observe(sentinel);
    } else {
        const btnMore = document.getElementById('btn-load-more');
        if (btnMore) btnMore.classList.remove('hidden');
    }
}

export function handleSortChange(sortVal) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortVal);
    window.location.href = url.toString();
}

export function openGroupModal() {
    const m = document.getElementById('groupModal');
    if (m) m.classList.remove('hidden');
}

export function closeGroupModal() {
    const m = document.getElementById('groupModal');
    if (m) m.classList.add('hidden');
}

// =========================================================================
// 2. KHO ƯU ĐÃI & VOUCHERS TRANG CHỦ
// =========================================================================
export function filterCouponTab(tab) {
    document.querySelectorAll('#coupons-section button[id^="coupon-tab-"]').forEach(btn => {
        btn.className = 'px-3.5 py-2 rounded-xl text-slate-600 hover:text-[#ee4d2d] transition-all cursor-pointer';
        btn.style.background = 'transparent';
        btn.style.color = '';
    });
    const activeBtn = document.getElementById('coupon-tab-' + tab);
    if (activeBtn) {
        activeBtn.className = 'px-3.5 py-2 rounded-xl text-white transition-all shadow-xs cursor-pointer';
        activeBtn.style.background = '#ee4d2d';
        activeBtn.style.color = '#ffffff';
    }

    const cards = document.querySelectorAll('.coupon-card');
    let visibleCount = 0;
    cards.forEach(card => {
        const isSaved = card.getAttribute('data-is-saved') === '1';
        const isExpiring = card.getAttribute('data-is-expiring') === '1';

        if (tab === 'all') {
            card.style.display = 'flex';
            visibleCount++;
        } else if (tab === 'saved') {
            if (isSaved) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        } else if (tab === 'expiring') {
            if (isExpiring) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        }
    });

    const emptyNotice = document.getElementById('coupons-empty');
    if (emptyNotice) {
        emptyNotice.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

export function copyCouponCode(code, btn) {
    if (!code) return;
    navigator.clipboard.writeText(code).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-emerald-500"></i>';
        
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-28 left-1/2 -translate-x-1/2 z-50 bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-2xl flex items-center gap-2 transition-all';
        toast.innerHTML = `<i class="fas fa-check-circle text-emerald-400"></i> Đã sao chép mã: <strong>#${code}</strong>`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 1800);

        setTimeout(() => { btn.innerHTML = originalHtml; }, 1500);
    }).catch(err => {
        console.error('Copy coupon error:', err);
    });
}

export function saveCoupon(couponId, couponCode, btn) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch("/api/coupons/save", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({ coupon_id: couponId, coupon_code: couponCode })
    })
    .then(res => res.json())
    .then(data => {
        if (data.require_login) {
            if (confirm(data.message + '\n\nBạn có muốn chuyển tới trang đăng nhập ngay không?')) {
                window.location.href = "/dang-nhap";
            }
            return;
        }

        if (data.success) {
            document.querySelectorAll(`button[onclick*="'${couponCode}'"]`).forEach(b => {
                b.className = 'btn-save-coupon px-3.5 py-1.5 rounded-xl text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-300 cursor-default flex items-center gap-1 shadow-2xs';
                b.innerHTML = '<i class="fas fa-check text-[11px]"></i><span>Đã lưu</span>';
                b.disabled = true;
                b.style.background = '';
            });

            const card = document.getElementById('coupon-card-' + couponId);
            if (card) {
                card.setAttribute('data-is-saved', '1');
            }

            const badge = document.getElementById('saved-badge-count');
            if (badge) {
                badge.innerText = parseInt(badge.innerText || 0) + 1;
            }
        }
    })
    .catch(err => console.error('Save coupon error:', err));
}

export function saveAllCoupons() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch("/api/coupons/save-all", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.require_login) {
            if (confirm(data.message + '\n\nBạn có muốn chuyển tới trang đăng nhập ngay không?')) {
                window.location.href = "/dang-nhap";
            }
            return;
        }

        if (data.success) {
            alert(data.message);
            document.querySelectorAll('.btn-save-coupon').forEach(b => {
                b.className = 'btn-save-coupon px-3.5 py-1.5 rounded-xl text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-300 cursor-default flex items-center gap-1 shadow-2xs';
                b.innerHTML = '<i class="fas fa-check text-[11px]"></i><span>Đã lưu</span>';
                b.disabled = true;
                b.style.background = '';
            });
            document.querySelectorAll('.coupon-card').forEach(c => {
                c.setAttribute('data-is-saved', '1');
            });
            const badge = document.getElementById('saved-badge-count');
            if (badge && data.total_active) {
                badge.innerText = data.total_active;
            }
        }
    })
    .catch(err => console.error('Save all coupons error:', err));
}

export function openVoucherModal(force = false) {
    if (!force) {
        try {
            if (sessionStorage.getItem('fooddaily_voucher_dismissed') || localStorage.getItem('fooddaily_voucher_dismissed')) {
                return;
            }
        } catch (e) {}
    }

    const modal = document.getElementById('voucherModal');
    const content = document.getElementById('voucherModalContent');
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.style.removeProperty('display');
    modal.style.display = 'flex';
    modal.style.opacity = '1';

    setTimeout(() => {
        if (content) {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
            content.style.opacity = '1';
            content.style.transform = 'scale(1)';
        }
    }, 20);
}

export function closeVoucherModal() {
    try {
        sessionStorage.setItem('fooddaily_voucher_dismissed', 'true');
        localStorage.setItem('fooddaily_voucher_dismissed', 'true');
    } catch (e) {}

    const modal = document.getElementById('voucherModal');
    const content = document.getElementById('voucherModalContent');

    if (!modal) return;

    if (content) {
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        content.style.opacity = '0';
        content.style.transform = 'scale(0.95)';
    }

    modal.style.setProperty('display', 'none', 'important');
    modal.style.opacity = '0';
    modal.classList.add('hidden');
}

// =========================================================================
// 3. REALTIME DISHES SYNCHRONIZER (WEBSOCKET + FAST-SYNC ENGINE)
// =========================================================================
let lastDishesSyncTime = Date.now() / 1000;
const processedDishMutationIds = new Set();
let isDishesSyncPolling = false;

export function showRealtimeToast(title, message, dishId = null, type = 'info') {
    const container = document.getElementById('realtime-toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    const borderClass = type === 'success' 
        ? 'border-emerald-300' 
        : (type === 'warning' ? 'border-amber-300' : (type === 'danger' ? 'border-rose-300' : 'border-blue-300'));
    const icon = type === 'success' ? '🎉' : (type === 'warning' ? '✏️' : (type === 'danger' ? '🗑️' : '🔔'));

    toast.className = `flex items-center gap-3 bg-white/95 backdrop-blur-md border ${borderClass} shadow-xl rounded-2xl p-3.5 text-xs text-slate-800 transition-all duration-300 transform translate-x-12 opacity-0 pointer-events-auto`;
    toast.innerHTML = `
        <span class="text-xl shrink-0">${icon}</span>
        <div class="min-w-0 pr-2">
            <p class="font-extrabold text-slate-900">${title}</p>
            <p class="text-slate-500 font-medium truncate">${message}</p>
        </div>
        ${dishId ? `<button type="button" onclick="scrollToRealtimeDish(${dishId})" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-[#ee4d2d] text-[#ee4d2d] hover:text-white font-black text-[11px] transition-colors shrink-0 ml-auto cursor-pointer">Xem</button>` : ''}
        <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-700 text-sm ml-1 cursor-pointer">&times;</button>
    `;

    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.remove('translate-x-12', 'opacity-0');
    }, 10);

    setTimeout(() => {
        toast.classList.add('translate-x-12', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

export function scrollToRealtimeDish(dishId) {
    const card = document.querySelector(`.dish-card-item[data-dish-id="${dishId}"]`);
    if (card) {
        const parentPane = card.closest('.cat-view-pane');
        if (parentPane && parentPane.classList.contains('hidden')) {
            filterCategory('all');
        }
        card.classList.remove('hidden');
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        card.classList.add('ring-4', 'ring-[#ee4d2d]', 'ring-offset-2');
        setTimeout(() => {
            card.classList.remove('ring-4', 'ring-[#ee4d2d]', 'ring-offset-2');
        }, 3000);
    }
}

export function applyDishMutation(mutation) {
    if (!mutation || !mutation.action) return;

    const mId = mutation.mutation_id || (mutation.action + '-' + mutation.dish_id + '-' + mutation.timestamp);
    if (processedDishMutationIds.has(mId)) {
        return;
    }
    processedDishMutationIds.add(mId);

    if (mutation.timestamp && mutation.timestamp > lastDishesSyncTime) {
        lastDishesSyncTime = mutation.timestamp;
    }

    const action = mutation.action;
    const dish = mutation.dish;
    const dishId = mutation.dish_id;

    if (action === 'created') {
        if (!dish || !mutation.rendered_card) return;

        const existingCard = document.querySelector(`.dish-card-item[data-dish-id="${dish.id}"]`);
        if (existingCard) {
            updateExistingDishCards(dish);
            return;
        }

        // 1. Chèn vào Tab "Tất cả"
        const allGrid = document.getElementById('all-dishes-grid');
        const emptyAll = document.getElementById('all-dishes-empty');
        if (emptyAll) emptyAll.remove();

        if (allGrid) {
            allGrid.insertAdjacentHTML('afterbegin', mutation.rendered_card);
            const newCardAll = allGrid.querySelector(`.dish-card-item[data-dish-id="${dish.id}"]`);
            if (newCardAll) {
                newCardAll.classList.remove('hidden');
                newCardAll.classList.add('ring-4', 'ring-emerald-400', 'ring-offset-2', 'dish-card-appear');
                setTimeout(() => newCardAll.classList.remove('ring-4', 'ring-emerald-400', 'ring-offset-2'), 4000);
            }
        }

        // 2. Chèn vào Grid của Danh mục tương ứng
        if (dish.category_id) {
            const catGrid = document.getElementById('cat-grid-' + dish.category_id);
            const catEmpty = document.getElementById('cat-empty-' + dish.category_id);
            if (catEmpty) catEmpty.remove();

            if (catGrid) {
                catGrid.insertAdjacentHTML('afterbegin', mutation.rendered_card);
                const newCardCat = catGrid.querySelector(`.dish-card-item[data-dish-id="${dish.id}"]`);
                if (newCardCat) {
                    newCardCat.classList.remove('hidden');
                    newCardCat.classList.add('ring-4', 'ring-emerald-400', 'ring-offset-2', 'dish-card-appear');
                    setTimeout(() => newCardCat.classList.remove('ring-4', 'ring-emerald-400', 'ring-offset-2'), 4000);
                }
            }
        }

        // 3. Cập nhật số đếm kết quả
        updateDishesCountBadge(1);

        // 4. Cập nhật phân trang hiển thị
        visibleLimits['all'] = (visibleLimits['all'] || INITIAL_LIMIT) + 1;
        if (dish.category_id) {
            visibleLimits['cat-' + dish.category_id] = (visibleLimits['cat-' + dish.category_id] || INITIAL_LIMIT) + 1;
        }
        updateActiveTabVisibility();

        // 5. Toast thông báo
        showRealtimeToast('Món mới vừa lên thực đơn!', `${dish.dish_name} - ${dish.formatted_price}`, dish.id, 'success');

    } else if (action === 'updated') {
        if (!dish) return;

        if (!dish.is_available) {
            removeDishCards(dish.id, dish.dish_name);
            return;
        }

        const existingCards = document.querySelectorAll(`.dish-card-item[data-dish-id="${dish.id}"]`);
        if (existingCards.length > 0) {
            updateExistingDishCards(dish);
            showRealtimeToast('Cập nhật món ăn!', `${dish.dish_name} vừa được thay đổi thông tin/giá bán`, dish.id, 'warning');
        } else if (mutation.rendered_card) {
            mutation.action = 'created';
            applyDishMutation(mutation);
        }

    } else if (action === 'deleted') {
        const dishName = dish ? dish.dish_name : 'Món ăn';
        removeDishCards(dishId, dishName);
    }
}

export function updateExistingDishCards(dish) {
    document.querySelectorAll(`.dish-card-item[data-dish-id="${dish.id}"]`).forEach(card => {
        card.setAttribute('data-price', dish.price);
        card.setAttribute('data-name', dish.dish_name);

        const nameEl = card.querySelector('.dish-card-name');
        if (nameEl) nameEl.innerText = dish.dish_name;

        const priceEl = card.querySelector('.dish-card-price');
        if (priceEl) priceEl.innerText = dish.formatted_price;

        const descEl = card.querySelector('.dish-card-desc');
        if (descEl) descEl.innerText = dish.description || 'FOODDAILY Store - TP. HCM';

        const imgEl = card.querySelector('.dish-card-img');
        if (imgEl && dish.display_image) imgEl.src = dish.display_image;

        card.classList.add('ring-4', 'ring-amber-400', 'ring-offset-2');
        setTimeout(() => card.classList.remove('ring-4', 'ring-amber-400', 'ring-offset-2'), 3000);
    });

    // Cập nhật hero promo grid nếu có
    document.querySelectorAll(`#hero-promo-grid [data-dish-id="${dish.id}"]`).forEach(promoEl => {
        const titleEl = promoEl.querySelector('h4');
        if (titleEl) titleEl.innerText = dish.dish_name;
        const pEl = promoEl.querySelector('p');
        if (pEl) pEl.innerText = dish.formatted_price;
        const imgEl = promoEl.querySelector('img');
        if (imgEl && dish.display_image) imgEl.src = dish.display_image;
    });
}

export function removeDishCards(dishId, dishName) {
    const cards = document.querySelectorAll(`.dish-card-item[data-dish-id="${dishId}"]`);
    if (cards.length === 0) return;

    cards.forEach(card => {
        card.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        card.style.transform = 'scale(0.85)';
        card.style.opacity = '0';
        setTimeout(() => {
            card.remove();
            updateActiveTabVisibility();
        }, 400);
    });

    document.querySelectorAll(`#hero-promo-grid [data-dish-id="${dishId}"]`).forEach(promoEl => {
        promoEl.style.transition = 'all 0.4s';
        promoEl.style.opacity = '0';
        setTimeout(() => promoEl.remove(), 400);
    });

    updateDishesCountBadge(-1);
    showRealtimeToast('Món ăn đã được gỡ!', `${dishName} đã tạm dừng phục vụ trên thực đơn`, null, 'danger');
}

export function updateDishesCountBadge(delta) {
    const badge = document.getElementById('menu-dishes-count');
    if (!badge) return;
    const match = badge.innerText.match(/\d+/);
    if (match) {
        let current = parseInt(match[0]) || 0;
        current = Math.max(0, current + delta);
        badge.innerText = current + ' Kết quả';
    }
}

export function pollDishesLiveFeed() {
    if (isDishesSyncPolling) return;
    if (!document.getElementById('all-dishes-grid')) return; // Chỉ chạy khi ở trang có thực đơn

    isDishesSyncPolling = true;
    fetch(`/api/dishes/live-feed?last_time=${encodeURIComponent(lastDishesSyncTime)}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        isDishesSyncPolling = false;
        if (data.timestamp) {
            lastDishesSyncTime = Math.max(lastDishesSyncTime, data.timestamp);
        }
        if (data.has_changes && data.mutations && data.mutations.length > 0) {
            data.mutations.forEach(m => applyDishMutation(m));
        }
    })
    .catch(() => {
        isDishesSyncPolling = false;
    });
}

// =========================================================================
// 4. GẮN CÁC HÀM VÀO WINDOW CHO INLINE ONCLICK / ONCHANGE TRÊN GIAO DIỆN
// =========================================================================
window.filterCategory = filterCategory;
window.updateActiveTabVisibility = updateActiveTabVisibility;
window.loadMoreDishes = loadMoreDishes;
window.handleSortChange = handleSortChange;
window.openGroupModal = openGroupModal;
window.closeGroupModal = closeGroupModal;
window.filterCouponTab = filterCouponTab;
window.copyCouponCode = copyCouponCode;
window.saveCoupon = saveCoupon;
window.saveAllCoupons = saveAllCoupons;
window.openVoucherModal = openVoucherModal;
window.closeVoucherModal = closeVoucherModal;
window.showRealtimeToast = showRealtimeToast;
window.scrollToRealtimeDish = scrollToRealtimeDish;
window.applyDishMutation = applyDishMutation;

// Khởi chạy trên trang có menu
document.addEventListener("DOMContentLoaded", function() {
    if (document.getElementById('all-dishes-grid')) {
        updateActiveTabVisibility();
        initInfiniteScrollObserver();

        window.addEventListener('dish:live_update', function(e) {
            if (e.detail) {
                applyDishMutation(e.detail);
            }
        });

        // Fast-Sync Polling Engine mỗi 2.5s
        setInterval(pollDishesLiveFeed, 2500);
    }
});
