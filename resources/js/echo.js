import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

let echoInstance = null;

export function getEcho() {
    if (!echoInstance) {
        try {
            echoInstance = new Echo({
                broadcaster: 'reverb',
                key: import.meta.env.VITE_REVERB_APP_KEY,
                wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
                wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
                wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
                forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
                enabledTransports: ['ws', 'wss'],
            });
            window.Echo = echoInstance;
        } catch (e) {
            console.warn('[Echo] Reverb connection deferred or unavailable:', e);
        }
    }
    return echoInstance;
}

// Khởi chạy ngầm khi CPU rảnh rỗi (requestIdleCallback) để giải phóng hoàn toàn Main Thread khi tải trang ban đầu
if (typeof window !== 'undefined') {
    const idleInit = () => {
        getEcho();
    };
    if ('requestIdleCallback' in window) {
        requestIdleCallback(idleInit, { timeout: 3000 });
    } else {
        setTimeout(idleInit, 1500);
    }
}

export default getEcho;
