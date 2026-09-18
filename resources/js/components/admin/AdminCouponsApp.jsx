import React from 'react';

export default function AdminCouponsApp({
    coupons = [],
    csrfToken = '',
    routes = {}
}) {
    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    return (
        <div className="container-fluid p-0">
            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">Mã Khuyến Mãi / Giảm Giá</h1>
                    <p className="text-muted small mb-0">Tạo mã voucher kích cầu mua sắm và hỗ trợ chương trình marketing</p>
                </div>

                <a href={routes.createCoupon || '/admin/promotion/create'} className="btn btn-danger btn-sm fw-bold shadow-sm">
                    <i className="bi bi-plus-lg me-1"></i> Tạo Mã Mới
                </a>
            </div>

            <div className="card border-0 shadow-sm">
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th className="py-3 px-4">Mã Voucher</th>
                                    <th>Mức Giảm</th>
                                    <th>Áp Dụng Cho Đơn Từ</th>
                                    <th>Lượt Sử Dụng</th>
                                    <th>Ngày Hết Hạn</th>
                                    <th className="text-end px-4">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                {coupons.length === 0 ? (
                                    <tr>
                                        <td colSpan="6" className="text-center py-5 text-muted small">
                                            Chưa có mã giảm giá nào.
                                        </td>
                                    </tr>
                                ) : (
                                    coupons.map(c => (
                                        <tr key={`cp-${c.id}`}>
                                            <td className="px-4 fw-bold font-monospace text-danger">
                                                <i className="bi bi-ticket-perforated me-2"></i>
                                                {c.code}
                                            </td>
                                            <td className="fw-bold">
                                                {c.discount_type === 'percent' ? `${c.discount_value}%` : formatPrice(c.discount_value)}
                                            </td>
                                            <td className="small text-muted">{formatPrice(c.min_order_value || 0)}</td>
                                            <td className="small text-muted">{c.used_count || 0} / {c.usage_limit || '∞'}</td>
                                            <td className="small text-muted">{c.expires_at || 'Vô thời hạn'}</td>
                                            <td className="text-end px-4">
                                                <a href={`/admin/promotion/${c.id}/edit`} className="btn btn-sm btn-light border text-primary">
                                                    <i className="bi bi-pencil"></i>
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
