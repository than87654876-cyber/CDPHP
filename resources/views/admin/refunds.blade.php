@extends('layouts.admin')

@section('title', 'Quản lý Hoàn tiền - FOODDAILY')

@section('styles')
    <link href="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Yêu cầu khiếu nại hoàn tiền</h1>
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
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Đơn khiếu nại & Hoàn trả dòng tiền</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-dark" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên khách hàng / SĐT</th>
                            <th>Ngày gửi yêu cầu</th>
                            <th>Liên kết mã đơn gốc</th>
                            <th>Số tiền hoàn</th>
                            <th>Trạng thái xác nhận</th>
                            <th>Thao tác xử lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $index => $order)
                            @php
                                $reqAmountText = number_format($order->final_amount, 0, ',', '.') . ' đ';
                                if (preg_match('/Số tiền yêu cầu: ([^,\]]+)/', $order->health_notes, $matches)) {
                                    $reqAmountText = $matches[1];
                                }
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold">
                                    {{ $order->user->fullname ?? 'Khách vãng lai' }}<br>
                                    <small class="text-muted">{{ $order->user->phone ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                <td><a href="{{ route('donhang_xem', $order->id) }}" class="font-weight-bold text-primary">#FDL-{{ $order->id }}</a></td>
                                <td class="font-weight-bold text-danger">{{ $reqAmountText }}</td>
                                <td>
                                    @if($order->payment_status === 'refunded')
                                        <span class="badge badge-success shadow-sm"><i class="fas fa-check-circle"></i> Đã hoàn tiền</span>
                                    @elseif(strpos($order->health_notes, '[Admin Phản hồi:') !== false)
                                        <span class="badge badge-danger shadow-sm"><i class="fas fa-times-circle"></i> Đã từ chối</span>
                                    @else
                                        <span class="badge badge-warning text-dark shadow-sm"><i class="fas fa-clock"></i> Đang chờ duyệt</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('yeucauhoan_xem', $order->id) }}"
                                        class="btn btn-primary btn-sm font-weight-bold shadow-sm"><i
                                            class="fas fa-search-plus"></i> Thẩm định đơn</a>
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
