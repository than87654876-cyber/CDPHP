@extends('layouts.admin')

@section('title', 'Tạo Phiếu Nhập Hàng - FOODDAILY Admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Header Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_nhaphang') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-2xs">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Tạo Phiếu Nhập Hàng Mới Từ Đơn Vị Cung Cấp</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Nghiệm thu hàng thực tế, cập nhật số lượng nhập và tự động cộng dồn tồn kho</p>
            </div>
        </div>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-semibold space-y-1">
            <div class="flex items-center gap-2 font-bold">
                <i class="fas fa-triangle-exclamation text-rose-600"></i>
                <span>Vui lòng kiểm tra lại thông tin:</span>
            </div>
            <ul class="list-disc list-inside text-rose-700 pl-4 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('nhaphang_them.post') }}" id="receiptForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Cột Trái & Giữa: Thông tin phiếu & Bảng danh sách hàng nhập (Chiếm 2 cột) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Block 1: Thông tin chung về đợt nhập -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs space-y-5">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fas fa-info-circle text-[#ee4d2d]"></i> 1. Thông Tin Đợt Nhập Hàng
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Chọn Nhà cung cấp -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Đơn Vị Cung Cấp (Bên Giao Hàng) *</label>
                            <select name="supplier_id" id="supplierSelect" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                                <option value="">-- Chọn đơn vị cung cấp đối tác --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" 
                                            data-phone="{{ $sup->phone }}" 
                                            data-contact="{{ $sup->contact_person }}" 
                                            data-address="{{ $sup->address }}"
                                            {{ (old('supplier_id', request('supplier_id')) == $sup->id) ? 'selected' : '' }}>
                                        {{ $sup->name }} ({{ $sup->supplier_code }})
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Box hiển thị nhanh thông tin NCC đã chọn -->
                            <div id="supplierInfoBox" class="hidden mt-3 p-3 bg-amber-50/70 border border-amber-200/60 rounded-2xl text-xs space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-500 font-medium">Hotline đặt hàng:</span>
                                    <span id="supPhone" class="font-extrabold text-[#ee4d2d]"></span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-slate-500 font-medium">Người phụ trách:</span>
                                    <span id="supContact" class="font-bold text-slate-800"></span>
                                </div>
                                <div class="text-[11px] text-slate-600">
                                    <span class="text-slate-400">Kho giao hàng:</span> <span id="supAddress"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Mã phiếu nhập -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mã Phiếu Nhập Kho *</label>
                            <input type="text" name="receipt_code" value="{{ old('receipt_code', $suggestedCode) }}" required
                                   class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                        </div>

                        <!-- Ngày giờ nhận hàng -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Thời Gian Nhận Hàng *</label>
                            <input type="datetime-local" name="receipt_date" value="{{ old('receipt_date', Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}" required
                                   class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                        </div>

                        <!-- Ghi chú / Số hóa đơn đỏ -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Số Hóa Đơn NCC & Ghi Chú Giao Nhận</label>
                            <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Ví dụ: Hóa đơn đỏ số HD-8821, hàng giao đợt sáng sớm, kiểm đủ tem niêm phong..."
                                   class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                        </div>
                    </div>
                </div>

                <!-- Block 2: Danh sách các mặt hàng nhập thực tế -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                            <i class="fas fa-boxes-stacked text-blue-600"></i> 2. Danh Sách Hàng Thực Nhận
                        </h3>
                        <button type="button" onclick="addNewItemRow()" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-extrabold transition-colors flex items-center gap-1.5">
                            <i class="fas fa-plus"></i> Thêm Hàng
                        </button>
                    </div>

                    <!-- Bảng các dòng hàng -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left" id="itemsTable">
                            <thead>
                                <tr class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100 pb-2">
                                    <th class="pb-2 min-w-[220px]">Mặt Hàng Nhập *</th>
                                    <th class="pb-2 text-center w-28">Số Lượng *</th>
                                    <th class="pb-2 text-right w-36">Đơn Giá Nhập *</th>
                                    <th class="pb-2 text-right w-36">Thành Tiền</th>
                                    <th class="pb-2 text-center w-36">Hạn Sử Dụng</th>
                                    <th class="pb-2 text-center w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="itemsTableBody">
                                <!-- Dòng hàng mẫu 1 -->
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-2">
                        <button type="button" onclick="addNewItemRow()" class="w-full py-3 rounded-2xl border-2 border-dashed border-slate-200 hover:border-blue-400 text-slate-500 hover:text-blue-600 text-xs font-bold transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-plus-circle"></i> Bấm vào đây để thêm mặt hàng khác vào phiếu
                        </button>
                    </div>
                </div>

            </div>

            <!-- Cột Phải: Tổng kết phiếu & Nút xác nhận nhập kho (Chiếm 1 cột) -->
            <div class="space-y-6">

                <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-xs space-y-5 sticky top-24">
                    <h3 class="text-sm font-black text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-receipt text-emerald-600"></i> Tổng Kết Phiếu Nhập
                    </h3>

                    <!-- Tóm tắt số lượng -->
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between text-slate-500 font-medium">
                            <span>Tổng số mặt hàng:</span>
                            <span id="summaryTotalItems" class="font-extrabold text-slate-800 text-sm">0</span>
                        </div>

                        <div class="flex items-center justify-between text-slate-500 font-medium">
                            <span>Người tiếp nhận kho:</span>
                            <span class="font-bold text-slate-800">{{ Auth::user()->name }}</span>
                        </div>

                        <!-- Trạng thái thanh toán -->
                        <div class="pt-2 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Thanh Toán Cho Đơn Vị Cung Cấp *</label>
                            <select name="payment_status" required class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30">
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>🟢 Đã thanh toán (Tiền mặt/Chuyển khoản)</option>
                                <option value="debt" {{ old('payment_status') == 'debt' ? 'selected' : '' }}>🟡 Ghi nợ đối tác (Thanh toán định kỳ)</option>
                                <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>⚪ Chưa thanh toán</option>
                            </select>
                        </div>

                        <!-- Tổng tiền toàn phiếu -->
                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Tổng Giá Trị Lô Hàng</span>
                            <div class="text-2xl font-black text-[#ee4d2d] mt-1" id="summaryGrandTotal">0 VNĐ</div>
                            <span class="text-[10px] text-slate-400 block mt-1 italic">Chi phí này sẽ tự động ghi nhận vào báo cáo tài chính của Admin.</span>
                        </div>
                    </div>

                    <!-- Nút Xác nhận nhập kho -->
                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <button type="submit" class="w-full py-4 rounded-2xl bg-[#ee4d2d] hover:bg-[#d73211] text-white text-xs font-black uppercase tracking-wider shadow-lg shadow-rose-500/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fas fa-check-double text-sm"></i> Xác Nhận Nhập & Tăng Kho
                        </button>
                        
                        <p class="text-[11px] text-slate-500 text-center leading-relaxed">
                            <i class="fas fa-shield-halved text-emerald-600 mr-1"></i> Sau khi bấm xác nhận, hệ thống sẽ <strong>tự động cộng dồn số lượng</strong> vào tồn kho thực tế của từng mặt hàng.
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </form>
</div>

<!-- Dữ liệu JavaScript cho danh sách mặt hàng để chọn trong dropdown -->
<script>
    const inventoryItemsData = [
        @foreach($inventoryItems as $item)
        {
            id: {{ $item->id }},
            code: "{{ $item->item_code }}",
            name: "{{ addslashes($item->name) }}",
            unit: "{{ $item->unit }}",
            stock: {{ $item->current_stock }},
            cost: {{ $item->cost_price }},
            supplier_id: {{ $item->supplier_id ?? 'null' }}
        },
        @endforeach
    ];

    let rowCounter = 0;

    // Hiển thị thông tin NCC khi chọn
    const supplierSelect = document.getElementById('supplierSelect');
    const supplierInfoBox = document.getElementById('supplierInfoBox');
    
    function updateSupplierInfo() {
        const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            document.getElementById('supPhone').textContent = selectedOption.getAttribute('data-phone') || 'Chưa có SĐT';
            document.getElementById('supContact').textContent = selectedOption.getAttribute('data-contact') || 'Chưa cập nhật';
            document.getElementById('supAddress').textContent = selectedOption.getAttribute('data-address') || 'Chưa cập nhật';
            supplierInfoBox.classList.remove('hidden');
        } else {
            supplierInfoBox.classList.add('hidden');
        }
    }

    supplierSelect.addEventListener('change', updateSupplierInfo);
    updateSupplierInfo();

    // Thêm dòng mặt hàng mới vào bảng
    function addNewItemRow(preselectedItemId = null, defaultQty = 1) {
        rowCounter++;
        const tbody = document.getElementById('itemsTableBody');
        const tr = document.createElement('tr');
        tr.id = `row_${rowCounter}`;
        tr.className = 'hover:bg-slate-50/60 transition-colors';

        // Tạo options cho dropdown mặt hàng
        let optionsHtml = '<option value="">-- Chọn mặt hàng trong kho --</option>';
        inventoryItemsData.forEach(item => {
            const isSelected = (preselectedItemId && item.id == preselectedItemId) ? 'selected' : '';
            optionsHtml += `<option value="${item.id}" data-unit="${item.unit}" data-cost="${item.cost}" data-stock="${item.stock}" ${isSelected}>${item.name} (${item.code}) - Tồn: ${item.stock} ${item.unit}</option>`;
        });

        tr.innerHTML = `
            <td class="py-3 pr-2">
                <select name="items[${rowCounter}][inventory_item_id]" required onchange="onItemSelectChanged(${rowCounter})" id="item_select_${rowCounter}"
                        class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30">
                    ${optionsHtml}
                </select>
                <span id="unit_hint_${rowCounter}" class="text-[10px] text-slate-400 font-medium block mt-1">Đơn vị: -</span>
            </td>
            <td class="py-3 px-2">
                <input type="number" step="0.01" min="0.01" name="items[${rowCounter}][quantity]" value="${defaultQty}" id="qty_${rowCounter}" required oninput="calculateSubtotal(${rowCounter})"
                       class="w-full px-2.5 py-2 text-center rounded-xl bg-slate-50 border border-slate-200 text-xs font-black text-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
            </td>
            <td class="py-3 px-2">
                <input type="number" step="1" min="0" name="items[${rowCounter}][unit_price]" value="0" id="price_${rowCounter}" required oninput="calculateSubtotal(${rowCounter})"
                       class="w-full px-2.5 py-2 text-right rounded-xl bg-slate-50 border border-slate-200 text-xs font-black text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
            </td>
            <td class="py-3 px-2 text-right">
                <span class="text-xs font-black text-emerald-600 block" id="subtotal_${rowCounter}">0đ</span>
            </td>
            <td class="py-3 px-2">
                <input type="date" name="items[${rowCounter}][expiry_date]" 
                       class="w-full px-2 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] font-medium text-slate-600 focus:outline-hidden">
            </td>
            <td class="py-3 pl-2 text-center">
                <button type="button" onclick="removeRow(${rowCounter})" class="text-slate-400 hover:text-rose-600 transition-colors p-1" title="Xóa dòng">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        if (preselectedItemId) {
            onItemSelectChanged(rowCounter);
        }
        recalculateGrandTotal();
    }

    // Khi chọn mặt hàng từ dropdown, tự điền đơn vị và giá nhập tham khảo
    function onItemSelectChanged(rowId) {
        const select = document.getElementById(`item_select_${rowId}`);
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const unit = selectedOption.getAttribute('data-unit') || '';
            const cost = selectedOption.getAttribute('data-cost') || 0;
            const stock = selectedOption.getAttribute('data-stock') || 0;

            document.getElementById(`unit_hint_${rowId}`).textContent = `Đơn vị: ${unit} (Hiện tồn: ${stock})`;
            document.getElementById(`price_${rowId}`).value = cost;
            calculateSubtotal(rowId);
        }
    }

    // Tính thành tiền dòng
    function calculateSubtotal(rowId) {
        const qty = parseFloat(document.getElementById(`qty_${rowId}`).value) || 0;
        const price = parseFloat(document.getElementById(`price_${rowId}`).value) || 0;
        const subtotal = qty * price;

        document.getElementById(`subtotal_${rowId}`).textContent = new Intl.NumberFormat('vi-VN').format(subtotal) + 'đ';
        recalculateGrandTotal();
    }

    // Xóa dòng
    function removeRow(rowId) {
        const tr = document.getElementById(`row_${rowId}`);
        if (tr) {
            tr.remove();
            recalculateGrandTotal();
        }
    }

    // Tính tổng tiền toàn phiếu
    function recalculateGrandTotal() {
        let total = 0;
        let count = 0;

        const tableBody = document.getElementById('itemsTableBody');
        const rows = tableBody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const rowId = rows[i].id.replace('row_', '');
            const qty = parseFloat(document.getElementById(`qty_${rowId}`)?.value) || 0;
            const price = parseFloat(document.getElementById(`price_${rowId}`)?.value) || 0;
            total += (qty * price);
            count++;
        }

        document.getElementById('summaryTotalItems').textContent = count + ' mặt hàng';
        document.getElementById('summaryGrandTotal').textContent = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
    }

    // Tự động khởi tạo ít nhất 1 dòng khi mở trang
    document.addEventListener('DOMContentLoaded', function() {
        @if(request('item_id'))
            addNewItemRow({{ request('item_id') }});
        @else
            addNewItemRow();
        @endif
    });
</script>
@endsection
