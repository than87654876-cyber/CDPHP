import React, { useState } from 'react';

export default function AdminCategoriesApp({
    categories = [],
    csrfToken = '',
    routes = {}
}) {
    const [search, setSearch] = useState('');
    const [modalCategory, setModalCategory] = useState(null); // null or { id, category_name, description }

    const filtered = categories.filter(c => {
        if (!search.trim()) return true;
        return c.category_name?.toLowerCase().includes(search.toLowerCase());
    });

    return (
        <div className="container-fluid p-0">
            {/* Header */}
            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">Danh Mục Món Ăn</h1>
                    <p className="text-muted small mb-0">Phân loại món ăn theo các nhóm thực đơn giúp khách hàng chọn lựa dễ dàng</p>
                </div>

                <a href={routes.createCategory || '/admin/category/create'} className="btn btn-danger btn-sm fw-bold shadow-sm">
                    <i className="bi bi-plus-lg me-1"></i> Thêm Danh Mục Mới
                </a>
            </div>

            {/* Table Card */}
            <div className="card border-0 shadow-sm">
                <div className="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Tìm tên danh mục..."
                        className="form-control form-control-sm"
                        style={{ maxWidth: '260px' }}
                    />
                    <span className="small text-muted fw-bold">Tổng: {categories.length} danh mục</span>
                </div>
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th className="py-3 px-4" style={{ width: '80px' }}>Mã DM</th>
                                    <th>Tên Danh Mục</th>
                                    <th>Mô Tả</th>
                                    <th>Số Lượng Món</th>
                                    <th className="text-end px-4">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.length === 0 ? (
                                    <tr>
                                        <td colSpan="5" className="text-center py-5 text-muted small">
                                            Không có danh mục nào phù hợp.
                                        </td>
                                    </tr>
                                ) : (
                                    filtered.map(cat => (
                                        <tr key={`cat-${cat.id}`}>
                                            <td className="px-4 fw-bold text-danger">#{cat.id}</td>
                                            <td className="fw-bold text-gray-800">{cat.category_name}</td>
                                            <td className="small text-muted text-truncate" style={{ maxWidth: '300px' }}>
                                                {cat.description || 'Chưa có mô tả'}
                                            </td>
                                            <td>
                                                <span className="badge bg-danger-subtle text-danger">
                                                    {cat.dishes_count ?? (cat.dishes?.length || 0)} món
                                                </span>
                                            </td>
                                            <td className="text-end px-4">
                                                <div className="btn-group">
                                                    <a href={`/admin/category/${cat.id}/edit`} className="btn btn-sm btn-light border text-primary" title="Sửa danh mục">
                                                        <i className="bi bi-pencil"></i>
                                                    </a>
                                                    <form action={`/admin/category/${cat.id}`} method="POST" onSubmit={(e) => { if (!window.confirm('Bạn có chắc muốn xóa danh mục này?')) e.preventDefault(); }}>
                                                        <input type="hidden" name="_token" value={csrfToken} />
                                                        <input type="hidden" name="_method" value="DELETE" />
                                                        <button type="submit" className="btn btn-sm btn-light border text-danger" title="Xóa danh mục">
                                                            <i className="bi bi-trash"></i>
                                                        </button>
                                                    </form>
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
        </div>
    );
}
