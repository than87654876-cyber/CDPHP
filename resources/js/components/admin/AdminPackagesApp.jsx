import React, { useState } from 'react';

export default function AdminPackagesApp({
    packages = [],
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
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">Gói Dịch Vụ / Combo Ăn Uống</h1>
                    <p className="text-muted small mb-0">Quản lý các gói ăn theo tuần, theo tháng hoặc combo tiết kiệm</p>
                </div>

                <a href={routes.createPackage || '/admin/packages/create'} className="btn btn-danger btn-sm fw-bold shadow-sm">
                    <i className="bi bi-plus-lg me-1"></i> Thêm Gói Mới
                </a>
            </div>

            <div className="row g-4">
                {packages.length === 0 ? (
                    <div className="col-12">
                        <div className="card border-0 shadow-sm p-5 text-center text-muted small">
                            Chưa có gói dịch vụ nào được cấu hình.
                        </div>
                    </div>
                ) : (
                    packages.map(pkg => (
                        <div key={`pkg-${pkg.id}`} className="col-12 col-md-6 col-xl-4">
                            <div className="card border-0 shadow-sm h-100">
                                <div className="card-body p-4 d-flex flex-col justify-content-between">
                                    <div>
                                        <div className="d-flex align-items-center justify-content-between mb-2">
                                            <span className="badge bg-danger-subtle text-danger fw-bold">
                                                {pkg.duration_days || 7} Ngày
                                            </span>
                                            <span className="small text-muted fw-bold">#{pkg.id}</span>
                                        </div>
                                        <h5 className="fw-bold text-gray-800 mb-2">{pkg.package_name}</h5>
                                        <p className="small text-muted mb-3" style={{ minHeight: '40px' }}>
                                            {pkg.description || 'Gói ăn dinh dưỡng tiện lợi mỗi ngày.'}
                                        </p>
                                        <div className="h4 fw-bold text-danger mb-0">
                                            {formatPrice(pkg.price)}
                                        </div>
                                    </div>

                                    <div className="d-flex justify-content-end gap-2 pt-3 border-top mt-3">
                                        <a href={`/admin/packages/${pkg.id}/edit`} className="btn btn-sm btn-light border text-primary">
                                            <i className="bi bi-pencil me-1"></i> Sửa
                                        </a>
                                        <form action={`/admin/packages/${pkg.id}`} method="POST" onSubmit={(e) => { if (!window.confirm('Xóa gói combo này?')) e.preventDefault(); }}>
                                            <input type="hidden" name="_token" value={csrfToken} />
                                            <input type="hidden" name="_method" value="DELETE" />
                                            <button type="submit" className="btn btn-sm btn-light border text-danger">
                                                <i className="bi bi-trash"></i>
                                            </button>
                                        </form>
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
