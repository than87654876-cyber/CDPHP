@extends('layouts.admin')

@section('title', 'Thêm Nhà Cung Cấp Mới - FOODDAILY Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_nhacungcap') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-2xs">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Khai Báo Nhà Cung Cấp / Đối Tác Mới</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Lưu thông tin liên hệ và mặt hàng để nhân viên dễ dàng liên hệ đặt hàng</p>
            </div>
        </div>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-semibold space-y-1">
            <div class="flex items-center gap-2 font-bold">
                <i class="fas fa-triangle-exclamation text-rose-600"></i>
                <span>Có lỗi xảy ra, vui lòng kiểm tra lại:</span>
            </div>
            <ul class="list-disc list-inside text-rose-700 pl-4 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-xs">
        <form method="POST" action="{{ route('nhacungcap_them.post') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Mã Nhà cung cấp -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mã Nhà Cung Cấp *</label>
                    <input type="text" name="supplier_code" value="{{ old('supplier_code', 'NCC-' . strtoupper(Str::random(5))) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                    <span class="text-[10px] text-slate-400 mt-1 block">Ví dụ: NCC-VISSAN, NCC-CPFOOD...</span>
                </div>

                <!-- Tên Nhà cung cấp -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tên Công Ty / Đơn Vị Cung Cấp *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Công ty Cổ phần Thực phẩm CP Việt Nam" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>

                <!-- Hotline / Số điện thoại đặt hàng -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Hotline / SĐT Đặt Hàng *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Số điện thoại hoặc Zalo để nhân viên gọi nhập hàng" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-[#ee4d2d] focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                </div>

                <!-- Người liên hệ / Phụ trách -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Người Phụ Trách / Kinh Doanh</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}" placeholder="Ví dụ: Anh Tuấn (Sales F&B)"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Đặt Hàng</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="contact@supplier.com"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>

                <!-- Ngành hàng cung cấp chính -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ngành Hàng Cung Cấp Chính</label>
                    <input type="text" name="supply_category" value="{{ old('supply_category') }}" placeholder="Ví dụ: Thịt tươi sống, Rau củ quả, Nước ngọt lon..."
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>

                <!-- Địa chỉ kho / Công ty -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Địa Chỉ Giao Nhận Hàng</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Địa chỉ kho hàng hoặc trụ sở của đơn vị đối tác"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>
            </div>

            <!-- Ghi chú / Quy định giao nhận -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Quy Định Đặt Hàng & Giao Nhận</label>
                <textarea name="notes" rows="3" placeholder="Ví dụ: Đặt trước 20h tối để giao lúc 5h30 sáng hôm sau. Thanh toán công nợ theo tuần..."
                          class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">{{ old('notes') }}</textarea>
            </div>

            <!-- Nút Lưu -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('quanly_nhacungcap') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-black shadow-lg shadow-amber-500/30 transition-all flex items-center gap-2">
                    <i class="fas fa-check"></i> Lưu Nhà Cung Cấp
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
