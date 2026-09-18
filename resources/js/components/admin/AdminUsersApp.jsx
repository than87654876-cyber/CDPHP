import React, { useState } from 'react';

export default function AdminUsersApp({
    users = [],
    roleType = 'customers', // 'customers' | 'employees'
    csrfToken = '',
    routes = {}
}) {
    const [search, setSearch] = useState('');

    const filtered = users.filter(u => {
        if (!search.trim()) return true;
        const q = search.toLowerCase();
        return u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q) || u.phone?.includes(q);
    });

    return (
        <div className="container-fluid p-0">
            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">
                        {roleType === 'employees' ? 'Quản Lý Nhân Viên & Phân Quyền' : 'Quản Lý Khách Hàng & Thành Viên'}
                    </h1>
                    <p className="text-muted small mb-0">
                        {roleType === 'employees' ? 'Danh sách nhân sự, phân vai trò Bếp, CSKH, Shipper và Quản trị' : 'Danh sách khách hàng, lịch sử tích điểm và thông tin liên hệ'}
                    </p>
                </div>

                {roleType === 'employees' && (
                    <a href={routes.createEmployee || '/admin/employees/create'} className="btn btn-danger btn-sm fw-bold shadow-sm">
                        <i className="bi bi-person-plus me-1"></i> Thêm Nhân Viên Mới
                    </a>
                )}
            </div>

            <div className="card border-0 shadow-sm">
                <div className="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Tìm tên, email, SĐT..."
                        className="form-control form-control-sm"
                        style={{ maxWidth: '280px' }}
                    />
                    <span className="small text-muted fw-bold">Tổng: {users.length} tài khoản</span>
                </div>
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th className="py-3 px-4">Họ và Tên</th>
                                    <th>Email</th>
                                    <th>Số Điện Thoại</th>
                                    <th>Vai Trò</th>
                                    <th>Ngày Tạo</th>
                                    <th className="text-end px-4">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.length === 0 ? (
                                    <tr>
                                        <td colSpan="6" className="text-center py-5 text-muted small">
                                            Không tìm thấy tài khoản phù hợp.
                                        </td>
                                    </tr>
                                ) : (
                                    filtered.map(u => (
                                        <tr key={`user-${u.id}`}>
                                            <td className="px-4 fw-bold text-gray-800">
                                                <div className="d-flex align-items-center gap-2">
                                                    <div className="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center fw-bold" style={{ width: '36px', height: '36px' }}>
                                                        {u.name?.charAt(0).toUpperCase() || 'U'}
                                                    </div>
                                                    <div>
                                                        <div>{u.name}</div>
                                                        <div className="small text-muted fw-normal">ID #{u.id}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="small text-muted">{u.email}</td>
                                            <td className="small text-muted">{u.phone || '---'}</td>
                                            <td>
                                                {u.role === 'admin' && <span className="badge bg-danger">Quản trị viên</span>}
                                                {u.role === 'staff' && <span className="badge bg-primary">Nhân viên</span>}
                                                {u.role === 'chef' && <span className="badge bg-warning text-dark">Đầu bếp</span>}
                                                {u.role === 'shipper' && <span className="badge bg-info text-dark">Shipper</span>}
                                                {(!u.role || u.role === 'customer') && <span className="badge bg-light text-dark border">Khách hàng</span>}
                                            </td>
                                            <td className="small text-muted">
                                                {u.created_at ? new Date(u.created_at).toLocaleDateString('vi-VN') : '---'}
                                            </td>
                                            <td className="text-end px-4">
                                                <a href={roleType === 'employees' ? `/admin/employees/${u.id}/edit` : `/admin/customers/${u.id}/edit`} className="btn btn-sm btn-light border text-primary" title="Sửa thông tin">
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
