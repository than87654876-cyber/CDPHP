@extends('layouts.admin')

@section('title', 'Chỉnh sửa gói món ăn - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <h1 class="h3 mb-0 text-gray-800">Cập nhật thông tin gói món ăn</h1>
        <a href="{{ route('quanly_goidichvu') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-undo fa-sm"></i> Hủy và Quay lại
        </a>
    </div>

    <form action="{{ route('goidichvu_chinhsua.post', $package->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Biểu mẫu thay đổi dữ liệu gói</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-5">
                                <label for="package_name" class="font-weight-bold text-dark">Tên gói món ăn <span class="text-danger">*</span></label>
                                <input type="text" class="form-control font-weight-bold" id="package_name" name="package_name" value="{{ old('package_name', $package->package_name) }}" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="duration_days" class="font-weight-bold text-dark">Số ngày hiệu lực <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="duration_days" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" required min="1">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="price" class="font-weight-bold text-dark">Giá gói tích hợp (vnđ) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control text-danger font-weight-bold" id="price" name="price" value="{{ old('price', $package->price) }}" required min="0">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="status" class="font-weight-bold text-dark">Trạng thái áp dụng</label>
                                <select class="form-control font-weight-bold" id="status" name="status">
                                    <option value="active" {{ old('status', $package->status ? 'active' : 'inactive') === 'active' ? 'selected' : '' }}>Đang kích hoạt (Active)</option>
                                    <option value="inactive" {{ old('status', $package->status ? 'active' : 'inactive') === 'inactive' ? 'selected' : '' }}>Tạm ngưng (Inactive)</option>
                                </select>
                            </div>
                            <div class="form-group col-md-8">
                                <label for="description" class="font-weight-bold text-dark">Mô tả chi tiết gói món ăn</label>
                                <textarea class="form-control" id="description" name="description" rows="2" placeholder="Mô tả thực đơn gói, các tùy chọn giao hàng...">{{ old('description', $package->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-utensils mr-2"></i>Cấu hình danh sách món ăn trong gói</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6 col-sm-12 mb-2 mb-md-0">
                                <div class="input-group">
                                    <input type="text" id="dishSearch" class="form-control bg-light border-0 small" placeholder="Tìm kiếm món ăn đơn..." aria-label="Search">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" disabled style="background-color: #ce1126; border-color: #ce1126;">
                                            <i class="fas fa-search fa-sm text-white"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 text-md-right text-left">
                                <span class="text-muted small"><i class="fas fa-info-circle mr-1"></i> Tích chọn vào ô đầu dòng để thêm món ăn thành phần vào cấu hình gói dịch vụ này.</span>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-bordered table-hover text-dark table-striped">
                                <thead class="bg-light sticky-top" style="z-index: 10;">
                                    <tr>
                                        <th style="width: 8%" class="text-center">Chọn</th>
                                        <th style="width: 15%">Hình ảnh</th>
                                        <th style="width: 45%">Tên món ăn đơn</th>
                                        <th style="width: 20%">Giá gốc bán lẻ</th>
                                        <th style="width: 12%">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dishes as $dish)
                                    @php
                                        $isChecked = old('dishes') 
                                            ? in_array($dish->id, old('dishes')) 
                                            : $package->dishes->contains($dish->id);
                                    @endphp
                                    <tr class="dish-row">
                                        <td class="text-center align-middle">
                                            <input type="checkbox" name="dishes[]" value="{{ $dish->id }}" style="width: 18px; height: 18px; cursor: pointer;" {{ $isChecked ? 'checked' : '' }}>
                                        </td>
                                        <td class="align-middle text-center">
                                            <img src="{{ $dish->image_url ? (Str::startsWith($dish->image_url, 'http') ? $dish->image_url : asset($dish->image_url)) : asset('logo.jpg') }}" alt="{{ $dish->dish_name }}" class="img-thumbnail rounded shadow-sm" style="max-height: 45px; max-width: 60px; object-fit: cover;">
                                        </td>
                                        <td class="align-middle font-weight-bold dish-name">{{ $dish->dish_name }}</td>
                                        <td class="align-middle text-secondary font-weight-bold">{{ number_format($dish->price, 0, ',', '.') }} đ</td>
                                        <td class="align-middle">
                                            <span class="badge badge-success px-2 py-1">Có sẵn</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Không có món ăn đơn lẻ nào trong hệ thống.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-start gap-2">
                            <button type="submit" class="btn text-white shadow-sm px-4 mr-2" style="background-color: #ce1126; border: none;">
                                <i class="fas fa-save fa-sm mr-1"></i> Lưu thay đổi gói món
                            </button>
                            <a href="{{ route('quanly_goidichvu') }}" class="btn btn-secondary shadow-sm px-3">Hủy bỏ</a>
                        </div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const searchInput = document.getElementById("dishSearch");
                            if (searchInput) {
                                searchInput.addEventListener("input", function () {
                                    const query = this.value.toLowerCase().normalize("NFC").trim();
                                    const rows = document.querySelectorAll(".dish-row");
                                    
                                    rows.forEach(row => {
                                        const dishNameEl = row.querySelector(".dish-name");
                                        if (dishNameEl) {
                                            const dishName = dishNameEl.textContent.toLowerCase().normalize("NFC");
                                            if (dishName.includes(query)) {
                                                row.style.setProperty('display', '', 'important');
                                            } else {
                                                row.style.setProperty('display', 'none', 'important');
                                            }
                                        }
                                    });
                                });
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </form>
@endsection
