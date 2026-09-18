import React from 'react';

export default function StaffWorkspaceApp({
    stats = {},
    user = {},
    routes = {}
}) {
    return (
        <div className="container-fluid p-0">
            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">Không Gian Làm Việc Nhân Viên</h1>
                    <p className="text-muted small mb-0">Xin chào, <strong>{user?.name}</strong>! Cổng thao tác nhanh theo vai trò ca trực</p>
                </div>
            </div>

            <div className="row g-4">
                {/* Kitchen shortcut */}
                <div className="col-12 col-md-4">
                    <div className="card border-0 shadow-sm h-100">
                        <div className="card-body p-4 text-center">
                            <div className="p-3 bg-danger-subtle text-danger rounded-circle d-inline-flex mb-3">
                                <i className="bi bi-fire fs-2"></i>
                            </div>
                            <h5 className="fw-bold mb-2">Bếp Nấu</h5>
                            <p className="small text-muted mb-4">Xem danh sách các món cần nấu cho các đơn hàng đang chờ</p>
                            <a href="/admin/kitchen-report" className="btn btn-outline-danger btn-sm fw-bold w-100">
                                Mở Bếp Nấu
                            </a>
                        </div>
                    </div>
                </div>

                {/* Orders shortcut */}
                <div className="col-12 col-md-4">
                    <div className="card border-0 shadow-sm h-100">
                        <div className="card-body p-4 text-center">
                            <div className="p-3 bg-primary-subtle text-primary rounded-circle d-inline-flex mb-3">
                                <i className="bi bi-bicycle fs-2"></i>
                            </div>
                            <h5 className="fw-bold mb-2">Điều Phối Giao Hàng</h5>
                            <p className="small text-muted mb-4">Theo dõi đơn hàng cần bàn giao cho tài xế và kiểm tra địa chỉ</p>
                            <a href="/admin/orders" className="btn btn-outline-primary btn-sm fw-bold w-100">
                                Điều Phối Đơn
                            </a>
                        </div>
                    </div>
                </div>

                {/* Support shortcut */}
                <div className="col-12 col-md-4">
                    <div className="card border-0 shadow-sm h-100">
                        <div className="card-body p-4 text-center">
                            <div className="p-3 bg-warning-subtle text-warning rounded-circle d-inline-flex mb-3">
                                <i className="bi bi-headset fs-2"></i>
                            </div>
                            <h5 className="fw-bold mb-2">Chăm Sóc Khách Hàng</h5>
                            <p className="small text-muted mb-4">Tiếp nhận khiếu nại, phản hồi đánh giá và xử lý yêu cầu đổi trả</p>
                            <a href="/admin/refunds" className="btn btn-outline-warning btn-sm fw-bold w-100">
                                Xử Lý Khiếu Nại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
