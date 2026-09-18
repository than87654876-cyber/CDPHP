import React, { useState } from 'react';

export default function RefundApp({
    refunds = [],
    initialSearch = '',
    csrfToken = '',
    routes = {},
    presetOrderId = ''
}) {
    const [search, setSearch] = useState(initialSearch || '');
    const [showForm, setShowForm] = useState(Boolean(presetOrderId));
    const [orderId, setOrderId] = useState(presetOrderId || '');
    const [reason, setReason] = useState('');
    const [bankInfo, setBankInfo] = useState('');

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    const filteredRefunds = refunds.filter(r => {
        if (!search.trim()) return true;
        const q = search.toLowerCase();
        return String(r.order_id).includes(q) || r.reason?.toLowerCase().includes(q);
    });

    const getStatusBadge = (status) => {
        switch(status) {
            case 'pending':
                return <span className="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold flex items-center gap-1.5"><i className="fas fa-clock text-amber-500"></i> Đang chờ duyệt</span>;
            case 'approved':
                return <span className="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold flex items-center gap-1.5"><i className="fas fa-check-circle text-emerald-500"></i> Đã hoàn tiền</span>;
            case 'rejected':
                return <span className="px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold flex items-center gap-1.5"><i className="fas fa-times-circle text-rose-500"></i> Đã từ chối</span>;
            default:
                return <span className="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">{status}</span>;
        }
    };

    return (
        <div className="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href={routes.shop || '/'} className="hover:text-[#ee4d2d]">Trang chủ</a>
                    <i className="fas fa-chevron-right text-[10px] text-slate-400"></i>
                    <a href={routes.trackOrder || '/tra-cuu-don-hang'} className="hover:text-[#ee4d2d]">Đơn hàng</a>
                    <i className="fas fa-chevron-right text-[10px] text-slate-400"></i>
                    <span className="text-[#ee4d2d] font-bold">Yêu cầu hoàn tiền</span>
                </div>
                <button
                    type="button"
                    onClick={() => setShowForm(!showForm)}
                    className="px-4 py-2 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs transition-colors flex items-center gap-1.5 cursor-pointer shadow-sm"
                >
                    <i className="fas fa-plus"></i> Tạo yêu cầu hoàn tiền mới
                </button>
            </div>

            {/* Header banner */}
            <div className="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4">
                <div className="flex items-center gap-3.5">
                    <div className="w-12 h-12 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center font-bold text-xl shrink-0">
                        <i className="fas fa-rotate-left"></i>
                    </div>
                    <div>
                        <h1 className="text-xl font-extrabold text-slate-900">Danh Sách Yêu Cầu Hoàn Tiền</h1>
                        <p className="text-xs text-slate-500">FOODDAILY cam kết bảo vệ quyền lợi khách hàng 100% khi có sự cố về chất lượng món ăn hoặc giao trễ</p>
                    </div>
                </div>

                {/* Search */}
                <div className="pt-2">
                    <input
                        type="text"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Tìm kiếm theo mã đơn hoặc lý do..."
                        className="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                    />
                </div>
            </div>

            {/* New Refund Request Form */}
            {showForm && (
                <div className="bg-white rounded-3xl p-6 sm:p-8 shadow-md border border-rose-100 space-y-4">
                    <h3 className="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i className="fas fa-file-invoice-dollar text-[#ee4d2d]"></i> Gửi Yêu Cầu Hoàn Tiền
                    </h3>
                    <form action={routes.refundStore || '/yeu-cau-hoan-tien'} method="POST" encType="multipart/form-data" className="space-y-4">
                        <input type="hidden" name="_token" value={csrfToken} />
                        
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Mã đơn hàng:</label>
                                <input
                                    type="text"
                                    name="order_id"
                                    required
                                    value={orderId}
                                    onChange={(e) => setOrderId(e.target.value)}
                                    placeholder="Ví dụ: 12 hoặc FDL-12"
                                    className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-[#ee4d2d]"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1">Ảnh minh chứng sự cố (nếu có):</label>
                                <input
                                    type="file"
                                    name="image"
                                    accept="image/*"
                                    className="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-[#ee4d2d]"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1">Lý do yêu cầu hoàn tiền:</label>
                            <textarea
                                name="reason"
                                required
                                rows="3"
                                value={reason}
                                onChange={(e) => setReason(e.target.value)}
                                placeholder="Mô tả chi tiết vấn đề bạn gặp phải..."
                                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 outline-none focus:border-[#ee4d2d]"
                            ></textarea>
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1">Thông tin nhận tiền hoàn (STK / Ngân hàng / Tên chủ TK hoặc Ví MoMo):</label>
                            <input
                                type="text"
                                name="bank_account"
                                required
                                value={bankInfo}
                                onChange={(e) => setBankInfo(e.target.value)}
                                placeholder="Ví dụ: Vietcombank 0123456789 - NGUYEN VAN A"
                                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-[#ee4d2d]"
                            />
                        </div>

                        <div className="flex justify-end gap-2 pt-2">
                            <button
                                type="button"
                                onClick={() => setShowForm(false)}
                                className="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold cursor-pointer"
                            >
                                Hủy
                            </button>
                            <button
                                type="submit"
                                className="px-6 py-2.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-extrabold shadow-md shadow-rose-500/25 cursor-pointer"
                            >
                                Gửi yêu cầu ngay
                            </button>
                        </div>
                    </form>
                </div>
            )}

            {/* List */}
            <div className="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4">
                <h3 className="text-sm font-black text-slate-900 uppercase tracking-wider">
                    Lịch sử khiếu nại & hoàn tiền ({filteredRefunds.length})
                </h3>

                {filteredRefunds.length === 0 ? (
                    <div className="py-8 text-center text-xs text-slate-400">
                        Bạn chưa có yêu cầu hoàn tiền nào.
                    </div>
                ) : (
                    <div className="divide-y divide-slate-100">
                        {filteredRefunds.map(ref => (
                            <div key={`refund-${ref.id}`} className="py-4 space-y-2">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <span className="font-extrabold text-xs text-[#ee4d2d]">#FDL-{ref.order_id}</span>
                                        <span className="text-[11px] text-slate-400">
                                            {ref.created_at ? new Date(ref.created_at).toLocaleDateString('vi-VN') : ''}
                                        </span>
                                    </div>
                                    <div>{getStatusBadge(ref.status)}</div>
                                </div>
                                <p className="text-xs text-slate-700 font-medium">{ref.reason}</p>
                                {ref.admin_reply && (
                                    <div className="bg-slate-50 p-3 rounded-xl text-xs text-slate-600 border border-slate-100">
                                        <strong className="text-slate-800">Phản hồi từ Admin:</strong> {ref.admin_reply}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}
