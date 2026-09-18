import React, { useState } from 'react';

export default function CustomerBackupsApp({
    backups = [],
    csrfToken = '',
    routes = {}
}) {
    const [confirmModal, setConfirmModal] = useState(null); // { type: 'restore' | 'delete', filename: '' }

    return (
        <div className="container-fluid p-0">
            {/* Header */}
            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold d-flex align-items-center gap-2">
                        <span>Sao Lưu & Khôi Phục Dữ Liệu</span>
                        <span className="badge bg-success-subtle text-success fs-6">Backup Security</span>
                    </h1>
                    <p className="text-muted small mb-0">Lưu trữ an toàn cơ sở dữ liệu hội viên và hỗ trợ phục hồi khẩn cấp một chạm</p>
                </div>

                <form action={routes.createBackup || '/admin/customer-backups'} method="POST">
                    <input type="hidden" name="_token" value={csrfToken} />
                    <button type="submit" className="btn btn-success btn-sm fw-bold shadow-sm">
                        <i className="bi bi-cloud-arrow-up me-1"></i> Tạo Bản Sao Lưu Ngay
                    </button>
                </form>
            </div>

            {/* Backups Table Card */}
            <div className="card border-0 shadow-sm">
                <div className="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                    <div className="p-2 bg-success-subtle rounded-3 text-success">
                        <i className="bi bi-database"></i>
                    </div>
                    <h6 className="m-0 fw-bold text-gray-800">Danh Sách Các Bản Sao Lưu Cơ Sở Dữ Liệu</h6>
                </div>
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th className="py-3 px-4 text-center" style={{ width: '60px' }}>STT</th>
                                    <th>Tên File Bản Sao</th>
                                    <th>Thời Điểm Sao Lưu</th>
                                    <th>Dung Lượng</th>
                                    <th className="text-end px-4">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                {backups.length === 0 ? (
                                    <tr>
                                        <td colSpan="5" className="text-center py-5 text-muted small">
                                            Chưa có bản sao lưu nào được tạo. Hãy nhấn "Tạo Bản Sao Lưu Ngay" để khởi tạo điểm lưu trữ an toàn.
                                        </td>
                                    </tr>
                                ) : (
                                    backups.map((bk, index) => (
                                        <tr key={`bk-${index}`}>
                                            <td className="text-center px-4 text-muted small">{index + 1}</td>
                                            <td className="fw-bold text-gray-800 font-monospace small">
                                                <i className="bi bi-filetype-json text-warning me-2 fs-5"></i>
                                                {bk.filename}
                                            </td>
                                            <td className="small text-muted">{bk.created_at || '---'}</td>
                                            <td className="small text-muted">{bk.size || '---'}</td>
                                            <td className="text-end px-4">
                                                <div className="btn-group">
                                                    <a
                                                        href={`/admin/customer-backups/download/${encodeURIComponent(bk.filename)}`}
                                                        className="btn btn-sm btn-light border text-primary"
                                                        title="Tải về máy"
                                                    >
                                                        <i className="bi bi-download"></i>
                                                    </a>

                                                    <button
                                                        type="button"
                                                        onClick={() => setConfirmModal({ type: 'restore', filename: bk.filename })}
                                                        className="btn btn-sm btn-light border text-warning"
                                                        title="Khôi phục dữ liệu này"
                                                    >
                                                        <i className="bi bi-arrow-counterclockwise"></i>
                                                    </button>

                                                    <button
                                                        type="button"
                                                        onClick={() => setConfirmModal({ type: 'delete', filename: bk.filename })}
                                                        className="btn btn-sm btn-light border text-danger"
                                                        title="Xóa bản sao lưu"
                                                    >
                                                        <i className="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {/* Modal Xác Nhận */}
            {confirmModal && (
                <div className="modal show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content border-0 shadow-lg">
                            <div className="modal-header border-0">
                                <h5 className="modal-title fw-bold">
                                    {confirmModal.type === 'restore' ? 'Xác Nhận Khôi Phục Dữ Liệu' : 'Xác Nhận Xóa Bản Sao'}
                                </h5>
                                <button type="button" className="btn-close" onClick={() => setConfirmModal(null)}></button>
                            </div>
                            <div className="modal-body py-0">
                                {confirmModal.type === 'restore' ? (
                                    <div className="alert alert-warning small mb-0">
                                        <i className="bi bi-exclamation-triangle-fill me-1"></i>
                                        Bạn có chắc muốn khôi phục cơ sở dữ liệu từ file <strong>{confirmModal.filename}</strong>?
                                        Thao tác này sẽ ghi đè dữ liệu hiện tại bằng dữ liệu từ thời điểm sao lưu!
                                    </div>
                                ) : (
                                    <div className="alert alert-danger small mb-0">
                                        <i className="bi bi-exclamation-octagon-fill me-1"></i>
                                        Bạn có chắc muốn xóa vĩnh viễn bản sao lưu <strong>{confirmModal.filename}</strong>? Hành động này không thể hoàn tác!
                                    </div>
                                )}
                            </div>
                            <div className="modal-footer border-0">
                                <button type="button" className="btn btn-light btn-sm" onClick={() => setConfirmModal(null)}>Hủy bỏ</button>
                                
                                {confirmModal.type === 'restore' ? (
                                    <form action={`/admin/customer-backups/restore/${encodeURIComponent(confirmModal.filename)}`} method="POST">
                                        <input type="hidden" name="_token" value={csrfToken} />
                                        <button type="submit" className="btn btn-warning btn-sm fw-bold">
                                            Khôi Phục Ngay
                                        </button>
                                    </form>
                                ) : (
                                    <form action={`/admin/customer-backups/delete/${encodeURIComponent(confirmModal.filename)}`} method="POST">
                                        <input type="hidden" name="_token" value={csrfToken} />
                                        <input type="hidden" name="_method" value="DELETE" />
                                        <button type="submit" className="btn btn-danger btn-sm fw-bold">
                                            Xóa Vĩnh Viễn
                                        </button>
                                    </form>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
