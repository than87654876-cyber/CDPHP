/**
 * FOODDAILY - Unified In-App Notification Center & Web Push Manager
 * Quản lý quả chuông thông báo (In-App Noti), Realtime WebSocket/Polling,
 * và Web Push Service Worker (Native OS Push).
 */

let lastSeenNotificationId = null;
let currentNotificationsList = [];
let isDropdownOpen = false;

// Lấy CSRF Token an toàn từ DOM hoặc Config
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
        || window.FoodDailyConfig?.csrfToken 
        || '';
}

// Tính toán URL API phù hợp với cả domain ảo và thư mục con (/CDPHP/public)
function getNotiApiUrl(endpoint = '') {
    if (window.FoodDailyConfig?.routes) {
        if (endpoint === '' && window.FoodDailyConfig.routes.notificationsList) return window.FoodDailyConfig.routes.notificationsList;
        if (endpoint === 'mark-all-read' && window.FoodDailyConfig.routes.notificationsMarkAllRead) return window.FoodDailyConfig.routes.notificationsMarkAllRead;
        if (endpoint === 'trigger-demo' && window.FoodDailyConfig.routes.notificationsTriggerDemo) return window.FoodDailyConfig.routes.notificationsTriggerDemo;
        if (endpoint === 'subscribe-push' && window.FoodDailyConfig.routes.notificationsSubscribePush) return window.FoodDailyConfig.routes.notificationsSubscribePush;
        if (endpoint.startsWith('poll') && window.FoodDailyConfig.routes.notificationsPoll) {
            const query = endpoint.includes('?') ? endpoint.substring(endpoint.indexOf('?')) : '';
            return window.FoodDailyConfig.routes.notificationsPoll + query;
        }
    }
    const base = window.location.pathname.startsWith('/CDPHP/public') ? '/CDPHP/public' : '';
    if (endpoint) {
        return `${base}/api/notifications/${endpoint}`;
    }
    return `${base}/api/notifications`;
}

// Kiểm tra quyền thông báo của trình duyệt
export function checkNotificationPermission() {
    if (!('Notification' in window)) {
        return 'unsupported';
    }
    return Notification.permission;
}

// Cập nhật trạng thái hiển thị của Web Push trên Dropdown Footer
export function updateWebPushStatusUI() {
    const statusText = document.getElementById('web-push-status-text');
    const container = document.getElementById('web-push-status-container');
    if (!statusText) return;

    const perm = checkNotificationPermission();
    if (perm === 'granted') {
        statusText.innerText = 'Đã bật Web Push';
        if (container) {
            container.innerHTML = `
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-emerald-600 font-semibold text-[10px]">Đã bật Web Push</span>
            `;
        }
    } else if (perm === 'denied') {
        if (container) {
            container.innerHTML = `
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span class="text-rose-500 font-semibold text-[10px]" title="Vui lòng mở cài đặt trình duyệt để cho phép">Bị chặn thông báo</span>
            `;
        }
    } else {
        if (container) {
            container.innerHTML = `
                <button type="button" onclick="requestMealNotificationPermission()" class="text-[10px] text-[#ee4d2d] font-bold hover:underline flex items-center gap-1 cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Bật Web Push</span>
                </button>
            `;
        }
    }
}

// Yêu cầu quyền thông báo từ người dùng & Lưu Subscription
export function requestMealNotificationPermission() {
    if (!('Notification' in window)) {
        alert('Trình duyệt của bạn hiện chưa hỗ trợ tính năng thông báo.');
        return;
    }

    Notification.requestPermission().then((permission) => {
        const banner = document.getElementById('pwa-notification-prompt-banner');
        if (permission === 'granted') {
            console.log('[WebPush] Người dùng đã cấp quyền thông báo!');
            if (banner) banner.classList.add('hidden');
            updateWebPushStatusUI();

            // Đăng ký Service Worker Push Subscription lên server
            registerPushSubscription();

            // Bắn ngay thông báo chào mừng
            displayPushNotification({
                id: 'welcome_' + Date.now(),
                title: '🎉 Kích hoạt thông báo thành công!',
                body: 'FOODDAILY sẽ tự động nhắc bạn vào giờ ăn trưa (11:00) & tối (18:00) kèm ưu đãi đặc biệt.',
                icon: '/icons/icon-192x192.png',
                url: '/trangchu',
                type: 'system'
            });
        } else {
            console.warn('[WebPush] Người dùng từ chối quyền thông báo:', permission);
            if (banner) banner.classList.add('hidden');
            updateWebPushStatusUI();
        }
    });
}

// Đóng banner xin quyền thông báo
export function dismissNotificationPrompt() {
    const banner = document.getElementById('pwa-notification-prompt-banner');
    if (banner) banner.classList.add('hidden');
    sessionStorage.setItem('fooddaily_noti_prompt_dismissed', 'true');
}

// Đăng ký Push Subscription qua Service Worker và gửi lên Backend
export function registerPushSubscription() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        return;
    }

    navigator.serviceWorker.ready.then((registration) => {
        // Kiểm tra xem đã có subscription chưa
        return registration.pushManager.getSubscription().then((existingSub) => {
            if (existingSub) {
                return existingSub;
            }
            // Đăng ký mới nếu chưa có
            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: null // Chế độ tương thích chuẩn Web Push
            }).catch(() => null);
        });
    }).then((subscription) => {
        if (!subscription) return;

        const subJson = subscription.toJSON();
        fetch(getNotiApiUrl('subscribe-push'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                endpoint: subscription.endpoint,
                keys: subJson.keys || {}
            })
        }).catch(err => console.warn('[WebPush] Lỗi gửi subscription lên server:', err));
    }).catch(err => console.warn('[WebPush] Lỗi service worker subscription:', err));
}

// --------------------------------------------------------------------------
// QUẢN LÝ GIAO DIỆN QUẢ CHUÔNG (IN-APP NOTIFICATION CENTER)
// --------------------------------------------------------------------------

// Bật / tắt Dropdown Menu Thông Báo
export function toggleNotificationDropdown(event) {
    if (event) event.stopPropagation();

    const dropdown = document.getElementById('global-notification-dropdown');
    if (!dropdown) return;

    isDropdownOpen = !isDropdownOpen;

    if (isDropdownOpen) {
        dropdown.classList.remove('hidden');
        fetchNotificationsList();
    } else {
        dropdown.classList.add('hidden');
    }
}

// Đóng dropdown khi click ngoài màn hình
if (typeof document !== 'undefined') {
    document.addEventListener('click', (e) => {
        const wrapper = document.getElementById('global-notification-wrapper');
        const dropdown = document.getElementById('global-notification-dropdown');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            if (wrapper && !wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
                isDropdownOpen = false;
            }
        }
    });
}

// Tải danh sách thông báo từ API Server
export function fetchNotificationsList() {
    fetch(getNotiApiUrl(''), {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            currentNotificationsList = data.notifications || [];
            updateNotificationBadgeUI(data.unread_count || 0);
            renderNotificationListUI(currentNotificationsList, data.unread_count || 0);
        }
    })
    .catch(err => {
        console.warn('[Notification] Không thể tải danh sách thông báo:', err);
    });
}

// Cập nhật Badge số lượng thông báo chưa đọc trên quả chuông
export function updateNotificationBadgeUI(count) {
    const badge = document.getElementById('global-notification-badge');
    const bellIcon = document.getElementById('global-notification-bell-icon');
    const subtitle = document.getElementById('global-notification-subtitle');

    if (badge) {
        if (count > 0) {
            badge.innerText = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
        }
    }

    if (subtitle) {
        subtitle.innerText = count > 0 ? `${count} thông báo mới chưa đọc` : 'Tất cả đã đọc';
    }
}

// Hiệu ứng rung chuông khi có thông báo mới tới
export function jiggleNotificationBell() {
    const bellIcon = document.getElementById('global-notification-bell-icon');
    if (bellIcon) {
        bellIcon.classList.add('text-[#ee4d2d]', 'scale-125');
        setTimeout(() => {
            bellIcon.classList.remove('text-[#ee4d2d]', 'scale-125');
        }, 1200);
    }
}

// Render HTML danh sách thông báo vào Dropdown
function renderNotificationListUI(list, unreadCount) {
    const container = document.getElementById('global-notification-list');
    if (!container) return;

    if (!list || list.length === 0) {
        container.innerHTML = `
            <div class="p-8 text-center text-slate-400">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-xl text-slate-400">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <p class="text-xs font-bold text-slate-600">Bạn chưa có thông báo nào</p>
                <p class="text-[11px] text-slate-400 mt-1">Các cập nhật đơn hàng & ưu đãi sẽ hiển thị tại đây.</p>
            </div>
        `;
        return;
    }

    let html = '';
    list.forEach((item) => {
        // Icon & Màu sắc tùy biến theo loại thông báo
        let iconHtml = '<div class="w-8 h-8 rounded-xl bg-orange-100 text-[#ee4d2d] flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-bell"></i></div>';
        
        if (item.type === 'order') {
            iconHtml = '<div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-motorcycle"></i></div>';
        } else if (item.type === 'promo') {
            iconHtml = '<div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-bolt"></i></div>';
        } else if (item.type === 'lunch' || item.type === 'dinner' || item.type === 'meal') {
            iconHtml = '<div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-utensils"></i></div>';
        }

        const isUnread = !item.is_read;
        const bgClass = isUnread ? 'bg-orange-50/40 hover:bg-orange-50/80' : 'bg-white hover:bg-slate-50';
        const titleClass = isUnread ? 'font-extrabold text-slate-900' : 'font-semibold text-slate-700';

        html += `
            <div class="p-3 sm:p-3.5 flex items-start gap-3 transition-colors cursor-pointer ${bgClass} group" 
                 onclick="handleNotificationClick(${item.id}, '${item.url || '/trangchu'}')">
                ${iconHtml}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <h4 class="text-xs ${titleClass} truncate group-hover:text-[#ee4d2d] transition-colors">${item.title}</h4>
                        ${isUnread ? '<span class="w-2 h-2 rounded-full bg-[#ee4d2d] shrink-0" title="Chưa đọc"></span>' : ''}
                    </div>
                    <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5 leading-relaxed">${item.message || item.body || ''}</p>
                    <span class="text-[10px] text-slate-400 font-medium block mt-1">${item.created_at || 'Vừa xong'}</span>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Xử lý khi người dùng click vào 1 thông báo
export function handleNotificationClick(id, url) {
    // 1. Gọi API đánh dấu đã đọc
    fetch(getNotiApiUrl(`mark-read/${id}`), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    }).then(res => res.json()).then(data => {
        if (data.success) {
            updateNotificationBadgeUI(data.unread_count || 0);
        }
    }).catch(() => {});

    // Cập nhật mảng local
    const target = currentNotificationsList.find(n => n.id == id);
    if (target) target.is_read = true;

    // 2. Chuyển hướng tới trang đích
    if (url && url !== '#') {
        setTimeout(() => {
            window.location.href = url;
        }, 120);
    }
}

// Đánh dấu tất cả thông báo là đã đọc
export function markAllNotificationsAsRead(event) {
    if (event) event.stopPropagation();

    fetch(getNotiApiUrl('mark-all-read'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            currentNotificationsList.forEach(item => item.is_read = true);
            updateNotificationBadgeUI(0);
            renderNotificationListUI(currentNotificationsList, 0);
        }
    })
    .catch(err => console.warn('[Notification] Lỗi đánh dấu tất cả đã đọc:', err));
}

// Bắn thử thông báo Demo (Từ dropdown hoặc admin)
export function triggerTestNotification(event) {
    if (event) event.stopPropagation();

    const types = ['lunch', 'dinner', 'promo', 'order'];
    const randomType = types[Math.floor(Math.random() * types.length)];

    fetch(getNotiApiUrl('trigger-demo'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ type: randomType })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.data) {
            onNewNotificationReceived(data.data);
        }
    })
    .catch(err => console.warn('[Notification] Lỗi bắn thử noti:', err));
}

// Hàm tiếp nhận thông báo mới từ mọi nguồn (Realtime Echo / Polling / Test)
export function onNewNotificationReceived(notification) {
    console.log('[Notification] Nhận thông báo mới:', notification);

    // 1. Rung chuông & tăng số unread badge
    jiggleNotificationBell();
    
    // Thêm vào danh sách hiện tại
    const exists = currentNotificationsList.some(n => n.id == notification.id);
    if (!exists) {
        currentNotificationsList.unshift({
            id: notification.id,
            title: notification.title,
            message: notification.message || notification.body,
            url: notification.url || '/trangchu',
            type: notification.type || 'system',
            is_read: false,
            created_at: 'Vừa xong'
        });
        const currentCount = (parseInt(document.getElementById('global-notification-badge')?.innerText) || 0) + 1;
        updateNotificationBadgeUI(currentCount);
        renderNotificationListUI(currentNotificationsList, currentCount);
    }

    // 2. Hiển thị Web Push ra màn hình máy tính / điện thoại
    displayPushNotification(notification);
}

// --------------------------------------------------------------------------
// WEB PUSH & TOAST NOTIFICATION ENGINE
// --------------------------------------------------------------------------

// Hiển thị thông báo (Ưu tiên qua Service Worker, Fallback sang new Notification và In-App Toast)
export function displayPushNotification(data) {
    const title = data.title || 'FOODDAILY - Thông báo';
    const body = data.message || data.body || 'Bạn có một thông báo mới từ FOODDAILY!';
    const options = {
        body: body,
        icon: data.icon || '/icons/icon-192x192.png',
        badge: '/icons/icon-192x192.png',
        vibrate: [200, 100, 200],
        data: { url: data.url || '/' },
        tag: data.type || 'fooddaily-alert',
        renotify: true
    };

    // 1. Hiển thị Native OS Push (Góc màn hình Windows / Mac / Android)
    if ('Notification' in window && Notification.permission === 'granted') {
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.ready.then((registration) => {
                registration.showNotification(title, options);
            }).catch(() => {
                try { new Notification(title, options); } catch(e) {}
            });
        } else {
            try {
                new Notification(title, options);
            } catch (e) {
                console.warn('[Push] Native notification error:', e);
            }
        }
    }

    // 2. Luôn hiển thị Toast Popup trên trang web
    showInAppNotificationToast(title, body, data.url);
}

// Hiển thị In-App Toast trên góc màn hình
function showInAppNotificationToast(title, body, url) {
    const existing = document.getElementById('fooddaily-live-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'fooddaily-live-toast';
    toast.className = 'fixed top-20 right-4 z-50 max-w-sm w-full bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-2xl border border-orange-200 animate-in slide-in-from-top-4 duration-300 flex items-start gap-3';
    toast.innerHTML = `
        <img src="/icons/icon-192x192.png" class="w-10 h-10 rounded-xl object-cover shrink-0 shadow-xs border border-orange-100">
        <div class="flex-1 min-w-0 cursor-pointer" onclick="window.location.href='${url || '/'}'">
            <h4 class="font-bold text-xs text-slate-900 leading-snug">${title}</h4>
            <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2 leading-relaxed">${body}</p>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 text-sm p-1 cursor-pointer">
            <i class="fas fa-times"></i>
        </button>
    `;

    document.body.appendChild(toast);
    setTimeout(() => {
        if (toast && toast.parentElement) toast.remove();
    }, 7000);
}

// Tự động kiểm tra kịch bản giờ ăn địa phương (11:00 Trưa & 18:00 Tối)
export function checkScheduledMealAlarms() {
    const now = new Date();
    const hour = now.getHours();
    const today = now.toISOString().slice(0, 10);

    // Khung giờ trưa: 11h
    if (hour === 11) {
        const lunchKey = 'fooddaily_lunch_alert_' + today;
        if (!localStorage.getItem(lunchKey)) {
            localStorage.setItem(lunchKey, 'true');
            onNewNotificationReceived({
                id: 'sched_lunch_' + today,
                title: '🍱 Đến giờ cơm trưa rồi bạn ơi!',
                body: 'Bạn đã đói chưa? Xem ngay thực đơn hôm nay và nhận ưu đãi giảm 20% nhé!',
                url: '/trangchu',
                type: 'lunch'
            });
        }
    }

    // Khung giờ tối: 18h
    if (hour === 18) {
        const dinnerKey = 'fooddaily_dinner_alert_' + today;
        if (!localStorage.getItem(dinnerKey)) {
            localStorage.setItem(dinnerKey, 'true');
            onNewNotificationReceived({
                id: 'sched_dinner_' + today,
                title: '🍲 Bữa tối ấm cúng cùng gia đình!',
                body: 'Nghỉ ngơi thôi! Hàng trăm món ngon nóng hổi đang chờ bạn, freeship hôm nay từ 150k.',
                url: '/trangchu',
                type: 'dinner'
            });
        }
    }
}

// Lắng nghe WebSocket Realtime qua Laravel Echo / Reverb
export function initEchoNotificationListener() {
    if (window.Echo) {
        try {
            window.Echo.channel('notifications-channel')
                .listen('.PushNotificationSent', (e) => {
                    onNewNotificationReceived(e);
                })
                .listen('PushNotificationSent', (e) => {
                    onNewNotificationReceived(e);
                });
        } catch (err) {
            console.warn('[Push] Lỗi khởi tạo Echo channel:', err);
        }
    }
}

// Polling định kỳ kiểm tra thông báo mới
export function pollServerNotifications() {
    const url = getNotiApiUrl('poll?last_id=' + encodeURIComponent(lastSeenNotificationId || ''));
    fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(res => res.json())
        .then(data => {
            if (data.unread_count !== undefined) {
                updateNotificationBadgeUI(data.unread_count);
            }
            if (data.has_new && data.notification) {
                lastSeenNotificationId = data.notification.id;
                onNewNotificationReceived(data.notification);
            }
        })
        .catch(() => {});
}

// Khởi chạy khi load trang
export function initNotifications() {
    // 1. Kiểm tra trạng thái banner xin quyền
    if ('Notification' in window) {
        if (Notification.permission === 'default') {
            const dismissed = sessionStorage.getItem('fooddaily_noti_prompt_dismissed');
            const banner = document.getElementById('pwa-notification-prompt-banner');
            if (banner && !dismissed) {
                setTimeout(() => banner.classList.remove('hidden'), 2000);
            }
        }
    }

    // 2. Cập nhật UI trạng thái Web Push
    updateWebPushStatusUI();

    // 3. Tải dữ liệu ban đầu cho quả chuông
    fetchNotificationsList();

    // 4. Lắng nghe WebSocket
    initEchoNotificationListener();

    // 5. Khởi tạo Polling định kỳ mỗi 20 giây
    setInterval(pollServerNotifications, 20000);

    // 6. Kiểm tra giờ ăn địa phương (11h & 18h)
    checkScheduledMealAlarms();
}

// Gắn toàn bộ ra window cho các sự kiện onclick từ Blade
window.toggleNotificationDropdown = toggleNotificationDropdown;
window.handleNotificationClick = handleNotificationClick;
window.markAllNotificationsAsRead = markAllNotificationsAsRead;
window.triggerTestNotification = triggerTestNotification;
window.requestMealNotificationPermission = requestMealNotificationPermission;
window.dismissNotificationPrompt = dismissNotificationPrompt;
window.displayPushNotification = displayPushNotification;

if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifications);
    } else {
        initNotifications();
    }
}
