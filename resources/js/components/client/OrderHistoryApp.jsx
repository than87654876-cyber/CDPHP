import React, { useState } from 'react';

export default function OrderHistoryApp({
    orders = [],
    initialSearch = '',
    csrfToken = '',
    routes = {}
}) {
    const [search, setSearch] = useState(initialSearch || '');

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    const filteredOrders = orders.filter(ord => {
        if (!search.trim()) return true;
        const q = search.toLowerCase();
        const matchesId = String(ord.id).includes(q) || `fdl-${ord.id}`.toLowerCase().includes(q);
        const matchesDish = ord.order_items?.some(it => it.dish?.dish_name?.toLowerCase().includes(q));
        return matchesId || matchesDish;
    });

    const getStatusBadge = (status) => {
        switch(status) {
            case 'pending':
                return <span className="px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-extrabold flex items-center gap-1.5"><i className="fas fa-spinner fa-spin text-amber-500"></i> Chờ xác nhận</span>;
            case 'confirmed':
            case 'cooking':
                return <span className="px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-extrabold flex items-center gap-1.5"><i className="fas fa-fire-burner text-blue-500"></i> Đang nấu</span>;
            case 'delivering':
                return <span className="px-3 py-1 rounded-full bg-purple-50 border border-purple-200 text-purple-700 text-xs font-extrabold flex items-center gap-1.5"><i className="fas fa-motorcycle text-purple-500"></i> Đang giao</span>;
            case 'completed':
                return <span className="px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-extrabold flex items-center gap-1.5"><i className="fas fa-check-circle text-emerald-500"></i> Đã giao thành công</span>;
            case 'cancelled':
                return <span className="px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-extrabold flex items-center gap-1.5"><i className="fas fa-times-circle text-rose-500"></i> Đã hủy</span>;
            default:
                return <span className="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">{status}</span>;
        }
    };

    return (
        <div className="py-8 bg-[#f8fafc] min-h-screen">
            <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                
                {/* Header & Search */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                            <a href={routes.shop || '/'} className="hover:text-[#ee4d2d]">Trang chủ</a>
                            <i className="fas fa-chevron-right text-[10px] text-slate-400"></i>
                            <span className="text-[#ee4d2d] font-bold">Lịch sử đơn hàng</span>
                        </div>
                        <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <i className="fas fa-receipt text-[#ee4d2d]"></i> Đơn Hàng Của Tôi ({orders.length})
                        </h1>
                    </div>

                    <div className="flex gap-2 max-w-md w-full bg-white p-1 rounded-2xl shadow-xs border border-slate-200">
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Tìm mã đơn #FDL-, tên món ăn..."
                            className="flex-1 px-4 py-2 text-xs font-semibold text-slate-800 outline-none"
                        />
                        <button
                            type="button"
                            className="px-5 py-2 bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs rounded-xl transition-colors shadow-xs cursor-pointer"
                        >
                            <i className="fas fa-search mr-1"></i> Tìm
                        </button>
                    </div>
                </div>

                {/* Orders List */}
                {filteredOrders.length === 0 ? (
                    <div className="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-3">
                        <div className="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                            <i className="fas fa-box-open"></i>
                        </div>
                        <h3 className="text-base font-bold text-slate-800">Chưa tìm thấy đơn hàng nào</h3>
                        <p className="text-xs text-slate-400">Bạn chưa đặt đơn hàng nào hoặc không có kết quả phù hợp với tìm kiếm.</p>
                        <a 
                            href={routes.shop || '/'}
                            className="inline-block px-5 py-2.5 rounded-xl bg-[#ee4d2d] text-white text-xs font-bold shadow-sm hover:bg-red-600 transition-colors"
                        >
                            Đặt món ngay
                        </a>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {filteredOrders.map((order) => (
                            <div key={`order-${order.id}`} className="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4 hover:shadow-md transition-all">
                                
                                {/* Top Header */}
                                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                                    <div className="flex items-center gap-3">
                                        <span className="text-xs font-black uppercase tracking-wider text-[#ee4d2d] bg-rose-50 px-3 py-1 rounded-full border border-rose-100">
                                            #FDL-{order.id}
                                        </span>
                                        <span className="text-xs font-bold text-slate-500">
                                            <i className="far fa-clock mr-1 text-slate-400"></i>
                                            {order.created_at ? new Date(order.created_at).toLocaleString('vi-VN') : 'Mới đây'}
                                        </span>
                                    </div>
                                    <div>
                                        {getStatusBadge(order.order_status)}
                                    </div>
                                </div>

                                {/* Items list */}
                                <div className="space-y-3">
                                    {(order.order_items || []).map((it, idx) => (
                                        <div key={`it-${idx}`} className="flex items-center justify-between gap-4 text-xs">
                                            <div className="flex items-center gap-3">
                                                <div className="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                                    <img 
                                                        src={it.dish?.image?.startsWith('http') ? it.dish.image : `/${it.dish?.image || 'logo.jpg'}`} 
                                                        alt={it.dish?.dish_name || 'Món'} 
                                                        className="w-full h-full object-cover"
                                                    />
                                                </div>
                                                <div>
                                                    <h4 className="font-bold text-slate-800">{it.dish?.dish_name || 'Món ăn'}</h4>
                                                    <p className="text-[11px] text-slate-400">Số lượng: x{it.quantity}</p>
                                                </div>
                                            </div>
                                            <span className="font-extrabold text-slate-800">
                                                {formatPrice((it.price || 0) * (it.quantity || 1))}
                                            </span>
                                        </div>
                                    ))}
                                </div>

                                {/* Total & Action buttons */}
                                <div className="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div className="text-xs">
                                        <span className="text-slate-400">Tổng thanh toán: </span>
                                        <span className="text-base font-black text-[#ee4d2d] ml-1">
                                            {formatPrice(order.final_amount || order.total_amount)}
                                        </span>
                                        <span className="ml-2 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                                            {order.payment_method === 'bank_transfer' ? 'VietQR' : (order.payment_method === 'momo' ? 'MoMo' : 'COD')}
                                        </span>
                                    </div>

                                    <div className="flex items-center gap-2 self-end sm:self-center">
                                        <a 
                                            href={`/tra-cuu-don-hang?order_id=FDL-${order.id}`}
                                            className="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors flex items-center gap-1"
                                        >
                                            <i className="fas fa-location-arrow text-[#0099ff]"></i> Theo dõi
                                        </a>

                                        {order.order_status === 'completed' && (
                                            <a 
                                                href={`/yeu-cau-hoan-tien?order_id=FDL-${order.id}`}
                                                className="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-[#ee4d2d] text-xs font-bold transition-colors"
                                            >
                                                Yêu cầu đổi/hoàn
                                            </a>
                                        )}
                                    </div>
                                </div>

                            </div>
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}
