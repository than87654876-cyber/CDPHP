@extends('layouts.admin')

@section('title', 'Sao lưu & Khôi phục Dữ liệu - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Sao Lưu & Khôi Phục Khách Hàng</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-extrabold border border-emerald-200">
                    Backup Security
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Lưu trữ an toàn cơ sở dữ liệu hội viên và hỗ trợ phục hồi khẩn cấp</p>
        </div>
        <form action="{{ route('backup_khachhang_create') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer flex items-center gap-2">
                <i class="fas fa-cloud-arrow-up"></i> Tạo bản sao lưu ngay
            </button>
        </form>
    </div>

    <!-- Alert Flash Notifications -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs text-xs font-semibold">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-lg leading-none">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs text-xs font-semibold">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-exclamation text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-lg leading-none">&times;</button>
        </div>
    @endif

    <!-- MAIN TABLE CARD -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                <i class="fas fa-database"></i>
            </div>
            <h3 class="text-sm font-extrabold text-slate-900">Danh sách các bản sao lưu cơ sở dữ liệu</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="py-4 px-6 w-16 text-center">STT</th>
                        <th class="py-4 px-6">Tên file bản sao</th>
                        <th class="py-4 px-6">Thời gian tạo</th>
                        <th class="py-4 px-6">Kích thước</th>
                        <th class="py-4 px-6">Trạng thái</th>
                        <th class="py-4 px-6">Người thực hiện</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($backups as $index => $backup)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-center font-bold text-slate-400">
                                {{ $loop->iteration + ($backups->currentPage() - 1) * $backups->perPage() }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-black text-slate-900 font-mono text-xs">{{ $backup->filename }}</span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 font-medium">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <i class="far fa-clock text-slate-400"></i>
                                    <span>{{ $backup->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-700">
                                {{ number_format(($backup->size ?? 0) / 1024, 2) }} KB
                            </td>
                            <td class="py-4 px-6">
                                @if($backup->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-extrabold text-[11px] border border-emerald-200">
                                        ✔ Hoàn tất
                                    </span>
                                @elseif($backup->status === 'restored')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-extrabold text-[11px] border border-blue-200">
                                        ↺ Đã khôi phục
                                    </span>
                                @elseif($backup->status === 'failed')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-rose-50 text-rose-700 font-extrabold text-[11px] border border-rose-200">
                                        ✕ Thất bại
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-50 text-amber-700 font-extrabold text-[11px] border border-amber-200">
                                        ⏳ Đang xử lý
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-800">
                                {{ $backup->createdBy?->fullname ?? 'Hệ thống tự động' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('backup_khachhang_download', $backup->id) }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-900 text-slate-600 hover:text-white flex items-center justify-center transition-all shadow-2xs" title="Tải về file sao lưu">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>

                                    <form action="{{ route('backup_khachhang_restore', $backup->id) }}" method="POST" class="inline-block" id="restore-form-{{ $backup->id }}">
                                        @csrf
                                        <input type="hidden" name="confirm" value="yes">
                                        <button type="button" class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-500 text-amber-600 hover:text-white flex items-center justify-center transition-all shadow-2xs cursor-pointer" onclick="confirmRestore({{ $backup->id }})" title="Khôi phục lại dữ liệu">
                                            <i class="fas fa-rotate-left text-xs"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('backup_khachhang_delete', $backup->id) }}" method="POST" class="inline-block" id="delete-form-{{ $backup->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition-all shadow-2xs cursor-pointer" onclick="confirmDelete({{ $backup->id }})" title="Xóa file sao lưu">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto">
                                        <i class="fas fa-cloud"></i>
                                    </div>
                                    <h4 class="font-extrabold text-slate-800 text-sm">Chưa có bản sao lưu nào</h4>
                                    <p class="text-xs text-slate-400">Bấm nút "Tạo bản sao lưu ngay" phía trên để sao lưu dữ liệu khách hàng.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($backups->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $backups->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmRestore(id) {
        if (confirm('CẢNH BÁO: Việc khôi phục sẽ ghi đè dữ liệu hiện tại bằng dữ liệu của bản sao lưu này. Bạn có chắc chắn muốn tiếp tục?')) {
            document.getElementById('restore-form-' + id).submit();
        }
    }

    function confirmDelete(id) {
        if (confirm('Bạn có chắc chắn muốn xóa bản sao lưu này? Hành động này không thể hoàn tác.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection
