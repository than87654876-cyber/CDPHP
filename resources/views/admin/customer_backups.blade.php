@extends('layouts.admin')

@section('title', 'Sao lưu và Khôi phục Khách hàng - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Sao lưu & Khôi phục dữ liệu khách hàng</h1>
            <p class="text-muted">Lưu trữ an toàn dữ liệu khách hàng và khôi phục khi cần thiết.</p>
        </div>
        <form action="{{ route('backup_khachhang_create') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-success shadow-sm">
                <i class="fas fa-save mr-2"></i> Sao lưu ngay
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách bản sao lưu</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="backupTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên file</th>
                            <th>Thời gian tạo</th>
                            <th>Kích thước</th>
                            <th>Trạng thái</th>
                            <th>Người tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                            <tr>
                                <td>{{ $loop->iteration + ($backups->currentPage() - 1) * $backups->perPage() }}</td>
                                <td>{{ $backup->filename }}</td>
                                <td>{{ $backup->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ number_format(($backup->size ?? 0) / 1024, 2) }} KB</td>
                                <td>
                                    @if($backup->status === 'completed')
                                        <span class="badge badge-success">Hoàn thành</span>
                                    @elseif($backup->status === 'restored')
                                        <span class="badge badge-info">Đã khôi phục</span>
                                    @elseif($backup->status === 'failed')
                                        <span class="badge badge-danger">Thất bại</span>
                                    @else
                                        <span class="badge badge-warning">Đang xử lý</span>
                                    @endif
                                </td>
                                <td>{{ $backup->createdBy?->fullname ?? 'Hệ thống' }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('backup_khachhang_download', $backup->id) }}" class="btn btn-sm btn-primary mb-1" title="Tải về">
                                        <i class="fas fa-download"></i>
                                    </a>

                                    <form action="{{ route('backup_khachhang_restore', $backup->id) }}" method="POST" class="d-inline" id="restore-form-{{ $backup->id }}">
                                        @csrf
                                        <input type="hidden" name="confirm" value="yes">
                                        <button type="button" class="btn btn-sm btn-warning mb-1" onclick="confirmRestore({{ $backup->id }})" title="Khôi phục">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('backup_khachhang_delete', $backup->id) }}" method="POST" class="d-inline" id="delete-form-{{ $backup->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger mb-1" onclick="confirmDelete({{ $backup->id }})" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Chưa có bản sao lưu nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $backups->links() }}</div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function confirmRestore(id) {
        if (confirm('Xác nhận khôi phục dữ liệu từ bản sao lưu này? Hành động này sẽ phục hồi dữ liệu khách hàng theo bản sao lưu.')) {
            document.getElementById('restore-form-' + id).submit();
        }
    }

    function confirmDelete(id) {
        if (confirm('Xác nhận xóa bản sao lưu này? Hành động này không thể hoàn tác.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection
