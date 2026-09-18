import React, { useState } from 'react';

export default function AdminRefundsApp({
    refunds = [],
    csrfToken = '',
    routes = {}
}) {
    const [previewImage, setPreviewImage] = useState(null);

    return (
        <div className="container-fluid p-0">
            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">Duyệt Yêu Cầu Hoàn Tiền / Khiếu Nại</h1>
                    <p className="text-muted small mb-0">Thẩm định chứng từ sự cố món ăn và xử lý chuyển tiền hoàn trả khách hàng</p>
                </div>
            </div>

            <div className="card border-0 shadow-sm">
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th className="py-3 px-4">Mã Đơn</th>
                                    <th>Khách Hàng</th>
                                    <th>Lý Do Hoàn Tiền</th>
                                    <th>Tài Khoản Nhận</th>
                                    <th>Ảnh Minh Chứng</th>
                                    <th>Trạng Thái</th>
                                    <th className="text-end px-4">Xử Lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                {refunds.length === 0 ? (
                                    <tr>
                                        <td colSpan="7" className="text-center py-5 text-muted small">
                                            Không có yêu cầu hoàn tiền nào đang chờ xử lý.
                                        </td>
                                    </tr>
                                ) : (
                                    refunds.map(ref => (
                                        <tr key={`refund-${ref.id}`}>
                                            <td className="px-4 fw-bold text-danger">#FDL-{ref.order_id}</td>
                                            <td className="fw-semibold">{ref.order?.full_name || ref.user?.name || 'Khách hàng'}</td>
                                            <td className="small text-muted" style={{ maxWidth: '240px' }}>{ref.reason}</td>
                                            <td className="small font-monospace text-primary">{ref.bank_account || '---'}</td>
                                            <td>
                                                {ref.image ? (
                                                    <button
                                                        type="button"
                                                        onClick={() => setPreviewImage(ref.image.startsWith('http') ? ref.image : `/${ref.image}`)}
                                                        className="btn btn-sm btn-outline-secondary py-0 px-2 small"
                                                    >
                                                        <i className="bi bi-image me-1"></i> Xem ảnh
                                                    </button>
                                                ) : (
                                                    <span className="small text-muted">Không có</span>
                                                )}
                                            </td>
                                            <td>
                                                {ref.status === 'pending' && <span className="badge bg-warning text-dark">Chờ duyệt</span>}
                                                {ref.status === 'approved' && <span className="badge bg-success">Đã hoàn</span>}
                                                {ref.status === 'rejected' && <span className="badge bg-danger">Từ chối</span>}
                                            </td>
                                            <td className="text-end px-4">
                                                {ref.status === 'pending' ? (
                                                    <div className="btn-group">
                                                        <form action={`/admin/refunds/${ref.id}/approve`} method="POST" className="d-inline">
                                                            <input type="hidden" name="_token" value={csrfToken} />
                                                            <button type="submit" className="btn btn-sm btn-success fw-bold">
                                                                Duyệt Hoàn
                                                            </button>
                                                        </form>
                                                        <form action={`/admin/refunds/${ref.id}/reject`} method="POST" className="d-inline">
                                                            <input type="hidden" name="_token" value={csrfToken} />
                                                            <button type="submit" className="btn btn-sm btn-outline-danger fw-bold">
                                                                Từ Chối
                                                            </button>
                                                        </form>
                                                    </div>
                                                ) : (
                                                    <span className="small text-muted">Đã xử lý</span>
                                                )}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {/* Modal preview ảnh */}
            {previewImage && (
                <div className="modal show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.6)' }} onClick={() => setPreviewImage(null)}>
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content border-0 p-2 text-center bg-transparent">
                            <img src={previewImage} alt="Chứng từ" className="img-fluid rounded-4 shadow-lg mx-auto" style={{ maxHeight: '80vh' }} />
                            <button type="button" className="btn btn-light btn-sm mt-3 mx-auto fw-bold" onClick={() => setPreviewImage(null)}>
                                Đóng ảnh
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
