import { getEcho } from './echo';
import './client-features';
import './shop-realtime';
import './pwa';
import './notifications';

// Lắng nghe sự kiện Realtime khi Echo đã sẵn sàng trong background (không chặn Main Thread ban đầu)
const setupChannels = () => {
    const echo = getEcho();
    if (echo) {
        try {
            echo.channel('dishes-channel')
                .listen('DishUpdated', (e) => {
                    window.dispatchEvent(new CustomEvent('dish:live_update', { detail: e }));
                });
        } catch (err) {
            console.warn('Echo dishes channel error:', err);
        }

        try {
            echo.channel('chat-room')
                .listen('MessageSent', (e) => {
                    console.log('Tin nhắn nhận được:', e.message);
                });
        } catch (err) {
            // chat-room optional
        }
    }
};

if (typeof window !== 'undefined') {
    if ('requestIdleCallback' in window) {
        requestIdleCallback(setupChannels, { timeout: 3500 });
    } else {
        setTimeout(setupChannels, 1500);
    }
}
