@extends('layouts.app')

@section('title', 'Phòng Đặt Đơn Theo Nhóm #' . $groupOrder->code . ' - FOODDAILY')

@section('content')
<div class="py-12 bg-slate-50 min-h-[calc(100vh-160px)]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Top Back Link & Status Badge -->
        <div class="flex items-center justify-between">
            <a href="{{ route('trangchu') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                <i class="fas fa-arrow-left"></i> Quay lại thực đơn
            </a>
            <span class="px-3.5 py-1.5 rounded-full {{ $groupOrder->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }} text-xs font-extrabold flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $groupOrder->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                {{ $groupOrder->status === 'active' ? 'Phòng nhóm đang mở' : 'Đã chốt đơn' }}
            </span>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-500 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <i class="fas fa-circle-exclamation text-rose-500 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Group Header Card (QR Code & Link Share) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-md border border-slate-200/80 grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
            <div class="md:col-span-2 space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold">
                    <i class="fas fa-users text-emerald-500"></i> Đặt đơn nhóm tiện lợi
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Mã Nhóm: <span class="text-emerald-600">#{{ $groupOrder->code }}</span>
                </h1>
                <p class="text-xs text-slate-500 font-medium">
                    Trưởng nhóm: <strong class="text-slate-800">{{ $groupOrder->host_name }}</strong> • Mời bạn bè quét mã QR hoặc gửi link dưới đây để mỗi người tự chọn món!
                </p>

                <!-- Copy Link Bar -->
                <div class="flex items-center gap-2 pt-2">
                    <input type="text" id="shareUrl" value="{{ request()->url() }}" class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-mono text-slate-600 outline-none" readonly>
                    <button type="button" onclick="copyShareUrl()" class="px-5 py-2.5 rounded-2xl bg-slate-900 hover:bg-emerald-600 text-white font-bold text-xs shrink-0 transition-all shadow-xs">
                        <i class="fas fa-copy mr-1"></i> Sao chép link
                    </button>
                </div>
            </div>

            <!-- QR Code -->
            <div class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                @php
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode(request()->url());
                @endphp
                <img src="{{ $qrUrl }}" alt="Mã QR Nhóm" class="w-32 h-32 rounded-xl shadow-xs border border-white">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Quét mã QR nhóm</span>
            </div>
        </div>

        <!-- Add Dish Form Section -->
        @if($groupOrder->status === 'active')
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-md border border-slate-200/80 space-y-6">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
                    <i class="fas fa-plus-circle text-emerald-600"></i> Thành viên chọn món vào nhóm
                </h3>

                <form action="{{ route('nhom.add_item', ['code' => $groupOrder->code]) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    @csrf
                    <!-- Tên thành viên -->
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tên bạn <span class="text-emerald-600">*</span></label>
                        <input type="text" name="member_name" value="{{ auth()->check() ? (auth()->user()->fullname ?? auth()->user()->name) : old('member_name') }}" placeholder="Nhập tên bạn..." class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all" required>
                    </div>

                    <!-- Chọn món -->
                    <div class="sm:col-span-5">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Chọn món ăn <span class="text-emerald-600">*</span></label>
                        <select name="dish_id" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-white" required>
                            <option value="">-- Chọn món từ thực đơn --</option>
                            @foreach($dishes as $dish)
                                <option value="{{ $dish->id }}">{{ $dish->dish_name }} - {{ number_format($dish->price) }}đ</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Số lượng -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Số lượng</label>
                        <input type="number" name="quantity" value="1" min="1" max="20" class="w-full px-3 py-2.5 text-center rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-800 focus:border-emerald-500 outline-none">
                    </div>

                    <!-- Nút thêm -->
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-500/20 transition-all">
                            + Thêm món
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Live Group Items List & Checkout -->
        <div class="bg-white rounded-3xl shadow-md border border-slate-200/80 overflow-hidden space-y-4">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-list-ul text-emerald-600"></i> Danh sách món nhóm đã chọn (<span id="itemCountDisplay">{{ count($groupOrder->items) }}</span>)
                </h3>
                <span class="text-xs font-medium text-slate-400">Tự động cập nhật thời gian thực 🔄</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                            <th class="py-4 px-6">Thành viên</th>
                            <th class="py-4 px-6">Món ăn</th>
                            <th class="py-4 px-6 text-center">Số lượng</th>
                            <th class="py-4 px-6">Đơn giá</th>
                            <th class="py-4 px-6">Thành tiền</th>
                            @if($groupOrder->status === 'active')
                                <th class="py-4 px-6 text-right">Xóa</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="groupItemsTbody">
                        @php $groupTotal = 0; @endphp
                        @forelse($groupOrder->items as $item)
                            @php 
                                $subtotal = ($item->dish->price ?? 0) * $item->quantity; 
                                $groupTotal += $subtotal;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6">
                                    <span class="font-extrabold text-slate-900 bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-xs border border-emerald-100">
                                        <i class="fas fa-user text-[10px] mr-1"></i>{{ $item->member_name }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800">
                                    {{ $item->dish->dish_name ?? 'Món ăn' }}
                                </td>
                                <td class="py-4 px-6 text-center font-extrabold text-slate-900">
                                    x{{ $item->quantity }}
                                </td>
                                <td class="py-4 px-6 text-slate-500 font-semibold">
                                    {{ number_format($item->dish->price ?? 0) }}đ
                                </td>
                                <td class="py-4 px-6 font-extrabold text-slate-900">
                                    {{ number_format($subtotal) }}đ
                                </td>
                                @if($groupOrder->status === 'active')
                                    <td class="py-4 px-6 text-right">
                                        <form action="{{ route('nhom.remove_item', ['code' => $groupOrder->code, 'itemId' => $item->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-bold transition-colors">
                                                <i class="fas fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400 font-medium">
                                    Chưa có thành viên nào chọn món. Hãy chia sẻ link/mã QR để bắt đầu!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Group Total & Host Checkout Action -->
            <div class="p-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <span class="text-xs font-bold text-slate-500 block uppercase tracking-wider">Tổng tiền đơn nhóm</span>
                    <span class="text-2xl font-extrabold text-slate-900" id="groupTotalDisplay">{{ number_format($groupTotal) }}đ</span>
                </div>

                @if($groupOrder->status === 'active')
                    <form action="{{ route('nhom.checkout', ['code' => $groupOrder->code]) }}" method="POST" class="flex items-center gap-3">
                        @csrf
                        <select name="payment_method" class="px-3.5 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-700 bg-white outline-none">
                            <option value="cash">Tiền mặt (COD)</option>
                            <option value="bank_transfer">Chuyển khoản VietQR</option>
                            <option value="momo">Ví MoMo</option>
                        </select>
                        <button type="submit" onclick="return confirm('Bạn có chắc muốn chốt đơn nhóm này và gửi xuống bếp?');" class="px-6 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 transition-all">
                            <i class="fas fa-check-circle mr-1.5"></i> CHỐT ĐƠN NHÓM & THANH TOÁN
                        </button>
                    </form>
                @else
                    <span class="px-4 py-2.5 rounded-2xl bg-slate-200 text-slate-600 font-extrabold text-xs">
                        Đơn nhóm đã hoàn tất chốt đơn
                    </span>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyShareUrl() {
        const input = document.getElementById('shareUrl');
        input.select();
        navigator.clipboard.writeText(input.value);
        alert('Đã sao chép đường dẫn chia sẻ đơn nhóm!');
    }

    // Realtime polling for group items every 3 seconds
    setInterval(function() {
        fetch("{{ route('nhom.poll', ['code' => $groupOrder->code]) }}")
            .then(res => res.json())
            .then(data => {
                if(data.items) {
                    document.getElementById('itemCountDisplay').innerText = data.items.length;
                    document.getElementById('groupTotalDisplay').innerText = new Intl.NumberFormat('vi-VN').format(data.total_price) + 'đ';
                }
            })
            .catch(err => console.log(err));
    }, 3000);
</script>
@endsection
