/**
 * FOODDAILY - Web Push Notification & Scheduled Meal Alert Manager
 * Quản lý xin quyền thông báo, lắng nghe WebSocket Reverb, Polling API
 * và kịch bản hẹn giờ tự động (11h trưa & 18h tối).
 */

let lastSeenNotificationId = null;

// Kiểm tra quyền thông báo
export function checkNotificationPermission() {
    if (!('Notification' in window)) {
        console.warn('[Push] Trình duyệt không hỗ trợ Web Notifications.');
        return 'unsupported';
    }
    return Notification.permission;
}

// Yêu cầu quyền thông báo từ người dùng
export function requestMealNotificationPermission() {
    if (!('Notification' in window)) {
        alert('Trình duyệt của bạn hiện chưa hỗ trợ tính năng thông báo.');
        return;
    }

    Notification.requestPermission().then((permission) => {
        const banner = document.getElementById('pwa-notification-prompt-banner');
        if (permission === 'granted') {
            console.log('[Push] Người dùng đã cấp quyền thông báo!');
            if (banner) banner.classList.add('hidden');

            // Bắn ngay thông báo chào mừng để xác nhận kích hoạt thành công
            displayPushNotification({
                title: '🎉 Kích hoạt thông báo thành công!',
                body: 'FOODDAILY sẽ tự động nhắc bạn vào giờ ăn trưa (11:00) & tối (18:00) kèm ưu đãi đặc biệt.',
                icon: '/icons/icon-192x192.png',
                url: '/trangchu'
            });
        } else {
            console.warn('[Push] Người dùng từ chối quyền thông báo:', permission);
            if (banner) banner.classList.add('hidden');
        }
    });
}

// Đóng banner xin quyền thông báo
export function dismissNotificationPrompt() {
    const banner = document.getElementById('pwa-notification-prompt-banner');
    if (banner) banner.classList.add('hidden');
    sessionStorage.setItem('fooddaily_noti_prompt_dismissed', 'true');
}

// Hiển thị thông báo (Ưu tiên qua Service Worker, Fallback sang new Notification và In-App Toast)
export function displayPushNotification(data) {
    const title = data.title || 'FOODDAILY - Thông báo';
    const options = {
        body: data.body || 'Bạn có một thông báo mới từ FOODDAILY!',
        icon: data.icon || '/icons/icon-192x192.png',
        badge: '/icons/icon-192x192.png',
        vibrate: [200, 100, 200],
        data: { url: data.url || '/' },
        tag: data.type || 'fooddaily-alert',
        renotify: true
    };

    // 1. Hiển thị thông báo hệ điều hành (Native OS Push)
    if ('Notification' in window && Notification.permission === 'granted') {
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.ready.then((registration) => {
                registration.showNotification(title, options);
            }).catch(() => {
                new Notification(title, options);
            });
        } else {
            try {
                new Notification(title, options);
            } catch (e) {
                console.warn('[Push] Native notification error:', e);
            }
        }
    }

    // 2. Đồng thời hiển thị Toast Popup trên trang web
    showInAppNotificationToast(title, options.body, data.url);
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
        <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 text-sm p-1">
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

    // Khung giờ trưa: từ 11h đến 12h
    if (hour === 11) {
        const lunchKey = 'fooddaily_lunch_alert_' + today;
        if (!localStorage.getItem(lunchKey)) {
            localStorage.setItem(lunchKey, 'true');
            displayPushNotification({
                title: '🍱 Đến giờ cơm trưa rồi bạn ơi!',
                body: 'Bạn đã đói chưa? Xem ngay thực đơn hôm nay và nhận ưu đãi giảm 20% nhé!',
                url: '/trangchu',
                type: 'lunch'
            });
        }
    }

    // Khung giờ tối: từ 18h đến 19h
    if (hour === 18) {
        const dinnerKey = 'fooddaily_dinner_alert_' + today;
        if (!localStorage.getItem(dinnerKey)) {
            localStorage.setItem(dinnerKey, 'true');
            displayPushNotification({
                title: '🍲 Bữa tối ấm cúng cùng gia đình!',
                body: 'Nghỉ ngơi thôi! Hàng trăm món ngon nóng hổi đang chờ bạn, freeship hôm nay từ 150k.',
                url: '/trangchu',
                type: 'dinner'
            });
        }
    }
}

// Lắng nghe WebSocket qua Laravel Echo
export function initEchoNotificationListener() {
    if (window.Echo) {
        try {
            window.Echo.channel('notifications-channel')
                .listen('.PushNotificationSent', (e) => {
                    console.log('[Push] Nhận thông báo realtime qua Echo:', e);
                    displayPushNotification(e);
                })
                .listen('PushNotificationSent', (e) => {
                    console.log('[Push] Nhận thông báo realtime qua Echo:', e);
                    displayPushNotification(e);
                });
        } catch (err) {
            console.warn('[Push] Lỗi khởi tạo Echo channel:', err);
        }
    }
}

// Polling định kỳ kiểm tra thông báo mới
export function pollServerNotifications() {
    const url = `/api/notifications/poll?last_id=${encodeURIComponent(lastSeenNotificationId || '')}`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.has_new && data.notification) {
                lastSeenNotificationId = data.notification.id;
                displayPushNotification(data.notification);
            }
        })
        .catch(() => {});
}

// Kích hoạt bắn thông báo thử nghiệm (Gọi từ Admin hoặc Demo)
export function triggerDemoNotification(type = 'lunch') {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    return fetch('/api/notifications/trigger-demo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ type })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.data) {
            displayPushNotification(data.data);
            return data;
        }
        throw new Error(data.message || 'Lỗi gửi thông báo');
    });
}

// Khởi chạy khi load trang
export function initNotifications() {
    // 1. Kiểm tra trạng thái quyền
    if ('Notification' in window) {
        if (Notification.permission === 'default') {
            const dismissed = sessionStorage.getItem('fooddaily_noti_prompt_dismissed');
            const banner = document.getElementById('pwa-notification-prompt-banner');
            if (banner && !dismissed) {
                setTimeout(() => banner.classList.remove('hidden'), 1500);
            }
        }
    }

    // 2. Lắng nghe WebSocket
    initEchoNotificationListener();

    // 3. Khởi tạo Polling định kỳ mỗi 20 giây
    setInterval(pollServerNotifications, 20000);

    // 4. Kiểm tra giờ ăn địa phương (11h & 18h)
    checkScheduledMealAlarms();
}

// Gắn ra window cho các sự kiện onclick
window.requestMealNotificationPermission = requestMealNotificationPermission;
window.dismissNotificationPrompt = dismissNotificationPrompt;
window.displayPushNotification = displayPushNotification;
window.triggerDemoNotification = triggerDemoNotification;

if (typeof window !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        initNotifications();
    });
}
