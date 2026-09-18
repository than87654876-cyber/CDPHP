import React, { useState } from 'react';

export default function AdminDishesApp({
    dishes = [],
    categories = [],
    csrfToken = '',
    routes = {}
}) {
    const [selectedCategory, setSelectedCategory] = useState('all');
    const [search, setSearch] = useState('');

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    const filteredDishes = dishes.filter(d => {
        if (selectedCategory !== 'all' && String(d.category_id) !== String(selectedCategory)) {
            return false;
        }
        if (search.trim() && !d.dish_name?.toLowerCase().includes(search.toLowerCase())) {
            return false;
        }
        return true;
    });

    return (
        <div className="container-fluid p-0">
            {/* Header */}
            <div className="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">Quản Lý Món Ăn</h1>
                    <p className="text-muted small mb-0">Quản lý thực đơn, cập nhật giá bán, hình ảnh và trạng thái còn món</p>
                </div>

                <a href={routes.createDish || '/admin/single-dishes/create'} className="btn btn-danger btn-sm fw-bold shadow-sm">
                    <i className="bi bi-plus-lg me-1"></i> Thêm Món Ăn Mới
                </a>
            </div>

            {/* Filter controls */}
            <div className="card border-0 shadow-sm mb-4">
                <div className="card-body p-3">
                    <div className="row g-3">
                        <div className="col-md-6 col-lg-4">
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Tìm kiếm theo tên món ăn..."
                                className="form-control form-control-sm"
                            />
                        </div>
                        <div className="col-md-6 col-lg-4">
                            <select
                                value={selectedCategory}
                                onChange={(e) => setSelectedCategory(e.target.value)}
                                className="form-select form-select-sm"
                            >
                                <option value="all">Tất cả danh mục ({dishes.length})</option>
                                {categories.map(cat => (
                                    <option key={cat.id} value={cat.id}>{cat.category_name}</option>
                                ))}
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {/* Table */}
            <div className="card border-0 shadow-sm">
                <div className="card-body p-0">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light">
                                <tr>
                                    <th className="py-3 px-4">Ảnh</th>
                                    <th>Tên Món Ăn</th>
                                    <th>Danh Mục</th>
                                    <th>Đơn Giá</th>
                                    <th>Đã Bán</th>
                                    <th>Đánh Giá</th>
                                    <th>Trạng Thái</th>
                                    <th className="text-end px-4">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filteredDishes.length === 0 ? (
                                    <tr>
                                        <td colSpan="8" className="text-center py-5 text-muted small">
                                            Không có món ăn nào phù hợp với tìm kiếm.
                                        </td>
                                    </tr>
                                ) : (
                                    filteredDishes.map(dish => (
                                        <tr key={`dish-${dish.id}`}>
                                            <td className="px-4" style={{ width: '60px' }}>
                                                <img
                                                    src={dish.image?.startsWith('http') ? dish.image : `/${dish.image || 'logo.jpg'}`}
                                                    alt={dish.dish_name}
                                                    className="rounded-3 object-fit-cover"
                                                    style={{ width: '48px', height: '48px' }}
                                                />
                                            </td>
                                            <td className="fw-bold text-gray-800">{dish.dish_name}</td>
                                            <td>
                                                <span className="badge bg-light text-dark border">
                                                    {dish.category?.category_name || 'Khác'}
                                                </span>
                                            </td>
                                            <td className="fw-bold text-danger">{formatPrice(dish.price)}</td>
                                            <td className="small text-muted">{dish.sold_count || 0} suất</td>
                                            <td className="small text-warning fw-bold">
                                                ⭐ {Number(dish.rating_score || 5).toFixed(1)}
                                            </td>
                                            <td>
                                                {dish.is_available ? (
                                                    <span className="badge bg-success-subtle text-success">Còn hàng</span>
                                                ) : (
                                                    <span className="badge bg-secondary-subtle text-secondary">Hết món</span>
                                                )}
                                            </td>
                                            <td className="text-end px-4">
                                                <div className="btn-group">
                                                    <a href={`/admin/single-dishes/${dish.id}/edit`} className="btn btn-sm btn-light border text-primary" title="Sửa món">
                                                        <i className="bi bi-pencil"></i>
                                                    </a>
                                                    <form action={`/admin/single-dishes/${dish.id}`} method="POST" onSubmit={(e) => { if (!window.confirm('Bạn có chắc muốn xóa món ăn này?')) e.preventDefault(); }}>
                                                        <input type="hidden" name="_token" value={csrfToken} />
                                                        <input type="hidden" name="_method" value="DELETE" />
                                                        <button type="submit" className="btn btn-sm btn-light border text-danger" title="Xóa món">
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
