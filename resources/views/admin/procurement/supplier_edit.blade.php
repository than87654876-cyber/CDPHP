@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Nhà Cung Cấp - ' . $supplier->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_nhacungcap') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-2xs">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Chỉnh Sửa Nhà Cung Cấp: {{ $supplier->name }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Mã đối tác: <span class="font-mono font-bold text-slate-800">{{ $supplier->supplier_code }}</span></p>
            </div>
        </div>

        <form method="POST" action="{{ route('nhacungcap_xoa', $supplier->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này?')">
            @csrf
            <button type="submit" class="px-3.5 py-2 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-bold transition-colors flex items-center gap-1.5">
                <i class="fas fa-trash-can"></i> Xóa NCC
            </button>
        </form>
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
        <form method="POST" action="{{ route('nhacungcap_chinhsua.post', $supplier->id) }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Mã Nhà cung cấp -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mã Nhà Cung Cấp *</label>
                    <input type="text" name="supplier_code" value="{{ old('supplier_code', $supplier->supplier_code) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>

                <!-- Tên Nhà cung cấp -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tên Công Ty / Đơn Vị Cung Cấp *</label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>

                <!-- Hotline / Số điện thoại đặt hàng -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Hotline / SĐT Đặt Hàng *</label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-[#ee4d2d] focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                </div>

                <!-- Người liên hệ / Phụ trách -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Người Phụ Trách / Kinh Doanh</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Đặt Hàng</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>

                <!-- Trạng thái hợp tác -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Trạng Thái Hợp Tác *</label>
                    <select name="status" class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                        <option value="active" {{ old('status', $supplier->status) == 'active' ? 'selected' : '' }}>🟢 Đang hợp tác (Active)</option>
                        <option value="inactive" {{ old('status', $supplier->status) == 'inactive' ? 'selected' : '' }}>⚪ Tạm ngưng hợp tác (Inactive)</option>
                    </select>
                </div>

                <!-- Ngành hàng cung cấp chính -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ngành Hàng Cung Cấp Chính</label>
                    <input type="text" name="supply_category" value="{{ old('supply_category', $supplier->supply_category) }}"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>

                <!-- Địa chỉ kho / Công ty -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Địa Chỉ Giao Nhận Hàng</label>
                    <input type="text" name="address" value="{{ old('address', $supplier->address) }}"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>
            </div>

            <!-- Ghi chú / Quy định giao nhận -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Quy Định Đặt Hàng & Giao Nhận</label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">{{ old('notes', $supplier->notes) }}</textarea>
            </div>

            <!-- Nút Lưu -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('quanly_nhacungcap') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-black shadow-lg shadow-amber-500/30 transition-all flex items-center gap-2">
                    <i class="fas fa-check"></i> Cập Nhật Nhà Cung Cấp
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
