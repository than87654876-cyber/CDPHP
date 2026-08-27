@extends('layouts.admin')

@section('title', 'Quản lý Khách vãng lai - FOODDAILY')

@section('styles')
    <link href="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý Khách vãng lai (Guest)</h1>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 text-dark" role="alert">
        <strong><i class="fas fa-check-circle mr-1"></i> Thành công!</strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách phiên mua hàng của khách vãng lai</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-dark" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã Guest</th>
                            <th>Tên hiển thị</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Số đơn hàng đã đặt</th>
                            <th>Ngày đặt đơn đầu</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guests as $guest)
                        <tr>
                            <td>GST-{{ sprintf('%03d', $guest->id) }}</td>
                            <td class="font-weight-bold">{{ $guest->fullname }}</td>
                            <td>{{ $guest->phone ?? 'Không có' }}</td>
                            <td>{{ $guest->email }}</td>
                            <td class="text-center font-weight-bold text-primary">
                                {{ $guest->orders_count ?? $guest->orders()->count() }}
                            </td>
                            <td>{{ $guest->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('quanly_donhang') }}?search={{ $guest->phone ?? $guest->email }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-list-alt"></i> Xem các đơn
                                </a>
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
