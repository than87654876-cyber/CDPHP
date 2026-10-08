import './echo';
import './client-features';
import './shop-realtime';

// Lắng nghe sự kiện Realtime thay đổi món ăn từ admin (Reverb / Pusher)
if (window.Echo) {
    try {
        window.Echo.channel('dishes-channel')
            .listen('DishUpdated', (e) => {
                window.dispatchEvent(new CustomEvent('dish:live_update', { detail: e }));
            });
    } catch (err) {
        console.warn('Echo dishes channel error:', err);
    }
}

if (window.Echo) {
    try {
        window.Echo.channel('chat-room')
            .listen('MessageSent', (e) => {
                console.log('Tin nhắn nhận được:', e.message);
            });
    } catch (err) {
        // chat-room optional
    }
}
