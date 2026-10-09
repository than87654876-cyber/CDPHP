@php
    $currentUserId = Auth::id();
    $initialUnreadCount = 0;
    $initialNotifications = collect([]);
    try {
        $initialUnreadCount = \App\Services\NotificationService::countUnread($currentUserId);
        $initialNotifications = \App\Services\NotificationService::getForUser($currentUserId, 15);
    } catch (\Throwable $e) {
        $initialUnreadCount = 0;
    }
@endphp

<!-- FOODDAILY Notification Bell & Dropdown Component -->
<div class="relative" id="global-notification-wrapper">
    <!-- Nút Quả Chuông -->
    <button type="button" 
            id="global-notification-bell-btn" 
            onclick="toggleNotificationDropdown(event)" 
            class="relative w-9 h-9 text-slate-700 hover:text-[#ee4d2d] bg-slate-100 hover:bg-orange-50 transition-all rounded-full border border-slate-200/80 cursor-pointer flex items-center justify-center shadow-2xs hover:scale-105 active:scale-95" 
            title="Xem thông báo hệ thống">
        <i class="fas fa-bell text-base transition-transform" id="global-notification-bell-icon"></i>
        
        <!-- Badge số lượng thông báo chưa đọc -->
        <span id="global-notification-badge" 
              class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-[#ee4d2d] text-white text-[10px] font-black min-w-[19px] h-[19px] px-1 rounded-full items-center justify-center border-2 border-white shadow-xs {{ $initialUnreadCount > 0 ? 'flex animate-pulse' : 'hidden' }}">
            {{ $initialUnreadCount > 99 ? '99+' : $initialUnreadCount }}
        </span>
    </button>

    <!-- Dropdown Menu Danh Sách Thông Báo -->
    <div id="global-notification-dropdown" 
         class="hidden absolute right-0 mt-3 w-80 sm:w-96 bg-white/98 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200 py-0 z-50 overflow-hidden animate-in fade-in slide-in-from-top-2 duration-200">
        
        <!-- Dropdown Header -->
        <div class="px-4 py-3 bg-gradient-to-r from-slate-50 to-orange-50/60 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-orange-100 text-[#ee4d2d] flex items-center justify-center text-xs font-bold shadow-2xs">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 leading-tight">Thông báo</h3>
                    <p class="text-[10px] font-semibold text-slate-400" id="global-notification-subtitle">
                        {{ $initialUnreadCount > 0 ? $initialUnreadCount . ' thông báo mới chưa đọc' : 'Tất cả đã đọc' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" 
                        onclick="markAllNotificationsAsRead(event)" 
                        id="global-notification-mark-all-btn"
                        class="text-[11px] font-bold text-[#ee4d2d] hover:text-red-700 hover:underline px-2 py-1 rounded-lg hover:bg-orange-100/50 transition-colors cursor-pointer flex items-center gap-1"
                        title="Đánh dấu tất cả thông báo là đã đọc">
                    <i class="fas fa-check-double text-[10px]"></i>
                    <span>Đã đọc tất cả</span>
                </button>
            </div>
        </div>

        <!-- Dropdown Body: Danh Sách Thông Báo (Hiển thị ngay lập tức bằng Server-side Blade, không bị quay vòng tròn) -->
        <div id="global-notification-list" class="max-h-[380px] overflow-y-auto divide-y divide-slate-100 overscroll-contain">
            @if($initialNotifications->isEmpty())
                <div class="p-8 text-center text-slate-400">
                    <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-slate-100 flex items-center justify-center text-xl text-slate-400">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-600">Bạn chưa có thông báo nào</p>
                    <p class="text-[11px] text-slate-400 mt-1">Các cập nhật đơn hàng & ưu đãi sẽ hiển thị tại đây.</p>
                </div>
            @else
                @foreach($initialNotifications as $noti)
                    @php
                        $isUnread = !$noti->isRead();
                        $targetUrl = $noti->url ?: route('trangchu');
                    @endphp
                    <div class="p-3 sm:p-3.5 flex items-start gap-3 transition-colors cursor-pointer {{ $isUnread ? 'bg-orange-50/50 hover:bg-orange-50/80' : 'bg-white hover:bg-slate-50' }} group" 
                         onclick="window.handleNotificationItemClick({{ $noti->id }}, '{{ $targetUrl }}')">
                        @if($noti->type === 'order')
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-motorcycle"></i></div>
                        @elseif($noti->type === 'promo')
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-bolt"></i></div>
                        @elseif($noti->type === 'lunch' || $noti->type === 'dinner' || $noti->type === 'meal')
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-utensils"></i></div>
                        @else
                            <div class="w-8 h-8 rounded-xl bg-orange-100 text-[#ee4d2d] flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-bell"></i></div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <h4 class="text-xs {{ $isUnread ? 'font-extrabold text-slate-900' : 'font-semibold text-slate-700' }} truncate group-hover:text-[#ee4d2d] transition-colors">{{ $noti->title }}</h4>
                                @if($isUnread)
                                    <span class="w-2 h-2 rounded-full bg-[#ee4d2d] shrink-0" title="Chưa đọc"></span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5 leading-relaxed">{{ $noti->message }}</p>
                            <span class="text-[10px] text-slate-400 font-medium block mt-1">{{ $noti->created_at ? $noti->created_at->diffForHumans() : 'Vừa xong' }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Dropdown Footer -->
        <div class="px-4 py-2.5 bg-slate-50/90 border-t border-slate-100 flex items-center justify-between text-[11px]">
            <!-- Trạng thái Web Push -->
            <div class="flex items-center gap-1.5" id="web-push-status-container">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-slate-500 font-semibold text-[10px]" id="web-push-status-text">Đã bật Web Push</span>
            </div>

            <!-- Nút Bắn thử Noti nhanh -->
            <button type="button" 
                    onclick="triggerTestNotification(event)" 
                    class="text-[10px] font-extrabold text-white bg-gradient-to-r from-orange-500 to-[#ee4d2d] hover:opacity-90 px-2.5 py-1 rounded-lg shadow-xs transition-all flex items-center gap-1 cursor-pointer">
                <i class="fas fa-paper-plane text-[9px]"></i>
                <span>Bắn thử Noti</span>
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    // Đảm bảo đường dẫn API luôn đúng kể cả khi chạy localhost/CDPHP/public/ hay cdphp.test
    const NOTI_ROUTES = {
        list: "{{ route('api.notifications.list') }}",
        markRead: "{{ url('/api/notifications/mark-read') }}",
        markAllRead: "{{ route('api.notifications.mark_all_read') }}",
        triggerDemo: "{{ route('api.notifications.trigger_demo') }}",
        subscribePush: "{{ route('api.notifications.subscribe_push') }}"
    };

    function getCsrf() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    window.toggleNotificationDropdown = window.toggleNotificationDropdown || function(e) {
        if (e) e.stopPropagation();
        const dd = document.getElementById('global-notification-dropdown');
        if (!dd) return;
        const isHidden = dd.classList.contains('hidden');
        if (isHidden) {
            dd.classList.remove('hidden');
            window.loadNotificationsAjax && window.loadNotificationsAjax();
        } else {
            dd.classList.add('hidden');
        }
    };

    window.loadNotificationsAjax = function() {
        const listContainer = document.getElementById('global-notification-list');
        fetch(NOTI_ROUTES.list, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                const badge = document.getElementById('global-notification-badge');
                const sub = document.getElementById('global-notification-subtitle');
                if (badge) {
                    if (data.unread_count > 0) {
                        badge.innerText = data.unread_count > 99 ? '99+' : data.unread_count;
                        badge.classList.remove('hidden');
                        badge.classList.add('flex');
                    } else {
                        badge.classList.add('hidden');
                        badge.classList.remove('flex');
                    }
                }
                if (sub) {
                    sub.innerText = data.unread_count > 0 ? (data.unread_count + ' thông báo mới chưa đọc') : 'Tất cả đã đọc';
                }
                if (!listContainer) return;
                if (!data.notifications || data.notifications.length === 0) {
                    listContainer.innerHTML = '<div class="p-8 text-center text-slate-400"><div class="w-12 h-12 mx-auto mb-2 rounded-full bg-slate-100 flex items-center justify-center text-xl text-slate-400"><i class="fas fa-bell-slash"></i></div><p class="text-xs font-bold text-slate-600">Bạn chưa có thông báo nào</p></div>';
                    return;
                }
                let html = '';
                data.notifications.forEach(item => {
                    let icon = '<div class="w-8 h-8 rounded-xl bg-orange-100 text-[#ee4d2d] flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-bell"></i></div>';
                    if (item.type === 'order') icon = '<div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-motorcycle"></i></div>';
                    if (item.type === 'promo') icon = '<div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-bolt"></i></div>';
                    if (item.type === 'lunch' || item.type === 'dinner' || item.type === 'meal') icon = '<div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-xs font-bold shadow-2xs"><i class="fas fa-utensils"></i></div>';
                    const unread = !item.is_read;
                    html += `
                        <div class="p-3 sm:p-3.5 flex items-start gap-3 transition-colors cursor-pointer ${unread ? 'bg-orange-50/50 hover:bg-orange-50/80' : 'bg-white hover:bg-slate-50'} group" 
                             onclick="window.handleNotificationItemClick(${item.id}, '${item.url || '/trangchu'}')">
                            ${icon}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <h4 class="text-xs ${unread ? 'font-extrabold text-slate-900' : 'font-semibold text-slate-700'} truncate group-hover:text-[#ee4d2d] transition-colors">${item.title}</h4>
                                    ${unread ? '<span class="w-2 h-2 rounded-full bg-[#ee4d2d] shrink-0"></span>' : ''}
                                </div>
                                <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5 leading-relaxed">${item.message}</p>
                                <span class="text-[10px] text-slate-400 font-medium block mt-1">${item.created_at || 'Vừa xong'}</span>
                            </div>
                        </div>
                    `;
                });
                listContainer.innerHTML = html;
            })
            .catch(() => {});
    };

    window.handleNotificationItemClick = function(id, url) {
        fetch(NOTI_ROUTES.markRead + '/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() }
        }).catch(() => {});
        if (url && url !== '#') {
            setTimeout(() => { window.location.href = url; }, 100);
        }
    };

    window.markAllNotificationsAsRead = window.markAllNotificationsAsRead || function(e) {
        if (e) e.stopPropagation();
        fetch(NOTI_ROUTES.markAllRead, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() }
        }).then(r => r.json()).then(() => {
            const badge = document.getElementById('global-notification-badge');
            if (badge) { badge.classList.add('hidden'); badge.classList.remove('flex'); }
            window.loadNotificationsAjax && window.loadNotificationsAjax();
        }).catch(() => {});
    };

    window.triggerTestNotification = window.triggerTestNotification || function(e) {
        if (e) e.stopPropagation();
        fetch(NOTI_ROUTES.triggerDemo, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrf() },
            body: JSON.stringify({ type: 'lunch' })
        }).then(r => r.json()).then(data => {
            if (data.success) {
                // Hiển thị Native notification nếu có quyền
                if ('Notification' in window && Notification.permission === 'granted') {
                    try {
                        new Notification(data.data.title, { body: data.data.body, icon: '/icons/icon-192x192.png' });
                    } catch(err) {
                        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                            navigator.serviceWorker.ready.then(reg => {
                                reg.showNotification(data.data.title, { body: data.data.body, icon: '/icons/icon-192x192.png' });
                            });
                        }
                    }
                }
                window.loadNotificationsAjax && window.loadNotificationsAjax();
            }
        }).catch(err => console.warn('Lỗi trigger demo:', err));
    };

    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('global-notification-wrapper');
        const dd = document.getElementById('global-notification-dropdown');
        if (dd && !dd.classList.contains('hidden')) {
            if (wrap && !wrap.contains(e.target)) {
                dd.classList.add('hidden');
            }
        }
    });

    // Cập nhật trạng thái Web Push
    setTimeout(function() {
        const statusText = document.getElementById('web-push-status-text');
        const statusBox = document.getElementById('web-push-status-container');
        if (!statusText) return;
        if (!('Notification' in window)) {
            statusText.innerText = 'Trình duyệt ko hỗ trợ Push';
            return;
        }
        if (Notification.permission === 'granted') {
            statusText.innerText = 'Đã bật Web Push';
            statusText.className = 'text-emerald-600 font-bold text-[10px]';
        } else if (Notification.permission === 'denied') {
            statusText.innerText = 'Web Push: Bị chặn';
            statusText.className = 'text-rose-500 font-bold text-[10px]';
        } else {
            if (statusBox) {
                statusBox.innerHTML = '<button type="button" onclick="Notification.requestPermission().then(() => location.reload())" class="text-[10px] text-[#ee4d2d] font-bold underline cursor-pointer">Bật Web Push</button>';
            }
        }
    }, 300);
})();
</script>
