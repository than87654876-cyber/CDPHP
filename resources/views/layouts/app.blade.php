<!DOCTYPE html>
<html lang="vi" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FOODDAILY - Đặt Đồ Ăn, Giao Hàng Siêu Tốc')</title>

    <link rel="icon" href="{{ asset('logo.jpg') }}">
    
    <!-- Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Tailwind CDN & Custom Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        shopee: {
                            DEFAULT: '#ee4d2d',
                            hover: '#d73211',
                            orange: '#ff4726',
                            blue: '#0099ff'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body, .site-bg-overlay {
            background-image: linear-gradient(rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.25)), url("{{ asset('uploads/cf1a02d49dc2b801e809fcb9adefd77e.jpg') }}") !important;
            background-attachment: fixed !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
            min-height: 100vh !important;
        }
    </style>
    @yield('styles')
</head>
<body class="font-sans antialiased site-bg-overlay text-slate-800 flex flex-col min-h-screen selection:bg-rose-500 selection:text-white">

    <!-- ShopeeFood Style Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                @php
                    $globalSettings = \App\Models\Setting::pluck('value', 'key')->all();
                @endphp
                <!-- Left: Brand Logo & Location -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('trangchu') }}" class="flex items-center group">
                        <div class="flex flex-col">
                            <span class="font-black text-2xl tracking-tighter text-[#ee4d2d] group-hover:opacity-90 transition-opacity">FOODDAILY</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest -mt-1.5">Food Delivery</span>
                        </div>
                    </a>

                    <!-- City Selector Pill -->
                    <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-200/80 transition-all">
                        <span>TP. HCM</span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                    </div>
                </div>

                <!-- Center: ShopeeFood Navigation Tabs -->
                <nav class="hidden lg:flex items-center gap-6 text-xs font-bold text-slate-600 h-full">
                    <a href="{{ route('trangchu') }}" class="h-full flex items-center border-b-2 border-[#ee4d2d] text-[#ee4d2d] px-1 font-extrabold">
                        Đồ ăn
                    </a>
                    <a href="{{ route('tracuu') }}" class="h-full flex items-center hover:text-[#ee4d2d] transition-colors px-1">
                        Tra cứu đơn
                    </a>
                    <a href="{{ route('yeucauhoan') }}" class="h-full flex items-center hover:text-[#ee4d2d] transition-colors px-1">
                        Đổi/Hoàn tiền
                    </a>
                </nav>

                <!-- Right: Search, Auth & Language -->
                <div class="flex items-center gap-4">
                    <!-- Search Icon Trigger -->
                    <button type="button" onclick="toggleHeaderSearch()" class="p-2 text-slate-600 hover:text-[#ee4d2d] text-base transition-colors" title="Tìm kiếm món ăn">
                        <i class="fas fa-magnifying-glass"></i>
                    </button>

                    <!-- Cart Icon (Dành cho cả Khách vãng lai & Thành viên) -->
                    <a href="{{ route('muahang') }}" class="relative p-2 text-slate-600 hover:text-[#ee4d2d] transition-colors" title="Giỏ hàng & Thanh toán">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span id="global-header-cart-badge" class="absolute -top-1.5 -right-2 bg-[#ee4d2d] text-white text-xs font-black min-w-[20px] h-5 px-1.5 rounded-full flex items-center justify-center border-2 border-white shadow-sm hidden">
                            0
                        </span>
                    </a>

                    @auth
                        <!-- User Profile Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 p-1 pl-2 pr-2.5 bg-slate-100 hover:bg-slate-200/80 rounded-full border border-slate-200 transition-all">
                                <div class="w-6 h-6 rounded-full bg-[#ee4d2d] text-white flex items-center justify-center font-bold text-[11px]">
                                    {{ strtoupper(substr(Auth::user()->fullname ?? Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-xs font-bold text-slate-800 hidden sm:inline">{{ Auth::user()->fullname ?? Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-slate-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->fullname ?? Auth::user()->name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                @if(in_array(Auth::user()->role, ['admin', 'staff']))
                                    <a href="{{ route('quanly') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#ee4d2d] hover:bg-rose-50">
                                        <i class="fas fa-chart-line"></i>Admin Portal
                                    </a>
                                @endif
                                <a href="{{ route('giohang') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    <i class="fas fa-receipt text-slate-400"></i>Đơn hàng của tôi
                                </a>
                                <hr class="my-1 border-slate-100">
                                <a href="{{ route('dangxuat') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                    <i class="fas fa-right-from-bracket"></i>Đăng xuất
                                </a>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('dangnhap') }}" class="px-4 py-1.5 rounded-lg border border-[#ee4d2d] text-[#ee4d2d] hover:bg-[#ee4d2d] hover:text-white text-xs font-bold transition-all">
                            Đăng nhập
                        </a>
                    @endauth

                    <!-- Language Flag Badge -->
                    <div class="flex items-center gap-1 text-xs font-bold text-slate-600 pl-2 border-l border-slate-200">
                        <span class="text-sm">🇻🇳</span>
                        <i class="fas fa-chevron-down text-[9px] text-slate-400"></i>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <!-- Header Live Search Modal Overlay -->
    <div id="header-search-modal" class="hidden fixed inset-x-0 top-16 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 p-4 shadow-xl">
        <div class="max-w-3xl mx-auto relative">
            <form action="{{ route('trangchu') }}" method="GET" class="flex items-center gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 text-sm"></i>
                    <input type="text" name="search" id="header-search-input" oninput="handleLiveSearch(this.value)" autocomplete="off" placeholder="Nhập tên món ăn cần tìm (VD: Bánh mì, Cơm tấm, Trà sữa)..." class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-xs">
                </div>
                <button type="submit" class="px-5 py-3 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md transition-all">TÌM KIẾM</button>
                <button type="button" onclick="toggleHeaderSearch()" class="px-4 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition-colors">Đóng</button>
            </form>

            <!-- Live Search Results Dropdown -->
            <div id="live-search-results" class="hidden mt-3 bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden divide-y divide-slate-100 max-h-96 overflow-y-auto">
                <!-- Populated via AJAX -->
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Floating AI Chatbot Widget -->
    <button type="button" onclick="toggleChatbot()" class="fixed bottom-6 right-6 z-50 px-4 py-3 rounded-full bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-xl shadow-rose-500/30 flex items-center gap-2.5 hover:scale-105 transition-all cursor-pointer">
        <span class="relative flex h-2.5 w-2.5">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
        </span>
        <i class="fas fa-robot text-base"></i> <span>Trợ lý AI FOODDAILY</span>
    </button>

    <!-- Chatbot Modal Window -->
    <div id="chatbot-modal" class="hidden fixed bottom-20 right-6 z-50 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden transition-all duration-300">
        <!-- Chat Header -->
        <div class="bg-[#ee4d2d] text-white p-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center font-bold text-lg">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm leading-tight">FOODDAILY Assistant</h3>
                    <p class="text-[10px] text-white/80 font-medium">Sẵn sàng gợi ý & tìm món cho bạn 24/7</p>
                </div>
            </div>
            <button type="button" onclick="toggleChatbot()" class="text-white/80 hover:text-white text-lg"><i class="fas fa-xmark"></i></button>
        </div>

        <!-- Chat Messages Area -->
        <div id="chatbot-messages" class="p-4 space-y-3 h-80 overflow-y-auto bg-slate-50/70 text-xs font-medium">
            <div class="flex items-start gap-2">
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-[#ee4d2d] flex items-center justify-center font-bold text-xs shrink-0">AI</div>
                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs text-slate-800 max-w-[85%] leading-relaxed">
                    Xin chào! 👋 Mình là Trợ lý AI FOODDAILY. Bạn đang thèm món gì hoặc cần tìm món ăn gì hôm nay?
                </div>
            </div>
        </div>

        <!-- Chat Input Form -->
        <form id="chatbot-form" onsubmit="sendChatbotMessage(event)" class="p-3 bg-white border-t border-slate-100 flex items-center gap-2">
            <input type="text" id="chatbot-input" placeholder="Nhập tên món ăn hoặc hỏi AI..." class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]">
            <button type="submit" class="w-9 h-9 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

    <!-- ShopeeFood Footer -->
    <footer class="bg-white/95 backdrop-blur-md border-t border-slate-200 pt-12 pb-8 mt-0 text-xs text-slate-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 pb-8 border-b border-slate-100">
                <div class="space-y-3">
                    <a href="{{ route('trangchu') }}" class="flex items-center">
                        <span class="font-black text-xl text-[#ee4d2d]">FOODDAILY</span>
                    </a>
                    <p class="text-slate-500 text-[11px] leading-relaxed">
                        FOODDAILY - Hệ thống giao đồ ăn, thực phẩm sạch tận nơi nhanh chóng từ 20 phút.
                    </p>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-[11px] mb-3">Về FOODDAILY</h4>
                    <ul class="space-y-2 text-slate-500">
                        <li><a href="{{ route('trangchu') }}" class="hover:text-[#ee4d2d]">Giới thiệu</a></li>
                        <li><a href="{{ route('tracuu') }}" class="hover:text-[#ee4d2d]">Tra cứu đơn hàng</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-[11px] mb-3">Trung tâm hỗ trợ</h4>
                    <ul class="space-y-2 text-slate-500">
                        <li><a href="{{ route('yeucauhoan') }}" class="hover:text-[#ee4d2d]">Chính sách đổi trả / hoàn tiền</a></li>
                        <li><a href="#" class="hover:text-[#ee4d2d]">Quy chế hoạt động</a></li>
                        <li><a href="#" class="hover:text-[#ee4d2d]">Giải quyết khiếu nại</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-[11px] mb-3">Tải ứng dụng FOODDAILY</h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 p-2 bg-slate-900 text-white rounded-lg w-36 cursor-pointer hover:bg-slate-800">
                            <i class="fab fa-apple text-xl"></i>
                            <div class="leading-none"><span class="text-[9px] block">App Store</span><span class="font-bold text-xs">iOS App</span></div>
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-slate-900 text-white rounded-lg w-36 cursor-pointer hover:bg-slate-800">
                            <i class="fab fa-google-play text-lg"></i>
                            <div class="leading-none"><span class="text-[9px] block">Google Play</span><span class="font-bold text-xs">Android</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 text-center text-slate-400 text-[11px]">
                © 2026 FOODDAILY Company. Tất cả quyền được bảo lưu.
            </div>
        </div>
    </footer>

    <script>
        // Header Cart Badge Update
        function updateGlobalHeaderCartBadge() {
            try {
                const cart = JSON.parse(localStorage.getItem('fooddelicious_cart') || '[]');
                const badge = document.getElementById('global-header-cart-badge');
                if (badge) {
                    if (cart.length > 0) {
                        badge.innerText = cart.length;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            } catch(e){}
        }
        document.addEventListener("DOMContentLoaded", updateGlobalHeaderCartBadge);
        window.addEventListener("storage", updateGlobalHeaderCartBadge);

        // Toggle Header Search Modal Bar
        function toggleHeaderSearch() {
            const modal = document.getElementById('header-search-modal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                document.getElementById('header-search-input').focus();
            }
        }

        // Live AJAX Dish Search by Name
        let liveSearchTimer = null;
        function handleLiveSearch(query) {
            clearTimeout(liveSearchTimer);
            const resultsBox = document.getElementById('live-search-results');
            if (!query.trim()) {
                resultsBox.classList.add('hidden');
                resultsBox.innerHTML = '';
                return;
            }

            liveSearchTimer = setTimeout(() => {
                fetch(`{{ route('api.dishes.search') }}?q=${encodeURIComponent(query)}`)
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
                                    <form action="{{ route('giohang.add') }}" method="POST">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                    .catch(err => console.error(err));
            }, 250);
        }

        // Toggle Chatbot Window Modal
        function toggleChatbot() {
            const modal = document.getElementById('chatbot-modal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                document.getElementById('chatbot-input').focus();
            }
        }

        // Send Chatbot Message API
        function sendChatbotMessage(e) {
            e.preventDefault();
            const input = document.getElementById('chatbot-input');
            const msg = input.value.trim();
            if (!msg) return;

            const msgContainer = document.getElementById('chatbot-messages');

            // Render User Message
            msgContainer.innerHTML += `
                <div class="flex items-start justify-end gap-2">
                    <div class="bg-[#ee4d2d] text-white p-3 rounded-2xl text-xs max-w-[85%] font-semibold shadow-xs">
                        ${msg}
                    </div>
                </div>
            `;
            input.value = '';
            msgContainer.scrollTop = msgContainer.scrollHeight;

            // Render Loading Indicator
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

            // Fetch Chatbot API
            fetch(`{{ route('api.chatbot.ask') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                            <form action="{{ route('giohang.add') }}" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="dish_id" value="${d.id}">
                                <button type="submit" class="px-2.5 py-1 bg-[#ee4d2d] text-white rounded-md text-[10px] font-bold">+ Đặt</button>
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
    </script>
    @yield('scripts')
</body>
</html>
