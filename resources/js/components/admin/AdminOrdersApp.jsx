import React, { useState } from 'react';

export default function AdminOrdersApp({
    orders = [],
    csrfToken = '',
    routes = {}
}) {
    const [selectedStatus, setSelectedStatus] = useState('all');
    const [search, setSearch] = useState('');

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    const filteredOrders = orders.filter(ord => {
        const matchesStatus = selectedStatus === 'all' || ord.order_status === selectedStatus;
        if (!matchesStatus) return false;

        if (!search.trim()) return true;
        const q = search.toLowerCase();
        const matchesId = String(ord.id).includes(q) || `fdl-${ord.id}`.includes(q);
        const matchesName = (ord.full_name || ord.user?.name || '').toLowerCase().includes(q);
        const matchesPhone = (ord.phone || '').includes(q);
        return matchesId || matchesName || matchesPhone;
    });

    const getStatusBadge = (status) => {
        switch(status) {
            case 'pending':
                return <span className="badge bg-warning text-dark"><i className="bi bi-hourglass-split me-1"></i>Chờ xác nhận</span>;
            case 'confirmed':
            case 'cooking':
                return <span className="badge bg-info text-dark"><i className="bi bi-fire me-1"></i>Đang nấu</span>;
            case 'delivering':
                return <span className="badge bg-primary"><i className="bi bi-bicycle me-1"></i>Đang giao</span>;
            case 'completed':
                return <span className="badge bg-success"><i className="bi bi-check2-circle me-1"></i>Hoàn thành</span>;
            case 'cancelled':
                return <span className="badge bg-danger"><i className="bi bi-x-circle me-1"></i>Đã hủy</span>;
            default:
                return <span className="badge bg-secondary">{status}</span>;
        }
    };

    return (
        <div className="container-fluid p-0">
            {/* Header */}
            <div className="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">Quản Lý Đơn Hàng</h1>
                    <p className="text-muted small mb-0">Theo dõi, điều phối tiến trình đơn hàng và cập nhật trạng thái chế biến</p>
                </div>

                <div className="d-flex gap-2">
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Tìm mã đơn, tên khách, SĐT..."
                        className="form-control form-control-sm shadow-sm"
                        style={{ maxWidth: '260px' }}
                    />
                </div>
            </div>

            {/* Status Filter Tabs */}
            <div className="card border-0 shadow-sm mb-4">
                <div className="card-body p-2">
                    <ul className="nav nav-pills nav-fill gap-1">
                        <li className="nav-item">
                            <button
                                type="button"
                                onClick={() => setSelectedStatus('all')}
                                className={`nav-link py-2 small fw-bold ${selectedStatus === 'all' ? 'active bg-danger text-white' : 'text-muted'}`}
                            >
                                Tất cả ({orders.length})
                            </button>
                        </li>
                        <li className="nav-item">
                            <button
                                type="button"
                                onClick={() => setSelectedStatus('pending')}
                                className={`nav-link py-2 small fw-bold ${selectedStatus === 'pending' ? 'active bg-warning text-dark' : 'text-muted'}`}
                            >
                                Chờ xác nhận ({orders.filter(o => o.order_status === 'pending').length})
                            </button>
                        </li>
                        <li className="nav-item">
                            <button
                                type="button"
                                onClick={() => setSelectedStatus('confirmed')}
                                className={`nav-link py-2 small fw-bold ${selectedStatus === 'confirmed' ? 'active bg-info text-dark' : 'text-muted'}`}
                            >
                                Đang nấu ({orders.filter(o => o.order_status === 'confirmed').length})
                            </button>
                        </li>
                        <li className="nav-item">
                            <button
                                type="button"
                                onClick={() => setSelectedStatus('delivering')}
                                className={`nav-link py-2 small fw-bold ${selectedStatus === 'delivering' ? 'active bg-primary text-white' : 'text-muted'}`}
                            >
                                Đang giao ({orders.filter(o => o.order_status === 'delivering').length})
                            </button>
                        </li>
                        <li className="nav-item">
                            <button
                                type="button"
                                onClick={() => setSelectedStatus('completed')}
                                className={`nav-link py-2 small fw-bold ${selectedStatus === 'completed' ? 'active bg-success text-white' : 'text-muted'}`}
                            >
                                Hoàn thành ({orders.filter(o => o.order_status === 'completed').length})
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            {/* Table */}
            <div className="card border-0 shadow-sm">
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th className="py-3 px-4">Mã Đơn</th>
                                    <th>Khách Hàng</th>
                                    <th>Số Điện Thoại</th>
                                    <th>Địa Chỉ</th>
                                    <th>Tổng Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th>Thời Gian</th>
                                    <th className="text-end px-4">Đổi Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredOrders.length === 0 ? (
                                    <tr>
                                        <td colSpan="8" className="text-center py-5 text-muted small">
                                            Không có đơn hàng nào phù hợp với bộ lọc hiện tại.
                                        </td>
                                    </tr>
                                ) : (
                                    filteredOrders.map(ord => (
                                        <tr key={`ord-${ord.id}`}>
                                            <td className="px-4 fw-bold text-danger">
                                                <a href={`/admin/orders/${ord.id}`} className="text-decoration-none text-danger">
                                                    #FDL-{ord.id}
                                                </a>
                                            </td>
                                            <td className="fw-semibold">{ord.full_name || ord.user?.name || 'Khách vãng lai'}</td>
                                            <td className="small text-muted">{ord.phone || '---'}</td>
                                            <td className="small text-muted text-truncate" style={{ maxWidth: '200px' }}>
                                                {ord.address || '---'}
                                            </td>
                                            <td className="fw-bold">{formatPrice(ord.final_amount || ord.total_amount)}</td>
                                            <td>{getStatusBadge(ord.order_status)}</td>
                                            <td className="small text-muted">
                                                {ord.created_at ? new Date(ord.created_at).toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit' }) : ''}
                                            </td>
                                            <td className="text-end px-4">
                                                <form action={`/admin/orders/${ord.id}/status`} method="POST" className="d-inline-block">
                                                    <input type="hidden" name="_token" value={csrfToken} />
                                                    <select
                                                        name="order_status"
                                                        defaultValue={ord.order_status}
                                                        onChange={(e) => e.target.form.submit()}
                                                        className="form-select form-select-sm border shadow-none"
                                                        style={{ width: '140px' }}
                                                    >
                                                        <option value="pending">Chờ xác nhận</option>
                                                        <option value="confirmed">Đang nấu</option>
                                                        <option value="delivering">Đang giao</option>
                                                        <option value="completed">Hoàn thành</option>
                                                        <option value="cancelled">Hủy đơn</option>
                                                    </select>
                                                </form>
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
