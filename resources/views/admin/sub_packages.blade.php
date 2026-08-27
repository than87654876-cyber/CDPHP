@extends('layouts.admin')

@section('title', 'Quản lý Đăng ký Gói - FOODDAILY')

@section('styles')
    <link href="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 text-dark" role="alert">
        <strong><i class="fas fa-check-circle mr-1"></i> Thành công!</strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 text-dark" role="alert">
        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Lỗi!</strong> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Danh sách đăng ký gói dịch vụ</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách khách hàng đang sử dụng gói</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-dark" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên gói</th>
                            <th>Mã đơn mua</th>
                            <th>Tài khoản / SĐT</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái hoạt động</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $index => $sub)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-weight-bold">{{ $sub->package->package_name }}</td>
                            <td><a href="{{ route('donhang_xem', $sub->order_id) }}"
                                    class="font-weight-bold text-underline">#FDL-{{ $sub->order_id }}</a></td>
                            <td>{{ $sub->user->fullname }}<br><small class="text-muted">{{ $sub->user->phone ?? 'Chưa cập nhật' }}</small>
                            </td>
                            <td>
                                @if($sub->order && $sub->order->payment_status === 'paid')
                                <span class="badge badge-success">Đã thanh toán</span>
                                @elseif($sub->order && $sub->order->payment_status === 'refunded')
                                <span class="badge badge-warning text-dark">Đã hoàn tiền</span>
                                @else
                                <span class="badge badge-danger">Chưa thanh toán</span>
                                @endif
                            </td>
                            <td>
                                @if($sub->status === 'active')
                                <span class="badge badge-success">Hoạt động (Còn {{ $sub->remaining_days }} ngày)</span>
                                @elseif($sub->status === 'paused')
                                <span class="badge badge-warning text-dark">Tạm dừng (Còn {{ $sub->remaining_days }} ngày)</span>
                                @elseif($sub->status === 'expired')
                                <span class="badge badge-secondary">Hết hạn</span>
                                @elseif($sub->status === 'pending')
                                <span class="badge badge-info">Chờ thanh toán (Còn {{ $sub->remaining_days }} ngày)</span>
                                @else
                                <span class="badge badge-danger">Đã hủy</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('goidangky_xem', $sub->id) }}"
                                    class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Xem</a>
                                <a href="{{ route('goidangky_chinhsua', $sub->id) }}"
                                    class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                                <form action="{{ route('goidangky_xoa', $sub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đăng ký gói này?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa"><i
                                        class="fas fa-trash"></i> Xóa</button>
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
