/**
 * FOODDAILY - Progressive Web App (PWA) Manager
 * Xử lý đăng ký Service Worker, Bắt sự kiện Cài đặt (Install Prompt),
 * Hướng dẫn cho iOS Safari và thông báo trạng thái PWA.
 */

let deferredInstallPrompt = null;

// Kiểm tra xem ứng dụng đã chạy ở chế độ PWA (Standalone) chưa
export function isRunningStandalone() {
    return (
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true ||
        document.referrer.includes('android-app://')
    );
}

// Kiểm tra thiết bị iOS
export function isIOS() {
    return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
}

// Đăng ký Service Worker
export function registerServiceWorker() {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then((reg) => {
                    console.log('[PWA] Service Worker đăng ký thành công:', reg.scope);

                    // Kiểm tra cập nhật Service Worker
                    reg.onupdatefound = () => {
                        const installingWorker = reg.installing;
                        installingWorker.onstatechange = () => {
                            if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                console.log('[PWA] Đã có bản cập nhật mới!');
                            }
                        };
                    };
                })
                .catch((err) => {
                    console.warn('[PWA] Đăng ký Service Worker thất bại:', err);
                });
        });
    }
}

// Khởi tạo các sự kiện PWA
export function initPWA() {
    registerServiceWorker();

    // Nếu đã ở chế độ Standalone (App), ẩn các nút cài đặt
    if (isRunningStandalone()) {
        const installBanners = document.querySelectorAll('.pwa-install-banner, .pwa-install-btn');
        installBanners.forEach(el => el.classList.add('hidden'));
        return;
    }

    // Lắng nghe sự kiện beforeinstallprompt (Chrome / Android / Desktop)
    window.addEventListener('beforeinstallprompt', (e) => {
        // Ngăn banner mặc định của trình duyệt để tự điều khiển UI đẹp hơn
        e.preventDefault();
        deferredInstallPrompt = e;

        // Hiển thị các nút cài đặt PWA và banner thông minh
        const installBtns = document.querySelectorAll('.pwa-install-btn');
        installBtns.forEach(btn => btn.classList.remove('hidden'));

        const banner = document.getElementById('pwa-mobile-install-banner');
        const dismissed = sessionStorage.getItem('pwa_banner_dismissed');
        if (banner && !dismissed) {
            banner.classList.remove('hidden');
        }
    });

    // Lắng nghe khi người dùng đã cài đặt thành công
    window.addEventListener('appinstalled', () => {
        console.log('[PWA] Ứng dụng đã được cài đặt thành công vào thiết bị!');
        deferredInstallPrompt = null;
        const banner = document.getElementById('pwa-mobile-install-banner');
        if (banner) banner.classList.add('hidden');
        const installBtns = document.querySelectorAll('.pwa-install-btn');
        installBtns.forEach(btn => btn.classList.add('hidden'));
    });
}

// Hàm kích hoạt cài đặt (gắn vào window để gọi từ onclick)
export function triggerPWAInstall() {
    if (deferredInstallPrompt) {
        deferredInstallPrompt.prompt();
        deferredInstallPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('[PWA] Người dùng đồng ý cài đặt');
            } else {
                console.log('[PWA] Người dùng từ chối cài đặt');
            }
            deferredInstallPrompt = null;
        });
    } else if (isIOS()) {
        openIOSInstallGuideModal();
    } else {
        openUniversalPWAModal();
    }
}

// Đóng banner cài đặt trên mobile
export function dismissPWABanner() {
    const banner = document.getElementById('pwa-mobile-install-banner');
    if (banner) banner.classList.add('hidden');
    sessionStorage.setItem('pwa_banner_dismissed', 'true');
}

// Modal hướng dẫn Universal (Desktop & Android & QR)
export function openUniversalPWAModal() {
    const modal = document.getElementById('pwa-universal-modal');
    if (modal) modal.classList.remove('hidden');
}

export function closeUniversalPWAModal() {
    const modal = document.getElementById('pwa-universal-modal');
    if (modal) modal.classList.add('hidden');
}

// Mở modal hướng dẫn cho thiết bị iOS
export function openIOSInstallGuideModal() {
    const modal = document.getElementById('pwa-ios-modal');
    if (modal) modal.classList.remove('hidden');
}

export function closeIOSInstallGuideModal() {
    const modal = document.getElementById('pwa-ios-modal');
    if (modal) modal.classList.add('hidden');
}

// Gắn các hàm ra window
window.triggerPWAInstall = triggerPWAInstall;
window.dismissPWABanner = dismissPWABanner;
window.openUniversalPWAModal = openUniversalPWAModal;
window.closeUniversalPWAModal = closeUniversalPWAModal;
window.openIOSInstallGuideModal = openIOSInstallGuideModal;
window.closeIOSInstallGuideModal = closeIOSInstallGuideModal;

// Tự động khởi chạy
if (typeof window !== 'undefined') {
    initPWA();
}
