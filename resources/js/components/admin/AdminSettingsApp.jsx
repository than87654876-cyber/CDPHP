import React, { useState } from 'react';

export default function AdminSettingsApp({
    settings = {},
    csrfToken = '',
    routes = {}
}) {
    const [brandName, setBrandName] = useState(settings?.brand_name || 'FOODDAILY');
    const [hotline, setHotline] = useState(settings?.hotline || '1900 6868');
    const [address, setAddress] = useState(settings?.address || 'Quận 1, TP. Hồ Chí Minh');
    const [deliveryFee, setDeliveryFee] = useState(settings?.delivery_fee || 15000);
    const [bannerTitle, setBannerTitle] = useState(settings?.banner_title || "Đặt Đồ ăn, giao hàng từ 20'...");
    const [bannerSubtitle, setBannerSubtitle] = useState(settings?.banner_subtitle || 'Có 110.625 Địa Điểm Ở TP. HCM Từ 00:00 - 23:59');

    return (
        <div className="container-fluid p-0">
            <div className="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 gap-3">
                <div>
                    <h1 className="h3 mb-1 text-gray-800 fw-bold">Cấu Hình Hệ Thống</h1>
                    <p className="text-muted small mb-0">Quản lý thương hiệu, số hotline, phí giao hàng và thông điệp hiển thị trang chủ</p>
                </div>
            </div>

            <div className="card border-0 shadow-sm">
                <div className="card-body p-4">
                    <form action={routes.updateSettings || '/admin/settings'} method="POST" encType="multipart/form-data">
                        <input type="hidden" name="_token" value={csrfToken} />

                        <div className="row g-4">
                            <div className="col-md-6">
                                <label className="form-label small fw-bold text-gray-700">Tên Thương Hiệu:</label>
                                <input
                                    type="text"
                                    name="brand_name"
                                    value={brandName}
                                    onChange={(e) => setBrandName(e.target.value)}
                                    className="form-control form-control-sm"
                                    required
                                />
                            </div>

                            <div className="col-md-6">
                                <label className="form-label small fw-bold text-gray-700">Hotline Hỗ Trợ:</label>
                                <input
                                    type="text"
                                    name="hotline"
                                    value={hotline}
                                    onChange={(e) => setHotline(e.target.value)}
                                    className="form-control form-control-sm"
                                    required
                                />
                            </div>

                            <div className="col-md-6">
                                <label className="form-label small fw-bold text-gray-700">Địa Chỉ Cửa Hàng:</label>
                                <input
                                    type="text"
                                    name="address"
                                    value={address}
                                    onChange={(e) => setAddress(e.target.value)}
                                    className="form-control form-control-sm"
                                    required
                                />
                            </div>

                            <div className="col-md-6">
                                <label className="form-label small fw-bold text-gray-700">Phí Giao Hàng Cơ Bản (VNĐ):</label>
                                <input
                                    type="number"
                                    name="delivery_fee"
                                    value={deliveryFee}
                                    onChange={(e) => setDeliveryFee(e.target.value)}
                                    className="form-control form-control-sm"
                                    required
                                />
                            </div>

                            <div className="col-12">
                                <label className="form-label small fw-bold text-gray-700">Tiêu Đề Banner Trang Chủ:</label>
                                <input
                                    type="text"
                                    name="banner_title"
                                    value={bannerTitle}
                                    onChange={(e) => setBannerTitle(e.target.value)}
                                    className="form-control form-control-sm"
                                />
                            </div>

                            <div className="col-12">
                                <label className="form-label small fw-bold text-gray-700">Phụ Đề Banner Trang Chủ:</label>
                                <input
                                    type="text"
                                    name="banner_subtitle"
                                    value={bannerSubtitle}
                                    onChange={(e) => setBannerSubtitle(e.target.value)}
                                    className="form-control form-control-sm"
                                />
                            </div>

                            <div className="col-12">
                                <label className="form-label small fw-bold text-gray-700">Ảnh Banner Mới (tùy chọn):</label>
                                <input
                                    type="file"
                                    name="banner_image"
                                    accept="image/*"
                                    className="form-control form-control-sm"
                                />
                            </div>

                            <div className="col-12 d-flex justify-content-end">
                                <button type="submit" className="btn btn-danger btn-sm fw-bold px-4">
                                    <i className="bi bi-check-lg me-1"></i> Lưu Cấu Hình
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}
