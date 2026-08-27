@extends('layouts.admin')

@section('title', 'Chi tiết chương trình khuyến mãi - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết chương trình khuyến mãi</h1>
        <a href="{{ route('quanly_khuyenmai') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-1"></i>Thông tin chi tiết mã ưu đãi</h6>
            <a href="{{ route('khuyenmai_chinhsua', $promotion->id) }}" class="btn btn-warning btn-sm fw-bold"><i class="fas fa-edit"></i> Chỉnh sửa</a>
        </div>
        <div class="card-body text-dark">
            <div class="table-responsive">
                <table class="table table-bordered text-dark mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 35%">Mã ưu đãi (Coupon Code):</th>
                            <td class="font-weight-bold text-primary">{{ $promotion->coupon_code }}</td>
                        </tr>
                        <tr>
                            <th>Loại chiết khấu:</th>
                            <td>{{ $promotion->discount_type === 'percent' ? 'Giảm giá theo phần trăm (%)' : 'Giảm số tiền cố định (đ)' }}</td>
                        </tr>
                        <tr>
                            <th>Giá trị giảm giá:</th>
                            <td class="text-danger font-weight-bold">
                                {{ number_format($promotion->discount_value, 0, ',', '.') }} {{ $promotion->discount_type === 'percent' ? '%' : ' đ' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Giá trị đơn hàng tối thiểu:</th>
                            <td>{{ number_format($promotion->min_order_value, 0, ',', '.') }} đ</td>
                        </tr>
                        <tr>
                            <th>Thời hạn áp dụng:</th>
                            <td>
                                Từ ngày <strong>{{ $promotion->start_date->format('d/m/Y') }}</strong> 
                                đến hết ngày <strong>{{ $promotion->end_date->format('d/m/Y') }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Giới hạn số lần sử dụng:</th>
                            <td>{{ $promotion->usage_limit ?? 'Vô hạn (Không giới hạn)' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tạo lập hệ thống:</th>
                            <td>{{ $promotion->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
