import React, { useState } from 'react';

export default function TrackOrderApp({
    myOrders = [],
    selectedOrder = null,
    orderIdInput = '',
    phoneInput = '',
    csrfToken = '',
    routes = {}
}) {
    const [showLookup, setShowLookup] = useState(!selectedOrder && myOrders.length === 0);
    const [orderId, setOrderId] = useState(orderIdInput || '');
    const [phone, setPhone] = useState(phoneInput || '');

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    // Calculate step index (1 to 4)
    const getStepIndex = (status) => {
        switch(status) {
            case 'pending': return 1;
            case 'confirmed':
            case 'cooking': return 2;
            case 'delivering': return 3;
            case 'completed': return 4;
            case 'cancelled': return 0;
            default: return 1;
        }
    };

    const currentStep = selectedOrder ? getStepIndex(selectedOrder.order_status) : 1;

    return (
        <div className="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            {/* Header banner */}
            <div className="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="flex items-center gap-3">
                        <div className="w-12 h-12 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center font-bold text-xl shadow-xs">
                            <i className="fas fa-receipt"></i>
                        </div>
                        <div>
                            <h1 className="text-xl font-extrabold text-slate-900">Tiến Độ & Tra Cứu Đơn Hàng</h1>
                            <p className="text-xs text-slate-500">Xem hành trình giao món ăn hỏa tốc từ nhà bếp đến bạn</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onClick={() => setShowLookup(!showLookup)}
                        className="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-1.5 cursor-pointer self-start sm:self-auto"
                    >
                        <i className="fas fa-search"></i>
                        <span>{showLookup ? 'Ẩn tra cứu' : 'Tra cứu mã khác'}</span>
                    </button>
                </div>

                {/* Guest Lookup Form */}
                {showLookup && (
                    <form action={routes.trackOrder || '/tra-cuu-don-hang'} method="GET" className="mt-6 pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <input
                            type="text"
                            name="order_id"
                            value={orderId}
                            onChange={(e) => setOrderId(e.target.value)}
                            placeholder="Mã đơn (Ví dụ: FDL-12)"
                            className="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                        />
                        <input
                            type="tel"
                            name="phone"
                            value={phone}
                            onChange={(e) => setPhone(e.target.value)}
                            placeholder="Số điện thoại đặt"
                            className="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                        />
                        <button
                            type="submit"
                            className="px-5 py-2.5 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-xl font-extrabold text-xs transition-colors cursor-pointer"
                        >
                            Tra cứu ngay
                        </button>
                    </form>
                )}
            </div>

            {/* If there is a selected order, show tracker */}
            {selectedOrder ? (
                <div className="space-y-6">
                    {/* Stepper Card */}
                    <div className="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                            <div>
                                <span className="text-xs font-black uppercase text-[#ee4d2d] bg-rose-50 px-3 py-1 rounded-full border border-rose-100">
                                    Mã đơn: #FDL-{selectedOrder.id}
                                </span>
                                <p className="text-xs text-slate-400 mt-1">
                                    Thời gian đặt: {selectedOrder.created_at ? new Date(selectedOrder.created_at).toLocaleString('vi-VN') : 'Mới đây'}
                                </p>
                            </div>
                            
                            {selectedOrder.order_status === 'cancelled' ? (
                                <span className="px-3.5 py-1.5 rounded-full bg-rose-50 text-rose-600 border border-rose-200 text-xs font-extrabold flex items-center gap-1.5">
                                    <i className="fas fa-ban"></i> Đơn hàng đã bị hủy
                                </span>
                            ) : (
                                <span className="px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-extrabold flex items-center gap-1.5">
                                    <span className="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    {selectedOrder.order_status === 'completed' ? 'Hoàn thành' : 'Đang xử lý & Giao'}
                                </span>
                            )}
                        </div>

                        {/* Stepper Progress Bar */}
                        {selectedOrder.order_status !== 'cancelled' && (
                            <div className="py-4">
                                <div className="grid grid-cols-4 gap-2 relative text-center">
                                    
                                    {/* Step 1 */}
                                    <div className="space-y-2">
                                        <div className={`w-10 h-10 mx-auto rounded-2xl flex items-center justify-center text-sm font-bold shadow-sm ${
                                            currentStep >= 1 ? 'bg-[#ee4d2d] text-white' : 'bg-slate-100 text-slate-400'
                                        }`}>
                                            <i className="fas fa-file-lines"></i>
                                        </div>
                                        <p className="text-[11px] font-bold text-slate-800">1. Đã nhận đơn</p>
                                    </div>

                                    {/* Step 2 */}
                                    <div className="space-y-2">
                                        <div className={`w-10 h-10 mx-auto rounded-2xl flex items-center justify-center text-sm font-bold shadow-sm ${
                                            currentStep >= 2 ? 'bg-[#ee4d2d] text-white' : 'bg-slate-100 text-slate-400'
                                        }`}>
                                            <i className="fas fa-fire-burner"></i>
                                        </div>
                                        <p className="text-[11px] font-bold text-slate-800">2. Đang chế biến</p>
                                    </div>

                                    {/* Step 3 */}
                                    <div className="space-y-2">
                                        <div className={`w-10 h-10 mx-auto rounded-2xl flex items-center justify-center text-sm font-bold shadow-sm ${
                                            currentStep >= 3 ? 'bg-[#ee4d2d] text-white' : 'bg-slate-100 text-slate-400'
                                        }`}>
                                            <i className="fas fa-motorcycle"></i>
                                        </div>
                                        <p className="text-[11px] font-bold text-slate-800">3. Đang giao (20')</p>
                                    </div>

                                    {/* Step 4 */}
                                    <div className="space-y-2">
                                        <div className={`w-10 h-10 mx-auto rounded-2xl flex items-center justify-center text-sm font-bold shadow-sm ${
                                            currentStep >= 4 ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400'
                                        }`}>
                                            <i className="fas fa-house-chimney-check"></i>
                                        </div>
                                        <p className="text-[11px] font-bold text-slate-800">4. Đã giao tới</p>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Order Items & Customer info */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                            <div className="space-y-3">
                                <h4 className="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                    <i className="fas fa-user text-slate-400"></i> Người nhận hàng
                                </h4>
                                <div className="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs space-y-1.5 text-slate-600">
                                    <p><strong className="text-slate-900">{selectedOrder.full_name || 'Khách hàng'}</strong></p>
                                    <p>SĐT: {selectedOrder.phone || 'Chưa cung cấp'}</p>
                                    <p>Địa chỉ: {selectedOrder.address || 'Giao tận nơi'}</p>
                                    {selectedOrder.notes && <p className="text-amber-600 italic">Ghi chú: {selectedOrder.notes}</p>}
                                </div>
                            </div>

                            <div className="space-y-3">
                                <h4 className="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                    <i className="fas fa-wallet text-slate-400"></i> Thanh toán
                                </h4>
                                <div className="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs space-y-1.5 text-slate-600">
                                    <div className="flex justify-between">
                                        <span>Hình thức:</span>
                                        <span className="font-bold text-slate-800">
                                            {selectedOrder.payment_method === 'bank_transfer' ? 'Chuyển khoản VietQR' : (selectedOrder.payment_method === 'momo' ? 'Ví MoMo' : 'Tiền mặt COD')}
                                        </span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span>Tình trạng:</span>
                                        <span className="font-bold text-emerald-600">
                                            {selectedOrder.payment_status === 'paid' ? 'Đã thanh toán' : 'Thanh toán khi nhận'}
                                        </span>
                                    </div>
                                    <div className="flex justify-between pt-1 border-t border-slate-200">
                                        <span className="font-bold text-slate-900">Tổng cộng:</span>
                                        <span className="font-black text-sm text-[#ee4d2d]">
                                            {formatPrice(selectedOrder.final_amount || selectedOrder.total_amount)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* List of items */}
                        <div className="space-y-2 pt-2">
                            <h4 className="text-xs font-black text-slate-900 uppercase tracking-wider">
                                Món ăn trong đơn ({selectedOrder.order_items?.length || 0})
                            </h4>
                            <div className="divide-y divide-slate-100">
                                {(selectedOrder.order_items || []).map((it, idx) => (
                                    <div key={`track-it-${idx}`} className="py-2.5 flex items-center justify-between text-xs">
                                        <div className="flex items-center gap-3">
                                            <div className="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 shrink-0">
                                                <img 
                                                    src={it.dish?.image?.startsWith('http') ? it.dish.image : `/${it.dish?.image || 'logo.jpg'}`} 
                                                    alt={it.dish?.dish_name || 'Món'} 
                                                    className="w-full h-full object-cover"
                                                />
                                            </div>
                                            <div>
                                                <h5 className="font-bold text-slate-800">{it.dish?.dish_name || 'Món ăn'}</h5>
                                                <span className="text-slate-400 text-[11px]">x{it.quantity}</span>
                                            </div>
                                        </div>
                                        <span className="font-extrabold text-slate-800">
                                            {formatPrice((it.price || 0) * (it.quantity || 1))}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </div>

                    </div>
                </div>
            ) : null}

            {/* List of other orders */}
            {myOrders && myOrders.length > 0 && (
                <div className="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4">
                    <h3 className="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i className="fas fa-list-check text-[#ee4d2d]"></i> Danh sách các đơn hàng khác của bạn ({myOrders.length})
                    </h3>

                    <div className="space-y-2.5">
                        {myOrders.map(ord => (
                            <a
                                key={`list-ord-${ord.id}`}
                                href={`/tra-cuu-don-hang?order_id=FDL-${ord.id}`}
                                className={`p-4 rounded-2xl border transition-all flex items-center justify-between block ${
                                    selectedOrder && selectedOrder.id === ord.id
                                        ? 'bg-rose-50/70 border-[#ee4d2d]'
                                        : 'bg-white border-slate-200 hover:border-slate-300'
                                }`}
                            >
                                <div className="space-y-1">
                                    <div className="flex items-center gap-2">
                                        <span className="font-black text-slate-900 text-xs">#FDL-{ord.id}</span>
                                        <span className="text-[11px] text-slate-400">
                                            • {ord.created_at ? new Date(ord.created_at).toLocaleDateString('vi-VN') : ''}
                                        </span>
                                    </div>
                                    <p className="text-xs font-extrabold text-[#ee4d2d]">
                                        {formatPrice(ord.final_amount || ord.total_amount)}
                                    </p>
                                </div>
                                <span className="text-xs font-bold text-slate-500 flex items-center gap-1">
                                    Xem chi tiết <i className="fas fa-chevron-right text-[10px]"></i>
                                </span>
                            </a>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
