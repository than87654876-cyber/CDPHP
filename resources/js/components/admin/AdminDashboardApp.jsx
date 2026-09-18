import React, { useState } from 'react';

export default function AdminDashboardApp({
    monthlyRevenue = 0,
    totalOrders = 0,
    pendingOrders = 0,
    completedOrders = 0,
    recentOrders = [],
    weatherData = null,
    routes = {},
    csrfToken = ''
}) {
    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    return (
        <div className="container-fluid p-0">
            {/* Page Header */}
            <div className="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">Tổng Quan Hoạt Động FOODDAILY</h1>
                    <p className="text-muted small mb-0">Theo dõi doanh thu kinh doanh, đơn hàng thời gian thực và vận hành nhà bếp</p>
                </div>

                <div className="btn-group shadow-sm">
                    <button type="button" className="btn btn-dark btn-sm dropdown-toggle fw-bold" data-bs-toggle="dropdown" aria-expanded="false">
                        <i className="bi bi-download me-1 text-warning"></i> Xuất Báo Cáo (CSV/Excel)
                    </button>
                    <ul className="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a className="dropdown-item small" href={routes.exportOrders || '#'}><i className="bi bi-file-earmark-excel text-success me-2"></i>Đơn hàng</a></li>
                        <li><a className="dropdown-item small" href={routes.exportCustomers || '#'}><i className="bi bi-people text-primary me-2"></i>Khách hàng</a></li>
                        <li><a className="dropdown-item small" href={routes.exportDishes || '#'}><i className="bi bi-cup-hot text-danger me-2"></i>Món bán chạy</a></li>
                        <li><hr className="dropdown-divider" /></li>
                        <li><a className="dropdown-item small" href={routes.exportRefunds || '#'}><i className="bi bi-arrow-counterclockwise text-warning me-2"></i>Lịch sử hoàn tiền</a></li>
                    </ul>
                </div>
            </div>

            {/* AI Weather Business Assistant Card */}
            <div className="card text-white mb-4 border-0 shadow-sm" style={{ background: 'linear-gradient(135deg, #ee4d2d 0%, #ff7355 100%)' }}>
                <div className="card-body p-4">
                    <div className="row align-items-center">
                        <div className="col-md-9">
                            <div className="d-flex align-items-center gap-2 mb-2">
                                <span className="badge bg-warning text-dark fw-bold text-uppercase">
                                    <i className="bi bi-cloud-sun me-1"></i> AI Weather Insight
                                </span>
                                <span className="small opacity-75">Tự động tối ưu menu theo điều kiện thời tiết</span>
                            </div>
                            <h5 className="fw-bold mb-1">
                                {weatherData?.recommendation || 'Hôm nay thời tiết đẹp, dự báo lượng đặt cơm trưa văn phòng và trà sữa sẽ tăng mạnh!'}
                            </h5>
                            <p className="small mb-0 opacity-75">
                                Gợi ý nhà bếp: Chuẩn bị sẵn nguyên liệu tươi cho các món canh nóng và nước ép thanh nhiệt.
                            </p>
                        </div>
                        <div className="col-md-3 text-md-end mt-3 mt-md-0">
                            <span className="display-6 fw-bold">31°C</span>
                            <div className="small opacity-75">TP. Hồ Chí Minh</div>
                        </div>
                    </div>
                </div>
            </div>

            {/* KPI Cards Row */}
            <div className="row g-3 mb-4">
                {/* Doanh thu tháng */}
                <div className="col-12 col-sm-6 col-xl-3">
                    <div className="card border-0 shadow-sm h-100 border-start border-danger border-4">
                        <div className="card-body">
                            <div className="d-flex align-items-center justify-content-between">
                                <div>
                                    <div className="text-uppercase fw-bold text-muted small">Doanh Thu Tháng</div>
                                    <div className="h4 mb-0 fw-bold text-danger mt-1">{formatPrice(monthlyRevenue)}</div>
                                </div>
                                <div className="p-3 bg-danger-subtle rounded-3 text-danger">
                                    <i className="bi bi-cash-coin fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Tổng đơn hàng */}
                <div className="col-12 col-sm-6 col-xl-3">
                    <div className="card border-0 shadow-sm h-100 border-start border-primary border-4">
                        <div className="card-body">
                            <div className="d-flex align-items-center justify-content-between">
                                <div>
                                    <div className="text-uppercase fw-bold text-muted small">Tổng Đơn Hàng</div>
                                    <div className="h4 mb-0 fw-bold text-gray-800 mt-1">{totalOrders}</div>
                                </div>
                                <div className="p-3 bg-primary-subtle rounded-3 text-primary">
                                    <i className="bi bi-bag-check fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Đơn chờ xác nhận */}
                <div className="col-12 col-sm-6 col-xl-3">
                    <div className="card border-0 shadow-sm h-100 border-start border-warning border-4">
                        <div className="card-body">
                            <div className="d-flex align-items-center justify-content-between">
                                <div>
                                    <div className="text-uppercase fw-bold text-muted small">Chờ Bếp Nấu</div>
                                    <div className="h4 mb-0 fw-bold text-warning mt-1">{pendingOrders}</div>
                                </div>
                                <div className="p-3 bg-warning-subtle rounded-3 text-warning">
                                    <i className="bi bi-clock-history fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Đơn hoàn thành */}
                <div className="col-12 col-sm-6 col-xl-3">
                    <div className="card border-0 shadow-sm h-100 border-start border-success border-4">
                        <div className="card-body">
                            <div className="d-flex align-items-center justify-content-between">
                                <div>
                                    <div className="text-uppercase fw-bold text-muted small">Giao Thành Công</div>
                                    <div className="h4 mb-0 fw-bold text-success mt-1">{completedOrders}</div>
                                </div>
                                <div className="p-3 bg-success-subtle rounded-3 text-success">
                                    <i className="bi bi-check2-circle fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Recent Orders Card */}
            <div className="card border-0 shadow-sm mb-4">
                <div className="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                    <h6 className="m-0 fw-bold text-gray-800">
                        <i className="bi bi-receipt me-2 text-danger"></i>Đơn Hàng Mới Nhất
                    </h6>
                    <a href={routes.orders || '/admin/orders'} className="btn btn-sm btn-outline-danger fw-bold">
                        Xem tất cả đơn
                    </a>
                </div>
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th className="py-3 px-4">Mã Đơn</th>
                                    <th>Khách Hàng</th>
                                    <th>Số Điện Thoại</th>
                                    <th>Tổng Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th>Thời Gian</th>
                                    <th className="text-end px-4">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                {recentOrders.length === 0 ? (
                                    <tr>
                                        <td colSpan="7" className="text-center py-4 text-muted small">
                                            Chưa có đơn hàng nào phát sinh.
                                        </td>
                                    </tr>
                                ) : (
                                    recentOrders.map(ord => (
                                        <tr key={`recent-${ord.id}`}>
                                            <td className="px-4 fw-bold text-danger">#FDL-{ord.id}</td>
                                            <td className="fw-semibold">{ord.full_name || ord.user?.name || 'Khách vãng lai'}</td>
                                            <td className="small text-muted">{ord.phone || '---'}</td>
                                            <td className="fw-bold">{formatPrice(ord.final_amount || ord.total_amount)}</td>
                                            <td>
                                                {ord.order_status === 'pending' && <span className="badge bg-warning text-dark">Chờ xác nhận</span>}
                                                {ord.order_status === 'confirmed' && <span className="badge bg-info text-dark">Đang nấu</span>}
                                                {ord.order_status === 'delivering' && <span className="badge bg-primary">Đang giao</span>}
                                                {ord.order_status === 'completed' && <span className="badge bg-success">Hoàn thành</span>}
                                                {ord.order_status === 'cancelled' && <span className="badge bg-danger">Đã hủy</span>}
                                            </td>
                                            <td className="small text-muted">
                                                {ord.created_at ? new Date(ord.created_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) : ''}
                                            </td>
                                            <td className="text-end px-4">
                                                <a href={`/admin/orders/${ord.id}`} className="btn btn-sm btn-light text-primary border">
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
