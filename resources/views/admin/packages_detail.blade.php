@extends('layouts.admin')

@section('title', 'Chi tiết gói món ăn - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết gói món ăn</h1>
        <a href="{{ route('quanly_goidichvu') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách gói
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hình ảnh gói món ăn</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('logo.jpg') }}" alt="Hình ảnh gói món ăn" class="img-fluid rounded shadow-sm"
                        style="max-height: 250px; width: 100%; object-fit: cover;">
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin tổng quan gói</h6>
                    <a href="{{ route('goidichvu_chinhsua', $package->id) }}" class="btn btn-sm btn-warning shadow-sm">
                        <i class="fas fa-edit fa-sm"></i> Chỉnh sửa gói
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-dark mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 30%">Mã định danh gói (ID):</th>
                                <td class="font-weight-bold">#PKG-{{ $package->id }}</td>
                            </tr>
                            <tr>
                                <th>Tên gói món ăn:</th>
                                <td class="font-weight-bold text-primary">{{ $package->package_name }}</td>
                            </tr>
                            <tr>
                                <th>Số ngày hiệu lực:</th>
                                <td class="font-weight-bold">{{ $package->duration_days }} ngày</td>
                            </tr>
                            <tr>
                                <th>Ngày tạo gói:</th>
                                <td>{{ $package->created_at ? $package->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Giá gói tích hợp:</th>
                                <td class="text-danger font-weight-bold fs-5">{{ number_format($package->price, 0, ',', '.') }} đ</td>
                            </tr>
                            <tr>
                                <th>Trạng thái áp dụng:</th>
                                <td>
                                    @if($package->status)
                                        <span class="badge badge-success">Đang kích hoạt</span>
                                    @else
                                        <span class="badge badge-secondary">Tạm ngưng</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Mô tả chi tiết:</th>
                                <td>{{ $package->description ?? 'Không có mô tả cho gói này' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-utensils mr-2"></i>Các món ăn có trong gói này</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped text-dark">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 10%">STT</th>
                                    <th style="width: 15%">Hình ảnh</th>
                                    <th style="width: 35%">Tên món ăn thành phần</th>
                                    <th style="width: 20%">Trạng thái phục vụ</th>
                                    <th style="width: 20%">Giá gốc lẻ (Bán đơn)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalOriginalPrice = 0; @endphp
                                @forelse($package->dishes as $index => $dish)
                                    @php $totalOriginalPrice += $dish->price; @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <img src="{{ $dish->image_url ? (Str::startsWith($dish->image_url, 'http') ? $dish->image_url : asset($dish->image_url)) : asset('logo.jpg') }}" alt="{{ $dish->dish_name }}" class="img-thumbnail" style="max-height: 50px;">
                                        </td>
                                        <td class="font-weight-bold text-secondary">{{ $dish->dish_name }}</td>
                                        <td>
                                            @if($dish->is_available)
                                                <span class="badge badge-success">Đang phục vụ</span>
                                            @else
                                                <span class="badge badge-danger">Tạm ngưng</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-dark">{{ number_format($dish->price, 0, ',', '.') }} đ</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Chưa cấu hình món ăn nào cho gói này.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($package->dishes->isNotEmpty())
                            <tfoot class="font-weight-bold bg-gray-100">
                                <tr>
                                    <td colspan="3" class="text-right">Tổng giá trị thực tế (Nếu mua lẻ):</td>
                                    <td colspan="2" class="text-danger">
                                        {{ number_format($totalOriginalPrice, 0, ',', '.') }} đ 
                                        @if($totalOriginalPrice > $package->price)
                                            (Tiết kiệm được {{ number_format($totalOriginalPrice - $package->price, 0, ',', '.') }} đ)
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
