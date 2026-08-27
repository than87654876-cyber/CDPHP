@extends('layouts.admin')

@section('title', 'Quản lý Đánh giá - FOODDAILY')

@section('styles')
    <link href="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Đánh giá chất lượng dịch vụ</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-dark" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Bảng dữ liệu DataTables -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Đánh giá & Nhận xét của Khách hàng</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-dark" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">STT</th>
                            <th>Khách hàng</th>
                            <th style="width: 120px;">Đơn hàng</th>
                            <th style="width: 120px;">Số sao</th>
                            <th>Nội dung nhận xét</th>
                            <th style="width: 150px;">Ngày đánh giá</th>
                            <th style="width: 100px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $index => $review)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold">
                                    {{ $review->user->fullname ?? 'Khách vãng lai' }}<br>
                                    <small class="text-muted">{{ $review->user->phone ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('donhang_xem', $review->order_id) }}" class="font-weight-bold text-primary">
                                        #FDL-{{ $review->order_id }}
                                    </a>
                                </td>
                                <td>
                                    <div class="text-nowrap">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <small class="text-muted">({{ $review->rating }}/5 sao)</small>
                                </td>
                                <td>{{ $review->comment ?? 'Không có bình luận.' }}</td>
                                <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <form action="{{ route('quanly_reviews.xoa', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này không?');" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm font-weight-bold shadow-sm">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('admin/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('admin/js/demo/datatables-demo.js') }}"></script>
@endsection
