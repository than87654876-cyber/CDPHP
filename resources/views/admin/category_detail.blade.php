@extends('layouts.admin')

@section('title', 'Chi tiết Danh mục - FOODDAILY')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Chi tiết danh mục: {{ $category->category_name }}</h1>
    <a href="{{ route('quanly_danhmuc') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="row">
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow mb-4 border-left-info text-dark">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-folder-open mr-2"></i>Thông tin danh mục</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Tên danh mục:</strong> {{ $category->category_name }}</p>
                <p class="mb-2"><strong>Ngày thiết lập tạo:</strong> {{ $category->created_at ? $category->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                <p class="mb-2"><strong>Cập nhật cuối:</strong> {{ $category->updated_at ? $category->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                <p class="mb-3"><strong>Mô tả:</strong> {{ $category->description ?? 'Chưa có miêu tả' }}</p>
                <a href="{{ route('danhmuc_chinhsua', $category->id) }}" class="btn btn-warning btn-sm btn-block font-weight-bold">
                    <i class="fas fa-edit"></i> Chỉnh sửa danh mục
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card shadow mb-4 text-dark">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-hamburger mr-2"></i>Danh sách món ăn thuộc nhóm này</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th style="width: 80px;">STT</th>
                                <th>Tên món ăn</th>
                                <th>Giá tiền</th>
                                <th>Trạng thái</th>
                                <th style="width: 150px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($category->dishes as $index => $dish)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold">{{ $dish->dish_name }}</td>
                                <td class="text-danger font-weight-bold">{{ number_format($dish->price, 0, ',', '.') }} đ</td>
                                <td>
                                    @if($dish->is_available)
                                        <span class="badge badge-success">Đang bán</span>
                                    @else
                                        <span class="badge badge-secondary">Tạm ngưng</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('monandon_xem', $dish->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Không có món ăn nào thuộc danh mục này.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('admin/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/js/demo/datatables-demo.js') }}"></script>
@endsection
