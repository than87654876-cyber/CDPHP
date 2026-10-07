@extends('layouts.admin')

@section('title', 'Màn Hình Bếp Nấu - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-black">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Hệ thống Vận hành Tốt
                </span>
                <span>•</span>
                <span>KDS - Kitchen Display System Realtime</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fas fa-fire-burner text-amber-500 text-3xl"></i> Bảng Chuẩn Bị Món Ăn Bếp Nấu
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Quản lý chế biến tuần tự theo từng Mã đơn hàng (FIFO): Nấu hoàn tất đơn hiện tại trước khi chuyển sang đơn tiếp theo
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <div class="flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-700 shadow-2xs">
                <i class="far fa-calendar-alt text-[#ee4d2d]"></i> Ngày: {{ \Carbon\Carbon::parse($todayStr)->format('d/m/Y') }}
            </div>
            <button onclick="window.location.reload()" class="px-3.5 py-2 rounded-2xl bg-white hover:bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700 transition-colors shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-arrows-rotate text-slate-400"></i> Làm mới
            </button>
        </div>
    </div>

    <!-- Alert thông báo -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer">&times;</button>
        </div>
    @endif

    <!-- Workflow Banner: Hướng dẫn quy tắc chế biến FIFO theo mã đơn -->
    <div class="bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-rose-500/10 border border-amber-200/90 rounded-3xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-lg font-black shrink-0 shadow-sm shadow-amber-500/30">
                <i class="fas fa-bell-concierge"></i>
            </div>
            <div class="space-y-0.5">
                <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                    <span>Quy Tắc Chế Biến Bếp Nấu: Theo Từng Mã Đơn (FIFO)</span>
                    <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-black uppercase">Đơn đặt trước làm trước</span>
                </h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Khi khách đặt nhiều món trong cùng 1 mã đơn, nhà bếp tập trung <strong>chế biến toàn bộ món ăn của mã đơn đó trước (Ưu tiên #1)</strong>, kiểm tra đủ món rồi mới chuyển sang làm các mã đơn phía sau.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0 text-xs font-black">
            <div class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 shadow-2xs">
                Chờ làm: <span class="text-blue-600">{{ $activeOrders->whereIn('order_status', ['pending', 'confirmed'])->count() }} đơn</span>
            </div>
            <div class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 shadow-2xs">
                Đang nấu: <span class="text-purple-600">{{ $activeOrders->where('order_status', 'preparing')->count() }} đơn</span>
            </div>
        </div>
    </div>

    <!-- PHẦN 1: HÀNG ĐỢI CHẾ BIẾN THEO TỪNG MÃ ĐƠN HÀNG (FIFO QUEUE) -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden" id="kitchen-queue-container">
        
        <!-- Header Phiếu Gọi Món Theo Mã Vận Đơn -->
        <div class="p-5 bg-gradient-to-r from-slate-900 via-slate-850 to-slate-800 text-white flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-black text-lg border border-amber-500/30">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h2 class="text-base font-black uppercase tracking-wider flex items-center gap-2 flex-wrap">
                        <span>Phiếu Gọi Món Theo Mã Vận Đơn</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-slate-900 text-xs font-black shadow-xs">
                            <span id="active-orders-count-badge">{{ $activeOrders->count() }}</span> Mã Đơn Cần Nấu
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Xếp tuần tự theo thời gian đặt (FIFO) — Hoàn tất mã đơn hiện tại trước khi chuyển đơn tiếp theo</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 text-xs font-bold text-slate-300">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-500/20 text-blue-300 border border-blue-500/30">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span> Chờ nấu
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-500/20 text-purple-300 border border-purple-500/30">
                    <span class="w-2 h-2 rounded-full bg-purple-400 animate-ping"></span> Đang nấu
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Đã nấu xong
                </span>
            </div>
        </div>

        <!-- KDS Control Toolbar: Tìm kiếm nhanh đơn hàng & Chế độ xem & Tự động đẩy đơn -->
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
            <!-- Ô Tìm Kiếm Đơn Hàng -->
            <div class="flex-1 max-w-lg flex items-center gap-2">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" 
                           id="kitchen-search-input" 
                           placeholder="🔍 Tìm mã đơn (vd: 18), tên khách, SĐT, hoặc món ăn..." 
                           oninput="handleKitchenSearch(this.value)"
                           class="w-full pl-9 pr-9 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#ee4d2d] focus:ring-2 focus:ring-rose-500/20 transition-all shadow-2xs">
                    <button type="button" 
                            id="btn-clear-kitchen-search" 
                            onclick="clearKitchenSearch()" 
                            class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                            title="Xóa tìm kiếm">
                        <i class="fas fa-times-circle text-xs"></i>
                    </button>
                </div>

                <div id="kitchen-search-feedback" class="hidden text-xs font-extrabold text-[#ee4d2d] shrink-0 whitespace-nowrap bg-rose-50 px-2.5 py-1.5 rounded-xl border border-rose-200">
                    <span id="kitchen-search-feedback-text">0 đơn</span>
                </div>
            </div>

            <!-- Điều khiển hiển thị & Tùy chọn chuyển đơn -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Chế độ hiển thị số lượng đơn (Mặc định 3 đơn tập trung) -->
                <div class="bg-white p-1 rounded-2xl border border-slate-200 flex items-center gap-1 shadow-2xs text-xs font-black">
                    <button type="button" 
                            onclick="setKitchenViewLimit(3)" 
                            id="btn-view-limit-3"
                            class="px-3 py-1.5 rounded-xl bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs">
                        🎯 3 đơn tập trung
                    </button>
                    <button type="button" 
                            onclick="setKitchenViewLimit(6)" 
                            id="btn-view-limit-6"
                            class="px-3 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                        6 đơn
                    </button>
                    <button type="button" 
                            onclick="setKitchenViewLimit(999)" 
                            id="btn-view-limit-all"
                            class="px-3 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                        Tất cả
                    </button>
                </div>

                <!-- Tùy chọn tự động đẩy đơn khi tick xong -->
                <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 cursor-pointer select-none shadow-2xs hover:border-slate-300">
                    <input type="checkbox" id="chk-auto-advance" checked class="w-3.5 h-3.5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-bolt text-amber-500"></i>
                        <span>Tự động chuyển đơn mới khi tick đủ món</span>
                    </span>
                </label>

                <!-- Nút xem đơn vừa xong -->
                <button type="button" 
                        onclick="toggleCompletedOrdersModal()" 
                        class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-black transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs">
                    <i class="fas fa-clock-rotate-left text-emerald-600"></i>
                    <span>Vừa nấu xong</span>
                    <span id="badge-completed-count" class="px-1.5 py-0.2 rounded-full bg-emerald-600 text-white text-[10px]">{{ count($initialCompletedOrders ?? []) }}</span>
                </button>
            </div>
        </div>

        <div class="p-6">
            @if($activeOrders->isEmpty())
                <div class="text-center py-16 space-y-3">
                    <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl font-bold mx-auto border border-emerald-100 shadow-sm">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-800">Không có đơn hàng nào đang chờ bếp nấu!</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Tất cả các món ăn đã được nhà bếp hoàn tất. Khi có khách đặt đơn mới, phiếu bếp kèm mã đơn và danh sách món ăn sẽ xuất hiện ngay tại đây.
                    </p>
                </div>
            @else
                <!-- Grid Vé Bếp Theo Mã Đơn (FIFO) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="kitchen-orders-grid">
                    @foreach($activeOrders as $index => $order)
                        @php
                            $isTopPriority = ($index === 0);
                            $isPreparing = ($order->order_status === 'preparing');
                            $totalDishCount = $order->orderItems->count();
                            $totalQuantity = $order->orderItems->sum('quantity');
                            $customerName = $order->user->fullname ?? $order->user->name ?? 'Khách vãng lai';
                            $phone = $order->user->phone ?? '';
                            $address = $order->user->address ?? '';
                            if (empty($phone) && !empty($order->health_notes) && preg_match('/SĐT:\s*([0-9\+\.\s]+)/i', $order->health_notes, $m)) {
                                $phone = trim($m[1]);
                            }
                            if (empty($address) && !empty($order->health_notes) && preg_match('/Giao:\s*([^.\n]+)/i', $order->health_notes, $m)) {
                                $address = trim($m[1]);
                            }
                            $dishNames = $order->orderItems->map(fn($oi) => $oi->dish->dish_name ?? '')->implode(' ');
                            $searchText = strtolower("#fdl-{$order->id} {$order->id} {$customerName} {$phone} {$dishNames} {$order->health_notes}");
                            $isInitiallyVisible = ($index < 3); // Mặc định chỉ hiển thị 3 đơn tập trung
                        @endphp

                        <div class="order-ticket-card rounded-3xl border-2 transition-all duration-300 flex flex-col justify-between overflow-hidden shadow-xs hover:shadow-md
                                    @if($isPreparing) border-purple-500 bg-purple-50/20 ring-4 ring-purple-500/10
                                    @elseif($isTopPriority) border-amber-400 bg-amber-50/20 ring-2 ring-amber-400/20
                                    @else border-slate-200 bg-white hover:border-slate-300 @endif"
                             id="order-ticket-{{ $order->id }}"
                             data-order-id="{{ $order->id }}"
                             data-order-code="{{ $order->id }}"
                             data-customer-name="{{ $customerName }}"
                             data-phone="{{ $phone }}"
                             data-address="{{ $address }}"
                             data-health-notes="{{ e($order->health_notes ?? '') }}"
                             data-amount="{{ number_format($order->final_amount, 0, ',', '.') }}đ"
                             data-dishes-json="{{ e(json_encode($order->orderItems->map(fn($oi) => ['name' => $oi->dish->dish_name ?? 'Món ăn', 'qty' => $oi->quantity]))) }}"
                             data-search-text="{{ $searchText }}"
                             data-total-items="{{ $totalDishCount }}"
                             data-payment-status="{{ $order->payment_status }}"
                             data-order-status="{{ $order->order_status }}"
                             data-queue-index="{{ $index }}"
                             style="{{ $isInitiallyVisible ? 'display: flex;' : 'display: none;' }}">
                            
                            <!-- Ticket Top Banner: Thứ Tự Ưu Tiên & Mã Vận Đơn -->
                            <div class="ticket-header p-4 border-b flex items-center justify-between gap-2 transition-colors
                                        @if($isPreparing) bg-purple-100/70 border-purple-200 text-purple-900
                                        @elseif($isTopPriority) bg-amber-100/70 border-amber-200 text-amber-900
                                        @else bg-slate-50 border-slate-200 text-slate-800 @endif"
                                 id="ticket-header-{{ $order->id }}">
                                
                                <div class="flex items-center gap-2">
                                    <!-- Mã Đơn Hàng Nổi Bật -->
                                    <span class="px-3 py-1 rounded-xl bg-slate-900 text-white font-black text-xs tracking-wider flex items-center gap-1.5 shadow-sm">
                                        <i class="fas fa-barcode text-[11px] text-amber-400"></i>
                                        #FDL-{{ $order->id }}
                                    </span>

                                    <!-- Badge Thứ tự làm món (được JS cập nhật realtime khi trượt đơn) -->
                                    <span class="queue-priority-badge" id="queue-badge-{{ $order->id }}">
                                        @if($isPreparing)
                                            <span class="px-2.5 py-0.5 rounded-full bg-purple-600 text-white font-black text-[10px] tracking-wide uppercase flex items-center gap-1 shadow-2xs">
                                                <i class="fas fa-fire-burner animate-bounce"></i> Đang nấu
                                            </span>
                                        @elseif($isTopPriority)
                                            <span class="px-2.5 py-0.5 rounded-full bg-rose-500 text-white font-black text-[10px] tracking-wide uppercase flex items-center gap-1 shadow-2xs">
                                                <i class="fas fa-bolt"></i> Ưu tiên #1
                                            </span>
                                        @elseif($index === 1)
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-white font-black text-[10px] tracking-wide uppercase flex items-center gap-1">
                                                Ưu tiên #2
                                            </span>
                                        @elseif($index === 2)
                                            <span class="px-2.5 py-0.5 rounded-full bg-blue-500 text-white font-black text-[10px] tracking-wide uppercase flex items-center gap-1">
                                                Ưu tiên #3
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[10px] tracking-wide uppercase">
                                                Hàng đợi #{{ $index + 1 }}
                                            </span>
                                        @endif
                                    </span>
                                </div>

                                <div class="text-right">
                                    <span class="text-[11px] font-bold text-slate-500 block">
                                        <i class="far fa-clock text-slate-400"></i> {{ $order->created_at ? $order->created_at->format('H:i') : '' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-semibold block">
                                        {{ $order->created_at ? $order->created_at->diffForHumans() : '' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Ticket Body: Khách hàng + Ghi chú + Danh Sách Các Món Ăn Thuộc Mã Đơn -->
                            <div class="p-5 space-y-4 flex-1">
                                
                                <!-- Customer Info Bar -->
                                <div class="flex items-center justify-between text-xs text-slate-600 pb-2 border-b border-slate-100">
                                    <span class="font-black text-slate-800 flex items-center gap-1.5">
                                        <i class="far fa-user text-slate-400"></i>
                                        {{ $customerName }}
                                    </span>
                                    @if($phone)
                                        <span class="text-slate-500 font-bold bg-slate-100 px-2 py-0.5 rounded-lg text-[11px]">
                                            <i class="fas fa-phone text-[10px] text-slate-400 mr-0.5"></i> {{ $phone }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Ghi chú khẩu vị / Lưu ý đặc biệt của khách -->
                                @if(!empty($order->health_notes))
                                    <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                                        <div class="font-black text-[11px] uppercase text-amber-700 flex items-center gap-1.5">
                                            <i class="fas fa-triangle-exclamation text-amber-500"></i> Ghi chú của khách:
                                        </div>
                                        <p class="text-xs font-semibold leading-relaxed pl-4 border-l-2 border-amber-300">
                                            {{ $order->health_notes }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Danh Sách Các Món Ăn Thuộc Mã Đơn Này (Checklist Chế Biến) -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-[11px] font-black uppercase text-slate-400 tracking-wider">
                                        <span>Các món cần làm ({{ $totalDishCount }} món):</span>
                                        <span class="text-[#ee4d2d] font-bold" id="dish-progress-{{ $order->id }}">{{ $totalQuantity }} suất</span>
                                    </div>

                                    <div class="divide-y divide-slate-100 bg-slate-50/80 rounded-2xl p-2.5 border border-slate-200/80 space-y-1">
                                        @foreach($order->orderItems as $itemIndex => $oi)
                                            <label class="flex items-center justify-between py-2 px-2 hover:bg-white rounded-xl transition-colors cursor-pointer select-none group dish-checkbox-row">
                                                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                                    <!-- Checkbox để đầu bếp tick khi làm xong từng món -->
                                                    <input type="checkbox" 
                                                           id="chk-order-{{ $order->id }}-item-{{ $oi->id }}"
                                                           onchange="handleDishCheckboxChange(this, {{ $order->id }}, '{{ $order->payment_status }}')"
                                                           class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer dish-item-checkbox">
                                                    
                                                    <div class="min-w-0">
                                                        <span class="dish-item-name font-extrabold text-xs text-slate-900 block truncate group-hover:text-[#ee4d2d] transition-colors">
                                                            {{ $oi->dish->dish_name ?? 'Món ăn' }}
                                                        </span>
                                                        @if(isset($oi->dish->category))
                                                            <span class="text-[10px] text-slate-400 font-semibold block">
                                                                {{ $oi->dish->category->category_name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <span class="px-2.5 py-1 rounded-xl bg-white border border-rose-200 text-[#ee4d2d] font-black text-xs shrink-0 shadow-2xs">
                                                    {{ $oi->quantity }} suất
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Thanh trạng thái tiến độ hoàn thành món trong đơn -->
                                <div id="order-ready-notice-{{ $order->id }}" class="hidden text-[11px] bg-emerald-50 text-emerald-800 p-2.5 rounded-xl border border-emerald-200 flex items-center justify-between font-bold">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fas fa-circle-check text-emerald-600"></i> Đã nấu đủ món! Đang chuyển đơn mới lên...
                                    </span>
                                    <i class="fas fa-spinner fa-spin text-emerald-600"></i>
                                </div>

                                <!-- Quy tắc nhắc nhở trực quan cho vé bếp -->
                                <div class="text-[11px] text-slate-500 bg-slate-100/70 p-2.5 rounded-xl border border-slate-200/60 flex items-center gap-2">
                                    <i class="fas fa-circle-info text-amber-500 shrink-0"></i>
                                    <span>Làm xong toàn bộ món của mã đơn <strong>#FDL-{{ $order->id }}</strong> rồi mới chuyển sang mã đơn tiếp theo.</span>
                                </div>

                            </div>

                            <!-- Ticket Actions: Nút Bắt Đầu Nấu / Nấu Xong Đơn -->
                            <div class="p-4 bg-slate-50/90 border-t border-slate-100 flex items-center justify-between gap-2" id="ticket-actions-{{ $order->id }}">
                                <a href="{{ route('donhang_xem', $order->id) }}" target="_blank"
                                   class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-colors flex items-center gap-1.5 shadow-2xs">
                                    <i class="far fa-eye text-slate-400"></i> Xem đơn
                                </a>

                                <div class="flex items-center gap-2" id="ticket-buttons-{{ $order->id }}">
                                    @if(in_array($order->order_status, ['pending', 'confirmed']))
                                        <button type="button" 
                                                id="btn-start-{{ $order->id }}"
                                                onclick="startCookingOrder({{ $order->id }}, '{{ $order->payment_status }}')"
                                                class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-black shadow-xs transition-all flex items-center gap-1.5 cursor-pointer hover:scale-102">
                                            <i class="fas fa-fire-burner text-amber-300"></i> Bắt đầu nấu đơn này
                                        </button>
                                    @endif

                                    <button type="button" 
                                            id="btn-complete-{{ $order->id }}"
                                            onclick="completeOrderTicket({{ $order->id }}, '{{ $order->payment_status }}')"
                                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-xs transition-all flex items-center gap-1.5 cursor-pointer hover:scale-102 {{ $order->order_status !== 'preparing' ? 'hidden' : '' }}">
                                        <i class="fas fa-check-double text-emerald-200"></i> Nấu xong đơn -> Giao hàng
                                    </button>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Phân Trang Vé Bếp (KDS Pagination) -->
                <div id="kitchen-pagination-bar" class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-slate-500 font-medium">
                        Hiển thị đơn 
                        <span id="kds-page-from" class="font-extrabold text-slate-900">1</span> - 
                        <span id="kds-page-to" class="font-extrabold text-slate-900">3</span> 
                        trong tổng số 
                        <span id="kds-total-orders" class="font-extrabold text-slate-900">{{ $activeOrders->count() }}</span> mã đơn cần nấu
                    </div>
                    <div class="flex items-center gap-1.5" id="kds-pagination-controls">
                        <button type="button" onclick="changeKitchenPage(-1)" id="btn-kds-prev" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors shadow-2xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-left text-[10px]"></i> Trước
                        </button>
                        <div id="kds-page-numbers" class="flex items-center gap-1"></div>
                        <button type="button" onclick="changeKitchenPage(1)" id="btn-kds-next" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors shadow-2xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                            Sau <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                </div>

                <!-- Empty state khi tìm kiếm không ra kết quả -->
                <div id="kitchen-search-empty" class="hidden text-center py-16 space-y-3 bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                    <div class="w-16 h-16 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-700">Không tìm thấy mã đơn nào phù hợp</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto">
                        Hãy kiểm tra lại mã số đơn (vd: 18), tên khách hàng hoặc tên món ăn bạn muốn tìm kiếm.
                    </p>
                    <button type="button" onclick="clearKitchenSearch()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer">
                        Quay lại 3 đơn ưu tiên
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL: DANH SÁCH ĐƠN VỪA NẤU XONG & TRA CỨU XỬ LÝ SỰ CỐ -->
    <div id="completedOrdersModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden transition-opacity">
        <div class="bg-white rounded-3xl max-w-2xl w-full overflow-hidden shadow-2xl border border-slate-200 flex flex-col max-h-[88vh]">
            <!-- Header Modal -->
            <div class="p-5 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white flex items-center justify-between shrink-0 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/30 shadow-inner">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-sm tracking-wide flex items-center gap-2">
                            <span>Các Đơn Vừa Nấu Xong & Tra Cứu Xử Lý</span>
                            <span class="px-2 py-0.2 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-bold border border-emerald-500/30">Hôm nay</span>
                        </h3>
                        <p class="text-[11px] text-slate-300 font-medium">Xem chi tiết món, liên hệ khách hoặc chỉnh sửa/hoàn tác đơn khi khách cần hỗ trợ</p>
                    </div>
                </div>
                <button type="button" onclick="toggleCompletedOrdersModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg cursor-pointer transition-colors">
                    &times;
                </button>
            </div>

            <!-- Thanh tìm kiếm nhanh bên trong Modal -->
            <div class="p-3 bg-slate-100/90 border-b border-slate-200 shrink-0">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           id="modal-search-completed" 
                           oninput="filterCompletedOrdersModal(this.value)" 
                           placeholder="Tìm nhanh theo mã đơn (#862), tên khách, SĐT, hoặc tên món..." 
                           class="w-full pl-9 pr-8 py-2 rounded-xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                    <button type="button" onclick="clearModalSearch()" id="btn-clear-modal-search" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            </div>

            <!-- Danh sách các thẻ đơn vừa hoàn thành -->
            <div class="p-4 overflow-y-auto space-y-3.5 flex-1 bg-slate-50/50" id="completed-orders-list-content">
                <div class="text-center py-12 text-xs text-slate-400" id="empty-completed-msg">
                    <i class="fas fa-inbox text-4xl mb-3 text-slate-300 block"></i>
                    <p class="font-bold text-slate-600">Chưa có đơn nào vừa nấu xong trong phiên này.</p>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="p-4 bg-white border-t border-slate-200 flex items-center justify-between shrink-0">
                <div class="text-[11px] text-slate-500 flex items-center gap-1.5 font-medium">
                    <i class="fas fa-circle-info text-blue-500"></i>
                    <span>Bấm <strong>Xem chi tiết</strong> để mở toàn bộ hóa đơn hoặc <strong>Xử lý / Sửa đơn</strong> để giải quyết khiếu nại.</span>
                </div>
                <button type="button" onclick="toggleCompletedOrdersModal()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors cursor-pointer shadow-xs">
                    Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- PHẦN 2: BẢNG TỔNG HỢP SUẤT ĂN THEO MÓN (DÀNH CHO BẾP TRƯỞNG CHUẨN BỊ NGUYÊN LIỆU) -->
    <div class="space-y-4 pt-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-layer-group text-slate-400"></i> Bảng Thống Kê Tổng Suất Ăn Cần Chuẩn Bị (Mise en Place)
            </h2>
            <span class="text-xs text-slate-400">Phục vụ bếp sơ chế & chuẩn bị nguyên liệu theo số lượng món</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Panel 1: Single Dishes (Món Ăn Cho Đơn Hàng Lẻ) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col justify-between" id="panel-single-dishes">
                <div>
                    <div class="p-4 sm:p-5 bg-rose-50 border-b border-rose-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-utensils text-[#ee4d2d]"></i> Món Ăn Cho Đơn Hàng Lẻ
                            </h3>
                            <span class="px-2 py-0.5 rounded-full bg-rose-100 text-[#ee4d2d] text-[10px] font-black border border-rose-200" id="badge-single-dishes-count">
                                {{ $singleDishes->count() }} món
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Ô Tìm kiếm nhanh món ăn -->
                            <div class="relative w-32 sm:w-40">
                                <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                <input type="text" 
                                       id="input-search-single-dishes" 
                                       oninput="searchSingleDishes(this.value)" 
                                       placeholder="Tìm món..." 
                                       class="w-full pl-7 pr-2.5 py-1 text-xs rounded-xl border border-rose-200 bg-white/90 text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#ee4d2d] shadow-2xs font-bold">
                            </div>

                            <!-- Chế độ chọn số lượng món/trang -->
                            <div class="bg-white/90 p-0.5 rounded-xl border border-rose-200 flex items-center text-[11px] font-extrabold shadow-2xs">
                                <button type="button" onclick="setSingleDishLimit(5)" id="btn-sd-limit-5" class="px-2.5 py-1 rounded-lg bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black">
                                    5 món
                                </button>
                                <button type="button" onclick="setSingleDishLimit(10)" id="btn-sd-limit-10" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                                    10 món
                                </button>
                                <button type="button" onclick="setSingleDishLimit(999)" id="btn-sd-limit-all" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                                    Tất cả
                                </button>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-[#ee4d2d] text-white text-xs font-black shadow-xs">
                                {{ $singleDishes->sum('total_qty') }} Suất
                            </span>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        @if($singleDishes->isEmpty())
                            <div class="text-center py-10 space-y-2">
                                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold mx-auto">
                                    <i class="fas fa-check"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700">Đã chế biến hoàn tất!</p>
                                <p class="text-[11px] text-slate-400">Không có đơn lẻ nào cần làm lúc này.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs" id="table-single-dishes">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                                            <th class="py-3 px-3.5">Tên Món Ăn</th>
                                            <th class="py-3 px-3.5">Mã Vận Đơn</th>
                                            <th class="py-3 px-3.5 text-center">Cần Làm</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100" id="tbody-single-dishes">
                                        @foreach($singleDishes as $item)
                                            @php
                                                $ordersCount = $item->orders->count();
                                                $previewOrders = $item->orders->take(2);
                                                $moreCount = $ordersCount - 2;
                                                $ordersDataJson = json_encode($item->orders->map(fn($o) => [
                                                    'order_id' => $o->order_id,
                                                    'tracking_code' => $o->tracking_code,
                                                    'quantity' => $o->quantity,
                                                    'order_status' => $o->order_status,
                                                    'payment_status' => $o->payment_status,
                                                    'customer_name' => $o->customer_name,
                                                    'health_notes' => $o->health_notes,
                                                ]));
                                            @endphp
                                            <tr class="hover:bg-slate-50/80 transition-colors single-dish-row" data-dish-name="{{ mb_strtolower($item->dish->dish_name ?? '') }}">
                                                <td class="py-2.5 px-3.5 font-extrabold text-slate-900 align-middle">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full bg-[#ee4d2d] shrink-0"></span>
                                                        <span class="font-extrabold text-xs text-slate-900">{{ $item->dish->dish_name ?? 'Món ăn không tồn tại' }}</span>
                                                    </div>
                                                    @if(isset($item->dish->category))
                                                        <span class="text-[10px] text-slate-400 font-semibold ml-4 block">{{ $item->dish->category->category_name }}</span>
                                                    @endif
                                                </td>
                                                <td class="py-2.5 px-3.5 align-middle">
                                                    <div class="flex flex-wrap items-center gap-1.5">
                                                        <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-[10px] font-black border border-slate-200 shrink-0">
                                                            {{ $ordersCount }} đơn
                                                        </span>

                                                        @foreach($previewOrders as $ord)
                                                            <a href="{{ route('donhang_xem', $ord->order_id) }}" 
                                                               title="Khách: {{ $ord->customer_name }} • {{ $ord->health_notes ?: 'Không có ghi chú' }}"
                                                               class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-black border transition-all hover:scale-105 shadow-2xs shrink-0
                                                                      @if($ord->order_status === 'pending') bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100
                                                                      @elseif($ord->order_status === 'confirmed') bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100
                                                                      @else bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100 @endif">
                                                                <span>#{{ $ord->tracking_code }}</span>
                                                                <span class="px-1 py-0.1 rounded bg-white text-[9px] font-black text-slate-800 shadow-2xs">
                                                                    {{ $ord->quantity }}
                                                                </span>
                                                            </a>
                                                        @endforeach

                                                        @if($moreCount > 0)
                                                            <button type="button" 
                                                                    data-dish-name="{{ $item->dish->dish_name ?? 'Món ăn' }}"
                                                                    data-total-qty="{{ $item->total_qty }}"
                                                                    data-orders="{{ e($ordersDataJson) }}"
                                                                    onclick="openDishOrdersModalFromBtn(this)"
                                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-black bg-rose-50 hover:bg-rose-100 text-[#ee4d2d] border border-rose-200 transition-colors cursor-pointer shadow-2xs shrink-0">
                                                                <span>+{{ $moreCount }} đơn khác</span>
                                                                <i class="fas fa-chevron-right text-[8px]"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="py-2.5 px-3.5 text-center align-middle">
                                                    <span class="px-2.5 py-1 rounded-xl bg-rose-50 text-[#ee4d2d] text-xs font-black border border-rose-200 whitespace-nowrap">
                                                        {{ $item->total_qty }} suất
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Thanh phân trang cho Món Ăn Đơn Hàng Lẻ -->
                            <div id="single-dishes-pagination-bar" class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                                <div class="text-slate-500 font-medium text-[11px]">
                                    Hiển thị món <span id="sd-page-from" class="font-extrabold text-slate-900">1</span> - <span id="sd-page-to" class="font-extrabold text-slate-900">5</span> / <span id="sd-total-dishes" class="font-extrabold text-slate-900">{{ $singleDishes->count() }}</span> món
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="changeSingleDishPage(-1)" id="btn-sd-prev" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition-colors shadow-2xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                        <i class="fas fa-chevron-left text-[9px]"></i> Trước
                                    </button>
                                    <div id="sd-page-numbers" class="flex items-center gap-1"></div>
                                    <button type="button" onclick="changeSingleDishPage(1)" id="btn-sd-next" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition-colors shadow-2xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                        Sau <i class="fas fa-chevron-right text-[9px]"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel 2: Subscriptions (Món Ăn Cho Gói Combo Định Kỳ) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col justify-between" id="panel-subscription-dishes">
                <div>
                    <div class="p-4 sm:p-5 bg-amber-50 border-b border-amber-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-box-archive text-amber-600"></i> Món Ăn Cho Gói Combo Định Kỳ
                            </h3>
                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black border border-amber-200" id="badge-subscription-dishes-count">
                                {{ $subscriptionDishes->count() }} món
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Chế độ chọn số lượng món/trang -->
                            <div class="bg-white/90 p-0.5 rounded-xl border border-amber-200 flex items-center text-[11px] font-extrabold shadow-2xs">
                                <button type="button" onclick="setSubscriptionDishLimit(5)" id="btn-subd-limit-5" class="px-2.5 py-1 rounded-lg bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black">
                                    5 món
                                </button>
                                <button type="button" onclick="setSubscriptionDishLimit(10)" id="btn-subd-limit-10" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                                    10 món
                                </button>
                                <button type="button" onclick="setSubscriptionDishLimit(999)" id="btn-subd-limit-all" class="px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                                    Tất cả
                                </button>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-amber-500 text-white text-xs font-black shadow-xs">
                                {{ $subscriptionDishes->sum('total_qty') }} Suất
                            </span>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        @if($subscriptionDishes->isEmpty())
                            <div class="text-center py-10 space-y-2">
                                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold mx-auto">
                                    <i class="fas fa-check"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700">Đã nấu xong gói định kỳ!</p>
                                <p class="text-[11px] text-slate-400">Không có lịch giao gói combo nào hôm nay.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs" id="table-subscription-dishes">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                                            <th class="py-3 px-3.5">Tên Món Ăn</th>
                                            <th class="py-3 px-3.5">Mã Lịch Giao / Đăng Ký</th>
                                            <th class="py-3 px-3.5 text-center">Cần Làm</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100" id="tbody-subscription-dishes">
                                        @foreach($subscriptionDishes as $item)
                                            @php
                                                $schedules = $item->schedules ?? collect();
                                                $schedCount = $schedules->count();
                                                $previewSched = $schedules->take(2);
                                                $moreSched = $schedCount - 2;
                                            @endphp
                                            <tr class="hover:bg-slate-50/80 transition-colors subscription-dish-row">
                                                <td class="py-2.5 px-3.5 font-extrabold text-slate-900 align-middle">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                                        <span class="font-extrabold text-xs text-slate-900">{{ $item->dish->dish_name ?? 'Món ăn không tồn tại' }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-2.5 px-3.5 align-middle">
                                                    <div class="flex flex-wrap items-center gap-1.5">
                                                        <span class="px-2 py-0.5 rounded-lg bg-amber-100 text-amber-900 text-[10px] font-black border border-amber-200 shrink-0">
                                                            {{ $schedCount }} lịch giao
                                                        </span>
                                                        @foreach($previewSched as $sch)
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-amber-50 text-amber-800 border border-amber-200 shrink-0"
                                                                  title="Hội viên: {{ $sch->customer_name }} • {{ $sch->package_name }} • {{ $sch->delivery_notes ?: 'Không có ghi chú' }}">
                                                                <i class="fas fa-box-open text-[9px] opacity-70"></i>
                                                                <span>#{{ $sch->tracking_code }}</span>
                                                            </span>
                                                        @endforeach
                                                        @if($moreSched > 0)
                                                            <span class="text-[10px] font-extrabold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200 shrink-0">
                                                                +{{ $moreSched }} lịch khác
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="py-2.5 px-3.5 text-center align-middle">
                                                    <span class="px-2.5 py-1 rounded-xl bg-amber-50 text-amber-700 text-xs font-black border border-amber-200 whitespace-nowrap">
                                                        {{ $item->total_qty }} suất
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Thanh phân trang cho Món Combo Định Kỳ -->
                            <div id="subscription-dishes-pagination-bar" class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                                <div class="text-slate-500 font-medium text-[11px]">
                                    Hiển thị món <span id="subd-page-from" class="font-extrabold text-slate-900">1</span> - <span id="subd-page-to" class="font-extrabold text-slate-900">5</span> / <span id="subd-total-dishes" class="font-extrabold text-slate-900">{{ $subscriptionDishes->count() }}</span> món
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="changeSubscriptionDishPage(-1)" id="btn-subd-prev" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition-colors shadow-2xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                        <i class="fas fa-chevron-left text-[9px]"></i> Trước
                                    </button>
                                    <div id="subd-page-numbers" class="flex items-center gap-1"></div>
                                    <button type="button" onclick="changeSubscriptionDishPage(1)" id="btn-subd-next" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition-colors shadow-2xs cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                        Sau <i class="fas fa-chevron-right text-[9px]"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL: DANH SÁCH CHI TIẾT MÃ VẬN ĐƠN CỦA TỪNG MÓN ĂN (MISE EN PLACE) -->
    <div id="dishOrdersModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity">
        <div class="bg-white rounded-3xl max-w-2xl w-full overflow-hidden shadow-2xl border border-slate-200 flex flex-col max-h-[85vh]">
            <!-- Header Modal -->
            <div class="p-5 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white flex items-center justify-between shrink-0 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-500/20 text-[#ee4d2d] flex items-center justify-center text-lg border border-rose-500/30">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-sm tracking-wide flex items-center gap-2">
                            <span id="modal-dish-title">Tên Món Ăn</span>
                            <span id="modal-dish-qty-badge" class="px-2.5 py-0.5 rounded-full bg-[#ee4d2d] text-white text-[11px] font-black">0 suất</span>
                        </h3>
                        <p class="text-[11px] text-slate-300 font-medium" id="modal-dish-subtitle">Danh sách tất cả các mã đơn hàng có món này</p>
                    </div>
                </div>
                <button type="button" onclick="closeDishOrdersModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg cursor-pointer transition-colors">
                    &times;
                </button>
            </div>

            <!-- Thanh tìm kiếm nhanh bên trong Modal -->
            <div class="p-3 bg-slate-100/90 border-b border-slate-200 shrink-0">
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           id="modal-dish-search" 
                           oninput="filterDishOrdersInModal(this.value)" 
                           placeholder="🔍 Tìm nhanh theo mã đơn (vd: 1313), tên khách, ghi chú..." 
                           class="w-full pl-9 pr-8 py-2 rounded-xl bg-white border border-slate-200 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500 shadow-2xs">
                </div>
            </div>

            <!-- Danh sách các mã đơn có món này -->
            <div class="p-4 overflow-y-auto space-y-2 flex-1 bg-slate-50/50" id="modal-dish-orders-container">
                <!-- Render động bằng JS -->
            </div>

            <!-- Footer Modal -->
            <div class="p-4 bg-white border-t border-slate-200 flex items-center justify-between shrink-0">
                <span class="text-xs text-slate-500 font-medium" id="modal-dish-footer-count">Đang hiển thị 0 đơn</span>
                <button type="button" onclick="closeDishOrdersModal()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors cursor-pointer shadow-xs">
                    Đóng
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Trạng thái quản lý KDS (Kitchen Display System)
    let currentViewLimit = 3; // Mặc định chỉ hiển thị 3 đơn tập trung
    let currentKitchenPage = 1; // Trang hiện tại của hàng đợi bếp
    let searchKeyword = '';
    let completedOrdersList = @json($initialCompletedOrders ?? []);
    let modalFilterText = '';

    // Trạng thái phân trang danh sách món ăn (Mise en Place)
    let singleDishPage = 1;
    let singleDishLimit = 5;
    let subscriptionDishPage = 1;
    let subscriptionDishLimit = 5;

    document.addEventListener("DOMContentLoaded", function () {
        // Khởi tạo hàng đợi bếp, danh sách đơn hoàn thành & phân trang món ăn
        renderKitchenQueue();
        updateCompletedOrdersModal();
        renderSingleDishesPagination();
        renderSubscriptionDishesPagination();

        // Polling cập nhật dữ liệu realtime
        let lastCheckedTime = null;
        fetch("{{ route('api.orders.poll') }}")
            .then(response => response.json())
            .then(data => {
                lastCheckedTime = data.timestamp;
                setInterval(pollKitchenUpdates, 3000);
            })
            .catch(err => console.error('Error initializing kitchen polling:', err));

        function pollKitchenUpdates() {
            if (!lastCheckedTime) return;

            fetch(`{{ route('api.orders.poll') }}?since=${encodeURIComponent(lastCheckedTime)}`)
                .then(response => response.json())
                .then(data => {
                    lastCheckedTime = data.timestamp;
                    if (data.updates && data.updates.length > 0) {
                        let hasNewOrder = data.updates.some(e => e.action === 'created');
                        if (hasNewOrder) {
                            showToast('🛎️ Có đơn hàng mới vừa được khách đặt!', 'info');
                            setTimeout(() => { window.location.reload(); }, 1200);
                        }
                    }
                })
                .catch(err => console.error('Error during kitchen polling:', err));
        }
    });

    // Chuyển trang hàng đợi bếp
    function changeKitchenPage(delta) {
        currentKitchenPage += delta;
        renderKitchenQueue();
    }

    function setKitchenPage(page) {
        currentKitchenPage = page;
        renderKitchenQueue();
    }

    // Hàm render lại hàng đợi hiển thị đơn hàng (KDS Queue)
    function renderKitchenQueue() {
        const grid = document.getElementById('kitchen-orders-grid');
        const emptySearch = document.getElementById('kitchen-search-empty');
        const activeBadge = document.getElementById('active-orders-count-badge');
        const paginationBar = document.getElementById('kitchen-pagination-bar');
        if (!grid) return;

        // Lấy tất cả các thẻ đơn chưa hoàn thành
        const activeCards = Array.from(grid.querySelectorAll('.order-ticket-card:not(.order-completed)'));

        if (activeBadge) {
            activeBadge.innerText = activeCards.length;
        }

        // Trường hợp 1: Đang có từ khóa tìm kiếm
        if (searchKeyword !== '') {
            if (paginationBar) paginationBar.style.display = 'none';

            let matchedCount = 0;
            activeCards.forEach(card => {
                const searchData = card.getAttribute('data-search-text') || '';
                if (searchData.includes(searchKeyword)) {
                    card.style.display = 'flex';
                    card.style.opacity = '1';
                    card.style.transform = 'none';
                    matchedCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (emptySearch) {
                emptySearch.style.display = matchedCount === 0 ? 'block' : 'none';
            }
            grid.style.display = matchedCount === 0 ? 'none' : 'grid';

            const feedback = document.getElementById('kitchen-search-feedback');
            const feedbackText = document.getElementById('kitchen-search-feedback-text');
            if (feedback && feedbackText) {
                feedback.classList.remove('hidden');
                feedbackText.innerText = `Tìm thấy ${matchedCount} đơn`;
            }
            return;
        }

        // Trường hợp 2: Chế độ hiển thị hàng đợi thông thường (FIFO Queue)
        if (emptySearch) emptySearch.style.display = 'none';
        grid.style.display = 'grid';

        const feedback = document.getElementById('kitchen-search-feedback');
        if (feedback) feedback.classList.add('hidden');

        const totalActive = activeCards.length;
        let startIndex = 0;
        let endIndex = totalActive;

        if (currentViewLimit < 999) {
            const totalPages = Math.max(1, Math.ceil(totalActive / currentViewLimit));
            if (currentKitchenPage > totalPages) currentKitchenPage = totalPages;
            if (currentKitchenPage < 1) currentKitchenPage = 1;

            startIndex = (currentKitchenPage - 1) * currentViewLimit;
            endIndex = Math.min(startIndex + currentViewLimit, totalActive);

            if (paginationBar) {
                paginationBar.style.display = totalActive > currentViewLimit ? 'flex' : 'none';
                const pageFrom = document.getElementById('kds-page-from');
                const pageTo = document.getElementById('kds-page-to');
                const totalElem = document.getElementById('kds-total-orders');
                const btnPrev = document.getElementById('btn-kds-prev');
                const btnNext = document.getElementById('btn-kds-next');
                const pageNums = document.getElementById('kds-page-numbers');

                if (pageFrom) pageFrom.innerText = totalActive === 0 ? 0 : startIndex + 1;
                if (pageTo) pageTo.innerText = endIndex;
                if (totalElem) totalElem.innerText = totalActive;

                if (btnPrev) btnPrev.disabled = (currentKitchenPage <= 1);
                if (btnNext) btnNext.disabled = (currentKitchenPage >= totalPages);

                if (pageNums) {
                    pageNums.innerHTML = '';
                    let startPage = Math.max(1, currentKitchenPage - 2);
                    let endPage = Math.min(totalPages, startPage + 4);
                    if (endPage - startPage < 4) {
                        startPage = Math.max(1, endPage - 4);
                    }

                    for (let p = startPage; p <= endPage; p++) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.onclick = () => setKitchenPage(p);
                        btn.innerText = p;
                        if (p === currentKitchenPage) {
                            btn.className = 'w-7 h-7 rounded-xl bg-[#ee4d2d] text-white font-black text-xs shadow-xs';
                        } else {
                            btn.className = 'w-7 h-7 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-xs transition-colors shadow-2xs';
                        }
                        pageNums.appendChild(btn);
                    }
                }
            }
        } else {
            if (paginationBar) paginationBar.style.display = 'none';
        }

        activeCards.forEach((card, index) => {
            const badgeContainer = card.querySelector('.queue-priority-badge');
            const isPreparing = card.getAttribute('data-order-status') === 'preparing';

            if (index >= startIndex && index < endIndex) {
                // Hiển thị thẻ đơn
                if (card.style.display === 'none') {
                    card.style.display = 'flex';
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(15px)';
                    setTimeout(() => {
                        card.style.transition = 'all 0.35s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 30);
                } else {
                    card.style.display = 'flex';
                }

                // Cập nhật lại số thứ tự ưu tiên động
                if (badgeContainer) {
                    if (isPreparing) {
                        badgeContainer.innerHTML = `<span class="px-2.5 py-0.5 rounded-full bg-purple-600 text-white font-black text-[10px] tracking-wide uppercase flex items-center gap-1 shadow-2xs"><i class="fas fa-fire-burner animate-bounce"></i> Đang nấu</span>`;
                    } else if (index === 0) {
                        badgeContainer.innerHTML = `<span class="px-2.5 py-0.5 rounded-full bg-rose-500 text-white font-black text-[10px] tracking-wide uppercase flex items-center gap-1 shadow-2xs"><i class="fas fa-bolt"></i> Ưu tiên #1</span>`;
                    } else if (index === 1) {
                        badgeContainer.innerHTML = `<span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-white font-black text-[10px] tracking-wide uppercase flex items-center gap-1">Ưu tiên #2</span>`;
                    } else if (index === 2) {
                        badgeContainer.innerHTML = `<span class="px-2.5 py-0.5 rounded-full bg-blue-500 text-white font-black text-[10px] tracking-wide uppercase flex items-center gap-1">Ưu tiên #3</span>`;
                    } else {
                        badgeContainer.innerHTML = `<span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[10px] tracking-wide uppercase">Hàng đợi #${index + 1}</span>`;
                    }
                }

                // Cập nhật viền nổi bật cho thẻ ưu tiên
                if (isPreparing) {
                    card.className = card.className.replace(/border-\w+-\d+/g, 'border-purple-500').replace(/bg-\w+-\d+\/\d+/g, 'bg-purple-50/20');
                } else if (index === 0) {
                    card.className = card.className.replace(/border-\w+-\d+/g, 'border-amber-400').replace(/bg-\w+-\d+\/\d+/g, 'bg-amber-50/20');
                } else {
                    card.className = card.className.replace(/border-\w+-\d+/g, 'border-slate-200').replace(/bg-\w+-\d+\/\d+/g, 'bg-white');
                }
            } else {
                // Đơn nằm ngoài giới hạn view limit thì tạm ẩn
                card.style.display = 'none';
            }
        });
    }

    // Xử lý ô tìm kiếm đơn hàng realtime
    function handleKitchenSearch(val) {
        searchKeyword = (val || '').trim().toLowerCase();
        const clearBtn = document.getElementById('btn-clear-kitchen-search');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', searchKeyword === '');
        }
        renderKitchenQueue();
    }

    // Xóa ô tìm kiếm
    function clearKitchenSearch() {
        const input = document.getElementById('kitchen-search-input');
        if (input) input.value = '';
        handleKitchenSearch('');
    }

    // Chọn số lượng đơn hiển thị tập trung (3 / 6 / tất cả)
    function setKitchenViewLimit(limit) {
        currentViewLimit = limit;
        currentKitchenPage = 1;
        const btn3 = document.getElementById('btn-view-limit-3');
        const btn6 = document.getElementById('btn-view-limit-6');
        const btnAll = document.getElementById('btn-view-limit-all');

        [btn3, btn6, btnAll].forEach(btn => {
            if (btn) {
                btn.className = 'px-3 py-1.5 rounded-xl text-slate-600 hover:text-slate-900 transition-all cursor-pointer';
            }
        });

        if (limit === 3 && btn3) {
            btn3.className = 'px-3 py-1.5 rounded-xl bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black';
        } else if (limit === 6 && btn6) {
            btn6.className = 'px-3 py-1.5 rounded-xl bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black';
        } else if (limit >= 999 && btnAll) {
            btnAll.className = 'px-3 py-1.5 rounded-xl bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black';
        }

        renderKitchenQueue();
        showToast(`Đã chuyển sang chế độ hiển thị ${limit >= 999 ? 'Tất cả' : limit + ' đơn'}`, 'info');
    }

    // Xử lý Checkbox tick từng món ăn trong đơn
    function handleDishCheckboxChange(checkbox, orderId, paymentStatus) {
        const row = checkbox.closest('label');
        const nameSpan = row ? row.querySelector('.dish-item-name') : null;
        if (checkbox.checked) {
            if (nameSpan) nameSpan.classList.add('line-through', 'text-slate-400');
            row.classList.add('bg-emerald-50/60');
        } else {
            if (nameSpan) nameSpan.classList.remove('line-through', 'text-slate-400');
            row.classList.remove('bg-emerald-50/60');
        }

        const card = document.getElementById('order-ticket-' + orderId);
        if (!card) return;

        const allBoxes = card.querySelectorAll('.dish-item-checkbox');
        const checkedBoxes = card.querySelectorAll('.dish-item-checkbox:checked');
        const total = allBoxes.length;
        const count = checkedBoxes.length;

        // Cập nhật text tiến độ
        const progressSpan = document.getElementById('dish-progress-' + orderId);
        if (progressSpan) {
            progressSpan.innerText = count === total ? '✓ ĐỦ ' + total + ' MÓN' : `Đã làm: ${count}/${total} món`;
            progressSpan.className = count === total ? 'text-emerald-600 font-black' : 'text-[#ee4d2d] font-bold';
        }

        const completeBtn = document.getElementById('btn-complete-' + orderId);
        const header = document.getElementById('ticket-header-' + orderId);
        const notice = document.getElementById('order-ready-notice-' + orderId);

        if (count === total && total > 0) {
            // Đã xong tất cả các món trong đơn này!
            if (completeBtn) {
                completeBtn.classList.remove('hidden');
                completeBtn.classList.add('animate-pulse', 'bg-emerald-600');
            }
            if (header) {
                header.classList.add('bg-emerald-100/90', 'border-emerald-200');
            }

            // Tự động chuyển đơn nếu tùy chọn đang bật
            const autoAdvance = document.getElementById('chk-auto-advance');
            if (autoAdvance && autoAdvance.checked) {
                if (notice) notice.classList.remove('hidden');
                setTimeout(() => {
                    completeOrderTicket(orderId, paymentStatus, true);
                }, 600);
            }
        } else {
            if (notice) notice.classList.add('hidden');
            if (completeBtn && card.getAttribute('data-order-status') !== 'preparing') {
                completeBtn.classList.add('hidden');
            }
            if (header) {
                header.classList.remove('bg-emerald-100/90', 'border-emerald-200');
            }
        }
    }

    // Bắt đầu nấu một đơn hàng (chuyển sang 'preparing' realtime không reload trang)
    function startCookingOrder(orderId, paymentStatus) {
        const card = document.getElementById('order-ticket-' + orderId);
        if (!card) return;

        card.setAttribute('data-order-status', 'preparing');
        const startBtn = document.getElementById('btn-start-' + orderId);
        const completeBtn = document.getElementById('btn-complete-' + orderId);
        if (startBtn) startBtn.classList.add('hidden');
        if (completeBtn) completeBtn.classList.remove('hidden');

        // Cập nhật badge sang "Đang nấu"
        const badge = document.getElementById('queue-badge-' + orderId);
        if (badge) {
            badge.innerHTML = `<span class="px-2.5 py-0.5 rounded-full bg-purple-600 text-white font-black text-[10px] tracking-wide uppercase flex items-center gap-1 shadow-2xs"><i class="fas fa-fire-burner animate-bounce"></i> Đang nấu</span>`;
        }

        showToast(`🍳 Đang chế biến mã đơn #FDL-${orderId}`, 'info');

        // Gửi AJAX ngầm cập nhật status
        fetch("{{ url('/donhang_chinhsua') }}/" + orderId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                order_status: 'preparing',
                payment_status: paymentStatus || 'paid',
                admin_notes: 'Nhà bếp bắt đầu nấu đơn'
            })
        }).catch(err => console.error('Start cooking error:', err));
    }

    // KHI ĐẦU BẾP NẤU XONG: Đơn hiện tại tạm mất và đơn mới tự động trượt lên!
    function completeOrderTicket(orderId, paymentStatus, isAuto = false) {
        const card = document.getElementById('order-ticket-' + orderId);
        if (!card || card.classList.contains('order-completed')) return;

        // 1. Hiệu ứng trượt mờ dần và thu nhỏ mượt mà
        card.style.transition = 'all 0.35s cubic-bezier(0.4, 0, 0.2, 1)';
        card.style.transform = 'translateY(-20px) scale(0.95)';
        card.style.opacity = '0';

        setTimeout(() => {
            // 2. Đánh dấu thẻ đã hoàn thành và ẩn khỏi DOM
            card.classList.add('order-completed');
            card.style.display = 'none';

            // 3. Trích xuất đầy đủ thông tin để phục vụ tra cứu chi tiết và giải quyết sự cố
            const customerName = card.getAttribute('data-customer-name') || 'Khách hàng';
            const phone = card.getAttribute('data-phone') || '';
            const address = card.getAttribute('data-address') || '';
            const healthNotes = card.getAttribute('data-health-notes') || '';
            const totalItems = parseInt(card.getAttribute('data-total-items') || '1');
            const finalAmount = card.getAttribute('data-amount') || '';
            let dishes = [];
            try {
                dishes = JSON.parse(card.getAttribute('data-dishes-json') || '[]');
            } catch(e) {
                dishes = [];
            }

            // Xóa item cũ nếu đã tồn tại trong mảng
            completedOrdersList = completedOrdersList.filter(item => item.id !== orderId);

            // Thêm vào đầu danh sách vừa hoàn thành
            completedOrdersList.unshift({
                id: orderId,
                customer: customerName,
                phone: phone,
                address: address,
                healthNotes: healthNotes,
                totalItems: totalItems,
                finalAmount: finalAmount,
                orderStatus: 'delivering',
                paymentStatus: paymentStatus || 'paid',
                time: new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
                dishes: dishes
            });

            updateCompletedOrdersModal();

            // 4. Gọi renderQueue: ĐƠN TIẾP THEO SẼ TỰ ĐỘNG ĐƯỢC ĐẨY LÊN VÀ ĐÁNH SỐ LẠI ƯU TIÊN!
            renderKitchenQueue();

            // 5. Thông báo Toast
            showToast(`🎉 Đã nấu xong đơn #FDL-${orderId}! Đơn tiếp theo đã được chuyển lên bếp.`, 'success');

            // 6. Gửi request AJAX ngầm chuyển trạng thái đơn sang 'delivering'
            fetch("{{ url('/donhang_chinhsua') }}/" + orderId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_status: 'delivering',
                    payment_status: paymentStatus || 'paid',
                    admin_notes: 'Nhà bếp đã nấu xong toàn bộ món, tự động chuyển đơn tiếp theo lên bếp'
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log('Order status updated successfully:', data);
            })
            .catch(err => {
                console.error('Error background updating order status:', err);
            });

        }, 320);
    }

    // Modal và Danh sách đơn vừa nấu xong hôm nay
    function toggleCompletedOrdersModal() {
        const modal = document.getElementById('completedOrdersModal');
        if (modal) modal.classList.toggle('hidden');
    }

    // Lọc tìm kiếm đơn bên trong Modal
    function filterCompletedOrdersModal(query) {
        modalFilterText = (query || '').trim().toLowerCase();
        const clearBtn = document.getElementById('btn-clear-modal-search');
        if (clearBtn) clearBtn.classList.toggle('hidden', modalFilterText === '');
        updateCompletedOrdersModal();
    }

    function clearModalSearch() {
        const input = document.getElementById('modal-search-completed');
        if (input) input.value = '';
        filterCompletedOrdersModal('');
    }

    // Cập nhật giao diện Modal danh sách đơn vừa nấu xong
    function updateCompletedOrdersModal() {
        const badge = document.getElementById('badge-completed-count');
        if (badge) badge.innerText = completedOrdersList.length;

        const content = document.getElementById('completed-orders-list-content');
        if (!content) return;

        if (completedOrdersList.length === 0) {
            content.innerHTML = `
                <div class="text-center py-12 text-xs text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3 text-slate-300 block"></i>
                    <p class="font-bold text-slate-600">Chưa có đơn nào vừa nấu xong trong phiên này.</p>
                    <p class="text-[11px] text-slate-400 mt-1">Các đơn sau khi đầu bếp bấm "Nấu xong đơn" sẽ tự động lưu vào đây để tiện xem lại hoặc xử lý khi khách có vấn đề.</p>
                </div>`;
            return;
        }

        const filteredList = completedOrdersList.filter(item => {
            if (!modalFilterText) return true;
            const dishesText = (item.dishes || []).map(d => d.name).join(' ');
            const searchHaystack = `#fdl-${item.id} ${item.id} ${item.customer} ${item.phone} ${item.address} ${item.healthNotes} ${dishesText}`.toLowerCase();
            return searchHaystack.includes(modalFilterText);
        });

        if (filteredList.length === 0) {
            content.innerHTML = `
                <div class="text-center py-10 text-xs text-slate-400">
                    <i class="fas fa-search text-3xl mb-2 text-slate-300 block"></i>
                    <p class="font-bold text-slate-600">Không tìm thấy đơn nào khớp với "${modalFilterText}"</p>
                    <button type="button" onclick="clearModalSearch()" class="mt-2.5 px-3.5 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold transition-colors cursor-pointer">
                        Xóa tìm kiếm
                    </button>
                </div>`;
            return;
        }

        let html = '';
        filteredList.forEach(item => {
            const dishesHtml = (item.dishes && item.dishes.length > 0)
                ? item.dishes.map(d => `
                    <div class="flex items-center justify-between py-1 border-b border-dashed border-slate-100 last:border-none text-xs">
                        <span class="font-semibold text-slate-800">• ${d.name}</span>
                        <span class="font-black text-[#ee4d2d] bg-rose-50 px-2 py-0.5 rounded-lg text-[11px]">${d.qty} suất</span>
                    </div>
                `).join('')
                : '<p class="text-xs text-slate-400 italic">Không có thông tin món</p>';

            const statusBadge = (item.orderStatus === 'delivering')
                ? `<span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black"><i class="fas fa-motorcycle text-xs mr-1"></i>Đang giao hàng</span>`
                : (item.orderStatus === 'completed'
                    ? `<span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black"><i class="fas fa-check-double text-xs mr-1"></i>Đã giao xong</span>`
                    : `<span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-black">${item.orderStatus}</span>`);

            const notesHtml = item.healthNotes ? `
                <div class="p-2.5 rounded-xl bg-amber-50/90 border border-amber-200 text-amber-900 text-xs space-y-0.5">
                    <div class="font-black text-[10px] uppercase text-amber-700 flex items-center gap-1">
                        <i class="fas fa-triangle-exclamation text-amber-500"></i> Ghi chú của khách:
                    </div>
                    <p class="text-[11px] font-semibold leading-relaxed pl-3 border-l-2 border-amber-300">
                        ${item.healthNotes}
                    </p>
                </div>
            ` : '';

            const phoneHtml = item.phone 
                ? `<a href="tel:${item.phone}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-bold bg-blue-50 hover:bg-blue-100 px-2 py-0.5 rounded-lg transition-colors" title="Gọi cho khách"><i class="fas fa-phone text-[10px]"></i> ${item.phone}</a>`
                : '<span class="text-slate-400 text-[11px]">Chưa có SĐT</span>';

            const addressHtml = item.address
                ? `<p class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5"><i class="fas fa-location-dot text-rose-500 text-[10px] shrink-0"></i><span class="truncate">${item.address}</span></p>`
                : '';

            html += `
                <div class="bg-white rounded-2xl p-4 border border-slate-200 hover:border-slate-300 shadow-2xs hover:shadow-xs transition-all space-y-3" id="modal-order-card-${item.id}">
                    <!-- Card Top Header -->
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-2.5 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-xl bg-slate-900 text-white font-black text-xs tracking-wider shadow-2xs flex items-center gap-1">
                                <i class="fas fa-barcode text-[10px] text-amber-400"></i>
                                #FDL-${item.id}
                            </span>
                            ${statusBadge}
                            <span class="text-[11px] text-slate-400 font-semibold"><i class="far fa-clock text-[10px] mr-0.5"></i>${item.time}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-black text-[#ee4d2d]">${item.finalAmount || ''}</span>
                        </div>
                    </div>

                    <!-- Customer & Contact Info -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                <i class="far fa-user text-slate-400"></i> ${item.customer}
                            </span>
                            ${phoneHtml}
                        </div>
                        ${addressHtml}
                    </div>

                    <!-- Ghi chú đặc biệt của khách (khẩu vị, dị ứng, giao gấp...) -->
                    ${notesHtml}

                    <!-- Danh sách các món ăn đã nấu -->
                    <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100">
                        <div class="flex items-center justify-between text-[11px] font-black uppercase text-slate-400 tracking-wider mb-2">
                            <span>Chi tiết món ăn đã nấu (${item.totalItems || (item.dishes ? item.dishes.length : 0)} món):</span>
                            <span class="text-emerald-700 font-bold flex items-center gap-1"><i class="fas fa-circle-check text-xs"></i>Hoàn tất</span>
                        </div>
                        <div class="space-y-1">
                            ${dishesHtml}
                        </div>
                    </div>

                    <!-- Action Buttons: Giải quyết sự cố khi khách có vấn đề -->
                    <div class="pt-2 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <!-- Nút Xem Hóa Đơn Chi Tiết -->
                            <a href="{{ url('/donhang_xem') }}/${item.id}" target="_blank"
                               class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold transition-all shadow-2xs flex items-center gap-1.5"
                               title="Mở toàn bộ hóa đơn chi tiết trong tab mới">
                                <i class="far fa-eye text-slate-400"></i> Xem chi tiết
                            </a>

                            <!-- Nút Chỉnh sửa / Xử lý sự cố đơn hàng -->
                            <a href="{{ url('/donhang_chinhsua') }}/${item.id}" target="_blank"
                               class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-bold transition-all flex items-center gap-1.5"
                               title="Cập nhật trạng thái hoặc xử lý khiếu nại của khách">
                                <i class="fas fa-pen-to-square text-blue-500"></i> Xử lý & Sửa đơn
                            </a>
                        </div>

                        <!-- Nút Hoàn tác: Đưa đơn quay lại bếp nấu lại -->
                        <button type="button" 
                                onclick="undoCompletedOrder(${item.id}, '${item.paymentStatus}')" 
                                class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-black transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs hover:scale-102"
                                title="Đưa đơn về lại màn hình bếp để nấu lại hoặc bổ sung món">
                            <i class="fas fa-rotate-left text-amber-600"></i> Nấu lại / Hoàn tác
                        </button>
                    </div>
                </div>
            `;
        });

        content.innerHTML = html;
    }

    // Hoàn tác đưa đơn quay lại bếp nếu tick nhầm hoặc khách cần làm lại
    function undoCompletedOrder(orderId, paymentStatus) {
        const card = document.getElementById('order-ticket-' + orderId);

        // Xóa khỏi mảng completed
        completedOrdersList = completedOrdersList.filter(item => item.id !== orderId);
        updateCompletedOrdersModal();

        showToast(`Đang chuyển đơn #FDL-${orderId} quay lại bếp...`, 'info');

        // Gửi AJAX ngầm cập nhật lại 'preparing'
        fetch("{{ url('/donhang_chinhsua') }}/" + orderId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                order_status: 'preparing',
                payment_status: paymentStatus || 'paid',
                admin_notes: 'Nhà bếp hoàn tác: Đưa đơn quay lại nấu tiếp cho khách'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (card) {
                // Gỡ đánh dấu completed
                card.classList.remove('order-completed');
                card.style.opacity = '1';
                card.style.transform = 'none';
                card.setAttribute('data-order-status', 'preparing');
                renderKitchenQueue();
                showToast(`🎉 Đã đưa đơn #FDL-${orderId} quay lại bếp thành công!`, 'success');
            } else {
                // Nếu thẻ không có trong DOM (ví dụ đơn đã xong từ trước hoặc sau khi F5)
                showToast(`🎉 Đã phục hồi đơn #FDL-${orderId} về bếp! Đang tải lại...`, 'success');
                setTimeout(() => { window.location.reload(); }, 800);
            }
        })
        .catch(err => {
            console.error('Undo order error:', err);
            showToast(`Lỗi khi hoàn tác đơn #FDL-${orderId}`, 'error');
        });
    }

    // Hiển thị Toast thông báo nổi nhẹ nhàng
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-8 right-8 z-50 px-4 py-3 rounded-2xl text-xs font-black shadow-2xl flex items-center gap-2.5 transition-all duration-300 transform translate-y-4 opacity-0 ' +
            (type === 'success' ? 'bg-emerald-800 text-white border border-emerald-600' : 'bg-slate-900 text-white border border-slate-700');
        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-bell text-amber-400'} text-sm"></i> <span>${message}</span>`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        }, 20);

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => toast.remove(), 350);
        }, 2800);
    }

    // =========================================================================
    // PHÂN TRANG: MÓN ĂN CHO ĐƠN HÀNG LẺ (MISE EN PLACE)
    // =========================================================================
    function setSingleDishLimit(limit) {
        singleDishLimit = limit;
        singleDishPage = 1;
        const btn5 = document.getElementById('btn-sd-limit-5');
        const btn10 = document.getElementById('btn-sd-limit-10');
        const btnAll = document.getElementById('btn-sd-limit-all');

        [btn5, btn10, btnAll].forEach(b => {
            if (b) b.className = 'px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all cursor-pointer';
        });

        if (limit === 5 && btn5) {
            btn5.className = 'px-2.5 py-1 rounded-lg bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black';
        } else if (limit === 10 && btn10) {
            btn10.className = 'px-2.5 py-1 rounded-lg bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black';
        } else if (limit >= 999 && btnAll) {
            btnAll.className = 'px-2.5 py-1 rounded-lg bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black';
        }

        renderSingleDishesPagination();
    }

    function changeSingleDishPage(delta) {
        singleDishPage += delta;
        renderSingleDishesPagination();
    }

    function setSingleDishPage(p) {
        singleDishPage = p;
        renderSingleDishesPagination();
    }

    function renderSingleDishesPagination() {
        const rows = document.querySelectorAll('.single-dish-row');
        const paginationBar = document.getElementById('single-dishes-pagination-bar');
        const total = rows.length;
        if (total === 0) return;

        if (singleDishLimit >= 999) {
            rows.forEach(r => r.style.display = '');
            if (paginationBar) paginationBar.style.display = 'none';
            return;
        }

        const totalPages = Math.max(1, Math.ceil(total / singleDishLimit));
        if (singleDishPage > totalPages) singleDishPage = totalPages;
        if (singleDishPage < 1) singleDishPage = 1;

        const start = (singleDishPage - 1) * singleDishLimit;
        const end = Math.min(start + singleDishLimit, total);

        rows.forEach((r, idx) => {
            if (idx >= start && idx < end) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });

        if (paginationBar) {
            paginationBar.style.display = total > singleDishLimit ? 'flex' : 'none';
            const fromElem = document.getElementById('sd-page-from');
            const toElem = document.getElementById('sd-page-to');
            const totalElem = document.getElementById('sd-total-dishes');
            const btnPrev = document.getElementById('btn-sd-prev');
            const btnNext = document.getElementById('btn-sd-next');
            const pageNums = document.getElementById('sd-page-numbers');

            if (fromElem) fromElem.innerText = start + 1;
            if (toElem) toElem.innerText = end;
            if (totalElem) totalElem.innerText = total;

            if (btnPrev) btnPrev.disabled = (singleDishPage <= 1);
            if (btnNext) btnNext.disabled = (singleDishPage >= totalPages);

            if (pageNums) {
                pageNums.innerHTML = '';
                let startP = Math.max(1, singleDishPage - 2);
                let endP = Math.min(totalPages, startP + 4);
                if (endP - startP < 4) {
                    startP = Math.max(1, endP - 4);
                }

                for (let p = startP; p <= endP; p++) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.onclick = () => setSingleDishPage(p);
                    btn.innerText = p;
                    if (p === singleDishPage) {
                        btn.className = 'w-6 h-6 rounded-lg bg-[#ee4d2d] text-white font-black text-xs shadow-xs';
                    } else {
                        btn.className = 'w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-xs transition-colors shadow-2xs';
                    }
                    pageNums.appendChild(btn);
                }
            }
        }
    }

    // =========================================================================
    // PHÂN TRANG: MÓN ĂN CHO GÓI COMBO ĐỊNH KỲ
    // =========================================================================
    function setSubscriptionDishLimit(limit) {
        subscriptionDishLimit = limit;
        subscriptionDishPage = 1;
        const btn5 = document.getElementById('btn-subd-limit-5');
        const btn10 = document.getElementById('btn-subd-limit-10');
        const btnAll = document.getElementById('btn-subd-limit-all');

        [btn5, btn10, btnAll].forEach(b => {
            if (b) b.className = 'px-2.5 py-1 rounded-lg text-slate-600 hover:text-slate-900 transition-all cursor-pointer';
        });

        if (limit === 5 && btn5) {
            btn5.className = 'px-2.5 py-1 rounded-lg bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black';
        } else if (limit === 10 && btn10) {
            btn10.className = 'px-2.5 py-1 rounded-lg bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black';
        } else if (limit >= 999 && btnAll) {
            btnAll.className = 'px-2.5 py-1 rounded-lg bg-slate-900 text-amber-300 transition-all cursor-pointer shadow-2xs font-black';
        }

        renderSubscriptionDishesPagination();
    }

    function changeSubscriptionDishPage(delta) {
        subscriptionDishPage += delta;
        renderSubscriptionDishesPagination();
    }

    function setSubscriptionDishPage(p) {
        subscriptionDishPage = p;
        renderSubscriptionDishesPagination();
    }

    function renderSubscriptionDishesPagination() {
        const rows = document.querySelectorAll('.subscription-dish-row');
        const paginationBar = document.getElementById('subscription-dishes-pagination-bar');
        const total = rows.length;
        if (total === 0) return;

        if (subscriptionDishLimit >= 999) {
            rows.forEach(r => r.style.display = '');
            if (paginationBar) paginationBar.style.display = 'none';
            return;
        }

        const totalPages = Math.max(1, Math.ceil(total / subscriptionDishLimit));
        if (subscriptionDishPage > totalPages) subscriptionDishPage = totalPages;
        if (subscriptionDishPage < 1) subscriptionDishPage = 1;

        const start = (subscriptionDishPage - 1) * subscriptionDishLimit;
        const end = Math.min(start + subscriptionDishLimit, total);

        rows.forEach((r, idx) => {
            if (idx >= start && idx < end) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });

        if (paginationBar) {
            paginationBar.style.display = total > subscriptionDishLimit ? 'flex' : 'none';
            const fromElem = document.getElementById('subd-page-from');
            const toElem = document.getElementById('subd-page-to');
            const totalElem = document.getElementById('subd-total-dishes');
            const btnPrev = document.getElementById('btn-subd-prev');
            const btnNext = document.getElementById('btn-subd-next');
            const pageNums = document.getElementById('subd-page-numbers');

            if (fromElem) fromElem.innerText = start + 1;
            if (toElem) toElem.innerText = end;
            if (totalElem) totalElem.innerText = total;

            if (btnPrev) btnPrev.disabled = (subscriptionDishPage <= 1);
            if (btnNext) btnNext.disabled = (subscriptionDishPage >= totalPages);

            if (pageNums) {
                pageNums.innerHTML = '';
                let startP = Math.max(1, subscriptionDishPage - 2);
                let endP = Math.min(totalPages, startP + 4);
                if (endP - startP < 4) {
                    startP = Math.max(1, endP - 4);
                }

                for (let p = startP; p <= endP; p++) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.onclick = () => setSubscriptionDishPage(p);
                    btn.innerText = p;
                    if (p === subscriptionDishPage) {
                        btn.className = 'w-6 h-6 rounded-lg bg-[#ee4d2d] text-white font-black text-xs shadow-xs';
                    } else {
                        btn.className = 'w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-xs transition-colors shadow-2xs';
                    }
                    pageNums.appendChild(btn);
                }
            }
        }
    }

    // =========================================================================
    // TÌM KIẾM & MODAL CHI TIẾT MÃ VẬN ĐƠN CỦA MÓN ĂN (MISE EN PLACE)
    // =========================================================================
    let singleDishSearchQuery = '';

    function searchSingleDishes(query) {
        singleDishSearchQuery = (query || '').trim().toLowerCase();
        const rows = document.querySelectorAll('.single-dish-row');
        const paginationBar = document.getElementById('single-dishes-pagination-bar');
        
        if (singleDishSearchQuery === '') {
            singleDishPage = 1;
            renderSingleDishesPagination();
            return;
        }

        let visibleCount = 0;
        rows.forEach(r => {
            const name = r.getAttribute('data-dish-name') || '';
            if (name.includes(singleDishSearchQuery)) {
                r.style.display = '';
                visibleCount++;
            } else {
                r.style.display = 'none';
            }
        });

        if (paginationBar) {
            paginationBar.style.display = 'none';
        }
    }

    let currentModalDishOrders = [];
    let modalDishFilterText = '';

    function openDishOrdersModalFromBtn(btn) {
        const dishName = btn.getAttribute('data-dish-name') || 'Món ăn';
        const totalQty = btn.getAttribute('data-total-qty') || '0';
        let orders = [];
        try {
            orders = JSON.parse(btn.getAttribute('data-orders') || '[]');
        } catch (e) {
            orders = [];
        }

        currentModalDishOrders = orders;
        modalDishFilterText = '';

        const titleElem = document.getElementById('modal-dish-title');
        const qtyBadge = document.getElementById('modal-dish-qty-badge');
        const subtitleElem = document.getElementById('modal-dish-subtitle');
        const searchInput = document.getElementById('modal-dish-search');

        if (titleElem) titleElem.innerText = dishName;
        if (qtyBadge) qtyBadge.innerText = `${totalQty} suất`;
        if (subtitleElem) subtitleElem.innerText = `Tổng ${orders.length} đơn hàng chứa món ăn này`;
        if (searchInput) searchInput.value = '';

        renderModalDishOrders();

        const modal = document.getElementById('dishOrdersModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeDishOrdersModal() {
        const modal = document.getElementById('dishOrdersModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function filterDishOrdersInModal(query) {
        modalDishFilterText = (query || '').trim().toLowerCase();
        renderModalDishOrders();
    }

    function renderModalDishOrders() {
        const container = document.getElementById('modal-dish-orders-container');
        const footerCount = document.getElementById('modal-dish-footer-count');
        if (!container) return;

        const filtered = currentModalDishOrders.filter(ord => {
            if (!modalDishFilterText) return true;
            const haystack = `#${ord.tracking_code} ${ord.tracking_code} ${ord.order_id} ${ord.customer_name} ${ord.health_notes || ''}`.toLowerCase();
            return haystack.includes(modalDishFilterText);
        });

        if (footerCount) {
            footerCount.innerText = `Đang hiển thị ${filtered.length} / ${currentModalDishOrders.length} đơn hàng`;
        }

        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-xs text-slate-400">
                    <i class="fas fa-search text-2xl mb-2 text-slate-300 block"></i>
                    <p class="font-bold text-slate-600">Không tìm thấy đơn hàng nào khớp với từ khóa</p>
                </div>`;
            return;
        }

        let html = '';
        filtered.forEach(ord => {
            let statusBadge = '';
            if (ord.order_status === 'pending') {
                statusBadge = '<span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200">Chờ duyệt</span>';
            } else if (ord.order_status === 'confirmed') {
                statusBadge = '<span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">Đã xác nhận</span>';
            } else if (ord.order_status === 'preparing') {
                statusBadge = '<span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 text-[10px] font-bold border border-purple-200">Đang nấu</span>';
            } else {
                statusBadge = `<span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-bold border border-slate-200">${ord.order_status}</span>`;
            }

            const notesHtml = ord.health_notes ? `
                <div class="mt-1.5 text-[11px] text-amber-900 bg-amber-50 p-2 rounded-lg border border-amber-200 font-medium flex items-start gap-1.5">
                    <i class="fas fa-triangle-exclamation text-amber-500 mt-0.5 text-[10px] shrink-0"></i>
                    <span>${ord.health_notes}</span>
                </div>
            ` : '';

            html += `
                <div class="bg-white rounded-2xl p-3 border border-slate-200 hover:border-slate-300 shadow-2xs transition-all flex flex-col gap-1.5">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-xl bg-slate-900 text-white font-black text-xs tracking-wider flex items-center gap-1">
                                <i class="fas fa-barcode text-[10px] text-amber-400"></i>
                                #${ord.tracking_code}
                            </span>
                            ${statusBadge}
                            <span class="font-bold text-xs text-slate-800">
                                <i class="far fa-user text-slate-400 text-[11px] mr-1"></i>${ord.customer_name}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2.5 py-0.5 rounded-xl bg-rose-50 text-[#ee4d2d] font-black text-xs border border-rose-200">
                                ${ord.quantity} suất
                            </span>
                            <a href="{{ url('/donhang_xem') }}/${ord.order_id}" target="_blank"
                               class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-colors inline-flex items-center gap-1 shadow-2xs">
                                <i class="far fa-eye text-slate-500"></i> Xem đơn
                            </a>
                        </div>
                    </div>
                    ${notesHtml}
                </div>
            `;
        });

        container.innerHTML = html;
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDishOrdersModal();
        }
    });

    document.getElementById('dishOrdersModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDishOrdersModal();
        }
    });
</script>
@endsection
