@extends('layouts.app')

@section('title', 'Hồ Sơ Cá Nhân - FOODDAILY')

@section('content')
<div class="py-8 bg-transparent min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Navigation Breadcrumb -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 bg-white/90 backdrop-blur-md px-4 py-2 rounded-xl border border-slate-200/80 shadow-xs">
                <a href="{{ route('trangchu') }}" class="hover:text-[#ee4d2d]">Trang chủ</a>
                <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                <span class="text-[#ee4d2d] font-bold">Hồ sơ tài khoản</span>
            </div>
            <a href="{{ route('giohang') }}" class="text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 px-4 py-2 rounded-xl border border-slate-200 shadow-xs transition-all flex items-center gap-1.5">
                <i class="fas fa-receipt text-[#ee4d2d]"></i> Lịch sử đơn hàng
            </a>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs animate-in fade-in">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 shadow-xs animate-in fade-in">
                <div class="flex items-center gap-2 font-black text-rose-900">
                    <i class="fas fa-circle-exclamation"></i> Vui lòng kiểm tra lại thông tin:
                </div>
                <ul class="list-disc list-inside pl-2 space-y-0.5 text-[11px] font-semibold text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- TOP PROFILE HERO CARD -->
        <div class="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-gradient-to-br from-rose-100 to-amber-100 opacity-50 blur-2xl pointer-events-none"></div>

            <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-6 relative z-10">
                <!-- User Info Left -->
                <div class="flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
                    <div class="relative group">
                        <img id="header-avatar-preview" src="{{ $user->avatar_url }}" alt="{{ $user->fullname }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl object-cover shadow-md border-4 border-white ring-2 ring-rose-100">
                        <button type="button" onclick="switchProfileTab('info'); document.getElementById('avatar-file-input').click();" class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-[#ee4d2d] text-white flex items-center justify-center shadow-lg hover:scale-110 active:scale-95 transition-all text-xs cursor-pointer" title="Đổi ảnh đại diện">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <h1 class="text-xl sm:text-2xl font-black text-slate-900">{{ $user->fullname ?? 'Khách hàng' }}</h1>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black {{ $membershipDetails['bg'] }} border {{ $membershipDetails['border'] }}">
                                <i class="fas {{ $membershipDetails['icon'] }} text-[10px]"></i> {{ $membershipDetails['name'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-semibold flex items-center justify-center sm:justify-start gap-3">
                            <span><i class="far fa-envelope text-slate-400 mr-1"></i>{{ $user->email }}</span>
                            @if($user->phone)
                                <span><i class="fas fa-phone text-slate-400 mr-1"></i>{{ $user->phone }}</span>
                            @endif
                        </p>
                        <p class="text-[11px] text-slate-400 font-medium">
                            Thành viên từ: {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Hôm nay' }}
                        </p>
                    </div>
                </div>

                <!-- Right Quick Stats Pills -->
                <div class="grid grid-cols-3 gap-3 w-full md:w-auto">
                    <div class="p-3.5 bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/80 rounded-2xl text-center min-w-[90px] shadow-2xs">
                        <div class="text-[10px] font-extrabold uppercase text-amber-700 tracking-wider">Điểm Xu</div>
                        <div class="text-lg font-black text-[#ee4d2d] mt-0.5">{{ number_format($user->points ?? 0) }}</div>
                        <div class="text-[9px] text-amber-600 font-semibold">Tương đương {{ number_format(($user->points ?? 0) * 1000) }}đ</div>
                    </div>

                    <div class="p-3.5 bg-gradient-to-br from-rose-50 to-red-50 border border-rose-200/80 rounded-2xl text-center min-w-[90px] shadow-2xs">
                        <div class="text-[10px] font-extrabold uppercase text-rose-700 tracking-wider">Voucher</div>
                        <div class="text-lg font-black text-[#ee4d2d] mt-0.5">{{ $savedCoupons->count() }}</div>
                        <div class="text-[9px] text-rose-600 font-semibold">Mã khả dụng</div>
                    </div>

                    <div class="p-3.5 bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 rounded-2xl text-center min-w-[90px] shadow-2xs">
                        <div class="text-[10px] font-extrabold uppercase text-slate-600 tracking-wider">Đơn Hàng</div>
                        <div class="text-lg font-black text-slate-900 mt-0.5">{{ $ordersCount }}</div>
                        <div class="text-[9px] text-slate-500 font-semibold">{{ $completedOrdersCount }} thành công</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN TABBED INTERFACE (4 CHUYÊN MỤC CHÍNH) -->
        <div class="bg-white/95 backdrop-blur-md rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
            <!-- Navigation Tabs Bar -->
            <div class="flex items-center overflow-x-auto border-b border-slate-200/80 scrollbar-none bg-slate-50/50 p-2 gap-1.5" id="profile-tabs-nav">
                <button type="button" onclick="switchProfileTab('info')" id="tab-btn-info" class="profile-tab-btn flex-1 min-w-[140px] sm:min-w-[160px] py-3 px-4 rounded-2xl font-extrabold text-xs flex items-center justify-center gap-2 transition-all cursor-pointer {{ $tab === 'info' ? 'bg-white text-[#ee4d2d] shadow-sm border border-slate-200/70' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    <i class="fas fa-user-circle text-sm"></i>
                    <span>Thông tin cá nhân</span>
                </button>

                <button type="button" onclick="switchProfileTab('address')" id="tab-btn-address" class="profile-tab-btn flex-1 min-w-[140px] sm:min-w-[160px] py-3 px-4 rounded-2xl font-extrabold text-xs flex items-center justify-center gap-2 transition-all cursor-pointer {{ $tab === 'address' ? 'bg-white text-[#ee4d2d] shadow-sm border border-slate-200/70' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    <i class="fas fa-map-location-dot text-sm"></i>
                    <span>Địa chỉ giao hàng</span>
                </button>

                <button type="button" onclick="switchProfileTab('account')" id="tab-btn-account" class="profile-tab-btn flex-1 min-w-[140px] sm:min-w-[160px] py-3 px-4 rounded-2xl font-extrabold text-xs flex items-center justify-center gap-2 transition-all cursor-pointer {{ $tab === 'account' ? 'bg-white text-[#ee4d2d] shadow-sm border border-slate-200/70' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    <i class="fas fa-shield-halved text-sm"></i>
                    <span>Tài khoản & Bảo mật</span>
                </button>

                <button type="button" onclick="switchProfileTab('member')" id="tab-btn-member" class="profile-tab-btn flex-1 min-w-[140px] sm:min-w-[160px] py-3 px-4 rounded-2xl font-extrabold text-xs flex items-center justify-center gap-2 transition-all cursor-pointer {{ $tab === 'member' ? 'bg-white text-[#ee4d2d] shadow-sm border border-slate-200/70' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    <i class="fas fa-crown text-sm text-amber-500"></i>
                    <span>Thành viên & Voucher</span>
                </button>
            </div>

            <!-- TAB CONTENT CONTAINER -->
            <div class="p-6 sm:p-8">

                <!-- ========================================== -->
                <!-- 1. TAB: THÔNG TIN CÁ NHÂN -->
                <!-- ========================================== -->
                <div id="tab-content-info" class="profile-tab-pane {{ $tab === 'info' ? 'block' : 'hidden' }} space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Hồ Sơ & Thông Tin Cá Nhân</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Quản lý họ tên, số điện thoại, ngày sinh và ảnh đại diện</p>
                        </div>
                    </div>

                    <form action="{{ route('hoso.update_info') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Ảnh đại diện upload & preview -->
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 flex flex-col sm:flex-row items-center gap-6">
                            <div class="relative">
                                <img id="info-avatar-preview" src="{{ $user->avatar_url }}" alt="Preview" class="w-20 h-20 rounded-2xl object-cover border-2 border-white shadow-sm ring-2 ring-slate-200">
                            </div>
                            <div class="space-y-2 text-center sm:text-left flex-1">
                                <label class="block text-xs font-black text-slate-800">Ảnh đại diện (Avatar)</label>
                                <p class="text-[11px] text-slate-500">Hỗ trợ định dạng JPG, PNG, WEBP. Dung lượng tối đa 5MB.</p>
                                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                                    <input type="file" name="avatar_file" id="avatar-file-input" accept="image/*" class="hidden" onchange="previewAvatarImage(this)">
                                    <button type="button" onclick="document.getElementById('avatar-file-input').click()" class="px-3.5 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer flex items-center gap-1.5">
                                        <i class="fas fa-upload text-slate-500"></i> Tải ảnh từ thiết bị
                                    </button>
                                    <button type="button" onclick="useDefaultAvatar()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors cursor-pointer">
                                        Ảnh mặc định
                                    </button>
                                    <input type="hidden" name="avatar_url" id="avatar_url_input" value="{{ $user->avatar }}">
                                </div>
                            </div>
                        </div>

                        <!-- Grid form fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Họ tên -->
                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    Họ và tên <span class="text-[#ee4d2d]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}" required placeholder="Nguyễn Văn A" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-xs">
                                </div>
                            </div>

                            <!-- Email (Readonly) -->
                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    Email tài khoản
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" value="{{ $user->email }}" readonly class="w-full pl-9 pr-24 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-xs font-bold text-slate-500 cursor-not-allowed">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[10px] font-black text-emerald-600">
                                        <i class="fas fa-circle-check mr-1"></i> Đã kích hoạt
                                    </span>
                                </div>
                            </div>

                            <!-- Số điện thoại -->
                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    Số điện thoại liên hệ
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0901234567" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-xs">
                                </div>
                            </div>

                            <!-- Ngày sinh -->
                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    Ngày sinh
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                        <i class="fas fa-cake-candles"></i>
                                    </span>
                                    <input type="date" name="birthday" value="{{ old('birthday', $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-xs">
                                </div>
                            </div>
                        </div>

                        <!-- Giới tính -->
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">
                                Giới tính
                            </label>
                            <div class="grid grid-cols-3 gap-3 max-w-md">
                                <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer text-xs font-bold transition-all has-checked:border-[#ee4d2d] has-checked:bg-rose-50/50 has-checked:text-[#ee4d2d]">
                                    <input type="radio" name="gender" value="male" {{ old('gender', $user->gender) === 'male' ? 'checked' : '' }} class="accent-[#ee4d2d]">
                                    <span>👨 Nam</span>
                                </label>

                                <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer text-xs font-bold transition-all has-checked:border-[#ee4d2d] has-checked:bg-rose-50/50 has-checked:text-[#ee4d2d]">
                                    <input type="radio" name="gender" value="female" {{ old('gender', $user->gender) === 'female' ? 'checked' : '' }} class="accent-[#ee4d2d]">
                                    <span>👩 Nữ</span>
                                </label>

                                <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 cursor-pointer text-xs font-bold transition-all has-checked:border-[#ee4d2d] has-checked:bg-rose-50/50 has-checked:text-[#ee4d2d]">
                                    <input type="radio" name="gender" value="other" {{ old('gender', $user->gender) === 'other' ? 'checked' : '' }} class="accent-[#ee4d2d]">
                                    <span>✨ Khác</span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-xl text-xs font-black shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                                <i class="fas fa-check"></i> Lưu Thay Đổi
                            </button>
                        </div>
                    </form>
                </div>


                <!-- ========================================== -->
                <!-- 2. TAB: ĐỊA CHỈ GIAO HÀNG -->
                <!-- ========================================== -->
                <div id="tab-content-address" class="profile-tab-pane {{ $tab === 'address' ? 'block' : 'hidden' }} space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Sổ Địa Chỉ Giao Hàng</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Lưu trước địa chỉ để tự động điền nhanh chóng khi thanh toán</p>
                        </div>
                    </div>

                    <!-- Banner nhắc nhở tự động điền thanh toán -->
                    <div class="p-3.5 rounded-2xl bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 text-orange-950 text-xs flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-orange-500 text-white flex items-center justify-center font-bold text-sm shrink-0">
                            <i class="fas fa-bolt"></i>
                        </span>
                        <div>
                            <span class="font-black block">Tự Động Điền Khi Đặt Món:</span>
                            <span class="text-[11px] text-orange-800">Khi bạn đặt hàng, hệ thống sẽ <strong>tự động lấy địa chỉ mặc định</strong> bạn đã chọn ở đây. Bạn cũng có thể bấm nút chuyển đổi giữa Nhà, Công ty và Khác chỉ bằng 1 chạm!</span>
                        </div>
                    </div>

                    <form action="{{ route('hoso.update_address') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 gap-5">

                            <!-- Slot 1: Nhà (Home Address) -->
                            <div class="p-5 rounded-2xl border-2 {{ ($user->default_address_type ?? 'home') === 'home' ? 'border-[#ee4d2d] bg-rose-50/20' : 'border-slate-200 bg-white' }} space-y-3 transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 rounded-xl bg-rose-100 text-[#ee4d2d] flex items-center justify-center text-sm font-bold">
                                            🏠
                                        </span>
                                        <div>
                                            <h3 class="text-xs font-black text-slate-900 uppercase">1. Địa Chỉ Nhà Riêng</h3>
                                            <p class="text-[10px] text-slate-400">Nhà ở, căn hộ, phòng trọ...</p>
                                        </div>
                                    </div>
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 cursor-pointer">
                                        <input type="radio" name="default_address_type" value="home" {{ ($user->default_address_type ?? 'home') === 'home' ? 'checked' : '' }} class="accent-[#ee4d2d]">
                                        <span>Đặt làm mặc định</span>
                                    </label>
                                </div>
                                <textarea name="home_address" rows="2" placeholder="Số nhà, ngõ/hẻm, tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-2xs resize-none">{{ old('home_address', $user->address) }}</textarea>
                            </div>

                            <!-- Slot 2: Công ty (Company Address) -->
                            <div class="p-5 rounded-2xl border-2 {{ ($user->default_address_type ?? 'home') === 'company' ? 'border-[#ee4d2d] bg-rose-50/20' : 'border-slate-200 bg-white' }} space-y-3 transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">
                                            🏢
                                        </span>
                                        <div>
                                            <h3 class="text-xs font-black text-slate-900 uppercase">2. Địa Chỉ Công Ty / Nơi Làm Việc</h3>
                                            <p class="text-[10px] text-slate-400">Tòa nhà, số tầng, phòng làm việc...</p>
                                        </div>
                                    </div>
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 cursor-pointer">
                                        <input type="radio" name="default_address_type" value="company" {{ ($user->default_address_type ?? 'home') === 'company' ? 'checked' : '' }} class="accent-[#ee4d2d]">
                                        <span>Đặt làm mặc định</span>
                                    </label>
                                </div>
                                <textarea name="company_address" rows="2" placeholder="Tên tòa nhà, số tầng/phòng, tên công ty, đường, Phường/Quận..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-2xs resize-none">{{ old('company_address', $user->company_address) }}</textarea>
                            </div>

                            <!-- Slot 3: Địa chỉ khác (Other Address) -->
                            <div class="p-5 rounded-2xl border-2 {{ ($user->default_address_type ?? 'home') === 'other' ? 'border-[#ee4d2d] bg-rose-50/20' : 'border-slate-200 bg-white' }} space-y-3 transition-all">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-bold">
                                            📍
                                        </span>
                                        <div>
                                            <h3 class="text-xs font-black text-slate-900 uppercase">3. Địa Chỉ Khác</h3>
                                            <p class="text-[10px] text-slate-400">Ký túc xá, trường học, nhà người thân...</p>
                                        </div>
                                    </div>
                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 cursor-pointer">
                                        <input type="radio" name="default_address_type" value="other" {{ ($user->default_address_type ?? 'home') === 'other' ? 'checked' : '' }} class="accent-[#ee4d2d]">
                                        <span>Đặt làm mặc định</span>
                                    </label>
                                </div>
                                <textarea name="other_address" rows="2" placeholder="Ghi chú địa điểm, số nhà, tên đường chi tiết..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-2xs resize-none">{{ old('other_address', $user->other_address) }}</textarea>
                            </div>

                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-xl text-xs font-black shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                                <i class="fas fa-floppy-disk"></i> Lưu Sổ Địa Chỉ
                            </button>
                        </div>
                    </form>
                </div>


                <!-- ========================================== -->
                <!-- 3. TAB: TÀI KHOẢN & BẢO MẬT -->
                <!-- ========================================== -->
                <div id="tab-content-account" class="profile-tab-pane {{ $tab === 'account' ? 'block' : 'hidden' }} space-y-8">
                    
                    <!-- Phần A: Đổi mật khẩu -->
                    <div class="space-y-4">
                        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-key text-[#ee4d2d]"></i> Đổi Mật Khẩu
                                </h2>
                                <p class="text-xs text-slate-400 mt-0.5">Để bảo mật tài khoản, không nên chia sẻ mật khẩu cho người khác</p>
                            </div>
                        </div>

                        <form action="{{ route('hoso.update_password') }}" method="POST" class="max-w-lg space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    Mật khẩu hiện tại <span class="text-[#ee4d2d]">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="current_password" required placeholder="Nhập mật khẩu hiện tại" class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-xs">
                                    <button type="button" onclick="togglePasswordVisibility('current_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    Mật khẩu mới <span class="text-[#ee4d2d]">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="password" id="new_password" required placeholder="Tối thiểu 6 ký tự" class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-xs">
                                    <button type="button" onclick="togglePasswordVisibility('new_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                                    Xác nhận mật khẩu mới <span class="text-[#ee4d2d]">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Nhập lại mật khẩu mới" class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-xs">
                                    <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 text-xs">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-black shadow-md transition-all flex items-center gap-2 cursor-pointer">
                                <i class="fas fa-lock"></i> Cập Nhật Mật Khẩu
                            </button>
                        </form>
                    </div>

                    <!-- Phần B: Cài đặt Bảo mật nâng cao -->
                    <div class="pt-6 border-t border-slate-100 space-y-4">
                        <div class="pb-3 border-b border-slate-100">
                            <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                <i class="fas fa-shield-cat text-emerald-600"></i> Thiết Lập Bảo Mật
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5">Kiểm soát an toàn tài khoản và trạng thái bảo vệ</p>
                        </div>

                        <form action="{{ route('hoso.update_security') }}" method="POST" class="space-y-4">
                            @csrf

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-base shrink-0 font-bold">
                                        🛡️
                                    </span>
                                    <div>
                                        <h4 class="text-xs font-black text-slate-900">Bảo mật xác thực tài khoản 2 lớp</h4>
                                        <p class="text-[11px] text-slate-500">Bảo vệ tài khoản khi đăng nhập trên thiết bị mới hoặc lạ</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="two_factor_enabled" value="1" {{ $user->two_factor_enabled ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                </label>
                            </div>
                        </form>

                        <!-- Lịch sử đăng nhập & thiết bị -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                                <span><i class="fas fa-desktop text-slate-400 mr-1.5"></i>Phiên hoạt động hiện tại</span>
                                <span class="text-emerald-600 font-black"><i class="fas fa-circle text-[8px] mr-1"></i>Đang trực tuyến</span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-medium">
                                Đăng nhập gần nhất: {{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('H:i d/m/Y') : 'Vừa xong' }}
                            </p>
                        </div>
                    </div>
                </div>


                <!-- ========================================== -->
                <!-- 4. TAB: THÀNH VIÊN & VOUCHER -->
                <!-- ========================================== -->
                <div id="tab-content-member" class="profile-tab-pane {{ $tab === 'member' ? 'block' : 'hidden' }} space-y-8">
                    
                    <!-- Phần A: Điểm tích lũy & Hạng thành viên -->
                    <div class="space-y-4">
                        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-crown text-amber-500"></i> Hạng Thành Viên & Điểm Thưởng
                                </h2>
                                <p class="text-xs text-slate-400 mt-0.5">Tích lũy điểm khi đặt món để nhận ưu đãi giảm giá và Freeship</p>
                            </div>
                        </div>

                        <!-- Card thẻ thành viên VIP -->
                        <div class="rounded-3xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                            <div class="absolute -right-10 -bottom-10 text-white/5 text-9xl font-black pointer-events-none">
                                <i class="fas {{ $membershipDetails['icon'] }}"></i>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-amber-400">FOODDAILY REWARDS</span>
                                    <h3 class="text-2xl font-black mt-1 flex items-center gap-2">
                                        {{ $membershipDetails['name'] }}
                                        <i class="fas {{ $membershipDetails['icon'] }} text-amber-400 text-lg"></i>
                                    </h3>
                                    <p class="text-xs text-slate-300 mt-1">Chủ thẻ: <strong>{{ $user->fullname }}</strong></p>
                                </div>
                                <div class="text-right sm:text-right bg-white/10 backdrop-blur-md px-5 py-3 rounded-2xl border border-white/10">
                                    <div class="text-[10px] uppercase font-bold text-slate-300">Điểm Xu Hiện Có</div>
                                    <div class="text-3xl font-black text-amber-300">{{ number_format($user->points ?? 0) }}</div>
                                    <div class="text-[10px] text-slate-300">1.000đ = 1 điểm tích lũy</div>
                                </div>
                            </div>

                            <!-- Progress to next tier -->
                            <div class="mt-6 pt-5 border-t border-white/10 relative z-10 space-y-2">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span>Tiến trình thăng hạng</span>
                                    <span>
                                        @if($membershipDetails['next_tier'])
                                            Cần thêm <strong>{{ number_format($membershipDetails['needed_points']) }}</strong> điểm để lên <strong>Hạng {{ $membershipDetails['next_tier'] }}</strong>
                                        @else
                                            <span class="text-amber-300">🎉 Bạn đã đạt cấp độ Thành Viên Cao Nhất!</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="w-full h-3 bg-white/20 rounded-full overflow-hidden p-0.5">
                                    <div class="h-full bg-gradient-to-r from-amber-400 to-[#ee4d2d] rounded-full transition-all duration-500" style="width: {{ $membershipDetails['progress'] }}%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Danh sách quyền lợi của hạng hiện tại -->
                        <div class="p-5 rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-2">
                            <h4 class="text-xs font-black text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-gift text-[#ee4d2d]"></i> Đặc Quyền Dành Riêng Cho Bạn:
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-semibold text-amber-950">
                                @foreach($membershipDetails['benefits'] as $benefit)
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-emerald-500 text-[11px]"></i>
                                        <span>{{ $benefit }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Phần B: Kho Voucher Của Bạn -->
                    <div class="pt-6 border-t border-slate-100 space-y-4">
                        <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-ticket-alt text-[#ee4d2d]"></i> Ví Voucher Khả Dụng ({{ $savedCoupons->count() }})
                                </h2>
                                <p class="text-xs text-slate-400 mt-0.5">Mã giảm giá đã lưu sẵn trong tài khoản, áp dụng tự động khi đặt món</p>
                            </div>
                            <button type="button" onclick="openGlobalUserVoucherModal('all')" class="text-xs font-bold text-[#ee4d2d] hover:underline flex items-center gap-1">
                                <i class="fas fa-plus"></i> Khám phá thêm mã
                            </button>
                        </div>

                        @if($savedCoupons->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($savedCoupons as $coupon)
                                    <div class="p-4 rounded-2xl bg-white border border-rose-200/80 shadow-2xs hover:shadow-md transition-all flex items-center justify-between gap-3 relative overflow-hidden group">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-[#ee4d2d] to-amber-500 text-white flex flex-col items-center justify-center shrink-0 font-black shadow-xs">
                                                <span class="text-xs leading-none">{{ $coupon->discount_type === 'percent' ? $coupon->discount_value . '%' : number_format($coupon->discount_value/1000) . 'K' }}</span>
                                                <span class="text-[8px] uppercase tracking-tighter opacity-80">GIẢM</span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">{{ $coupon->coupon_code }}</span>
                                                    <span class="px-1.5 py-0.2 bg-rose-100 text-[#ee4d2d] text-[9px] font-black rounded-md">Voucher</span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $coupon->description ?? 'Áp dụng cho mọi đơn hàng' }}</p>
                                                <p class="text-[10px] text-slate-400 mt-0.5">
                                                    Đơn tối thiểu: <strong>{{ number_format($coupon->min_order_amount ?? 0) }}đ</strong>
                                                </p>
                                            </div>
                                        </div>
                                        <a href="{{ route('muahang') }}" class="px-3 py-1.5 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-xl text-xs font-black transition-colors shrink-0 shadow-2xs">
                                            Dùng ngay
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200/70 text-center space-y-3">
                                <div class="text-3xl">🎟️</div>
                                <h4 class="text-xs font-bold text-slate-700">Bạn chưa lưu mã giảm giá nào</h4>
                                <p class="text-[11px] text-slate-400 max-w-sm mx-auto">Nhận ngay các mã ưu đãi giảm 10% - 30% tại Kho Ưu Đãi để tiết kiệm chi phí cho bữa ăn tiếp theo!</p>
                                <button type="button" onclick="openGlobalUserVoucherModal('all')" class="px-4 py-2 bg-[#ee4d2d] text-white rounded-xl text-xs font-black shadow-xs hover:bg-red-600 transition-colors cursor-pointer">
                                    Xem Kho Ưu Đãi Ngay
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
    // Chuyển đổi tab
    function switchProfileTab(tabName) {
        // Cập nhật URL mà không reload trang
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);

        // Ẩn tất cả nội dung tab
        document.querySelectorAll('.profile-tab-pane').forEach(el => el.classList.add('hidden'));
        
        // Hiện tab được chọn
        const activePane = document.getElementById('tab-content-' + tabName);
        if (activePane) {
            activePane.classList.remove('hidden');
        }

        // Cập nhật kiểu dáng nút tab
        document.querySelectorAll('.profile-tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-[#ee4d2d]', 'shadow-sm', 'border', 'border-slate-200/70');
            btn.classList.add('text-slate-600', 'hover:text-slate-900', 'hover:bg-white/60');
        });

        const activeBtn = document.getElementById('tab-btn-' + tabName);
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-600', 'hover:text-slate-900', 'hover:bg-white/60');
            activeBtn.classList.add('bg-white', 'text-[#ee4d2d]', 'shadow-sm', 'border', 'border-slate-200/70');
        }
    }

    // Xem trước ảnh đại diện khi chọn file
    function previewAvatarImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const headerPreview = document.getElementById('header-avatar-preview');
                const infoPreview = document.getElementById('info-avatar-preview');
                if (headerPreview) headerPreview.src = e.target.result;
                if (infoPreview) infoPreview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Khôi phục avatar về mặc định
    function useDefaultAvatar() {
        const defaultUrl = "https://ui-avatars.com/api/?name={{ urlencode($user->fullname ?: 'Khách hàng') }}&background=ee4d2d&color=ffffff&bold=true";
        document.getElementById('avatar_url_input').value = defaultUrl;
        document.getElementById('header-avatar-preview').src = defaultUrl;
        document.getElementById('info-avatar-preview').src = defaultUrl;
        const fileInput = document.getElementById('avatar-file-input');
        if (fileInput) fileInput.value = '';
    }

    // Hiện / ẩn mật khẩu
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        if (field.type === 'password') {
            field.type = 'text';
        } else {
            field.type = 'password';
        }
    }

    // Kiểm tra query parameter lúc tải trang để mở tab tương ứng
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab && ['info', 'address', 'account', 'member'].includes(tab)) {
            switchProfileTab(tab);
        }
    });
</script>
@endsection
