import React from 'react';

export default function AdminSubscriptionsApp({
    subscriptions = [],
    csrfToken = '',
    routes = {}
}) {
    return (
        <div className="container-fluid p-0">
            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">Khách Đăng Ký Gói Ăn Uống Dài Hạn</h1>
                    <p className="text-muted small mb-0">Quản lý các suất ăn định kỳ hàng ngày và điều phối vận đơn</p>
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
                                    <th>Gói Đăng Ký</th>
                                    <th>Bắt Đầu</th>
                                    <th>Kết Thúc</th>
                                    <th>Trạng Thái</th>
                                    <th className="text-end px-4">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                {subscriptions.length === 0 ? (
                                    <tr>
                                        <td colSpan="7" className="text-center py-5 text-muted small">
                                            Chưa có lượt đăng ký gói định kỳ nào.
                                        </td>
                                    </tr>
                                ) : (
                                    subscriptions.map(sub => (
                                        <tr key={`sub-${sub.id}`}>
                                            <td className="px-4 fw-bold text-danger">#FDL-{sub.order_id}</td>
                                            <td className="fw-semibold">{sub.order?.full_name || sub.user?.name || 'Khách hàng'}</td>
                                            <td className="fw-bold text-gray-800">{sub.package?.package_name}</td>
                                            <td className="small text-muted">{sub.start_date || '---'}</td>
                                            <td className="small text-muted">{sub.end_date || '---'}</td>
                                            <td>
                                                {sub.status === 'active' && <span className="badge bg-success-subtle text-success">Đang kích hoạt</span>}
                                                {sub.status === 'paused' && <span className="badge bg-warning-subtle text-warning">Tạm ngưng</span>}
                                                {sub.status === 'expired' && <span className="badge bg-secondary-subtle text-secondary">Hết hạn</span>}
                                            </td>
                                            <td className="text-end px-4">
                                                <a href={`/admin/sub-packages/${sub.id}`} className="btn btn-sm btn-light border text-primary">
                                                    Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    );
}
