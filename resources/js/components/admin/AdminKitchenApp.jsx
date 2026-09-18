import React from 'react';

export default function AdminKitchenApp({
    pendingDishes = [],
    csrfToken = '',
    routes = {}
}) {
    return (
        <div className="container-fluid p-0">
            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold d-flex align-items-center gap-2">
                        <i className="bi bi-fire text-danger"></i>
                        <span>Bếp Chế Biến Món Trực Tuyến</span>
                    </h1>
                    <p className="text-muted small mb-0">Hiển thị các món ăn cần nấu ngay lập tức, gộp số lượng theo từng món để tối ưu hóa công suất bếp</p>
                </div>
                <button type="button" onClick={() => window.location.reload()} className="btn btn-sm btn-outline-danger fw-bold">
                    <i className="bi bi-arrow-clockwise me-1"></i> Làm mới đơn
                </button>
            </div>

            <div className="row g-4">
                {pendingDishes.length === 0 ? (
                    <div className="col-12">
                        <div className="card border-0 shadow-sm p-5 text-center text-muted small">
                            <i className="bi bi-check-circle-fill text-success fs-1 mb-2"></i>
                            <div>Tuyệt vời! Hiện tại bếp đã nấu xong tất cả các đơn hàng.</div>
                        </div>
                    </div>
                ) : (
                    pendingDishes.map((item, idx) => (
                        <div key={`kitchen-${idx}`} className="col-12 col-md-6 col-xl-4">
                            <div className="card border-0 shadow-sm h-100 border-top border-danger border-3">
                                <div className="card-body p-4 d-flex justify-content-between align-items-start">
                                    <div className="d-flex gap-3">
                                        <div className="p-3 rounded-3 bg-danger-subtle text-danger d-flex align-items-center justify-content-center fw-bold fs-4" style={{ width: '56px', height: '56px' }}>
                                            x{item.total_quantity || 1}
                                        </div>
                                        <div>
                                            <h5 className="fw-bold text-gray-800 mb-1">{item.dish_name || item.dish?.dish_name}</h5>
                                            <div className="small text-muted mb-2">Thuộc đơn hàng: #{item.order_ids || item.order_id}</div>
                                            {item.notes && (
                                                <div className="small text-danger fw-bold fst-italic">
                                                    Ghi chú: {item.notes}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))
                )}
            </div>
        </div>
    );
}
