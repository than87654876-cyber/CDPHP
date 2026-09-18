import React, { useState } from 'react';

export default function GroupOrderApp({
    groupOrder = {},
    dishes = [],
    csrfToken = '',
    routes = {},
    isHost = false
}) {
    const [memberName, setMemberName] = useState('');
    const [selectedDishId, setSelectedDishId] = useState(dishes[0]?.id || '');
    const [quantity, setQuantity] = useState(1);
    const [copied, setCopied] = useState(false);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    const shareUrl = window.location.href;

    const handleCopy = () => {
        navigator.clipboard.writeText(shareUrl);
        setCopied(true);
        setTimeout(() => setCopied(false), 2500);
    };

    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(shareUrl)}`;

    const items = groupOrder.items || [];
    const totalAmount = items.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 1), 0);

    return (
        <div className="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            {/* Top link & status */}
            <div className="flex items-center justify-between">
                <a href={routes.shop || '/'} className="inline-flex items-center gap-2 text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                    <i className="fas fa-arrow-left"></i> Quay lại thực đơn
                </a>
                <span className={`px-3.5 py-1.5 rounded-full text-xs font-extrabold flex items-center gap-2 ${
                    groupOrder.status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600'
                }`}>
                    <span className={`w-2 h-2 rounded-full ${groupOrder.status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'}`}></span>
                    {groupOrder.status === 'active' ? 'Phòng nhóm đang mở' : 'Đã chốt đơn'}
                </span>
            </div>

            {/* Room Share Card */}
            <div className="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                <div className="md:col-span-2 space-y-4">
                    <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold">
                        <i className="fas fa-users text-emerald-500"></i> Đặt đơn nhóm tiện lợi
                    </div>
                    <h1 className="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Mã Nhóm: <span className="text-emerald-600">#{groupOrder.code}</span>
                    </h1>
                    <p className="text-xs text-slate-500">
                        Trưởng nhóm: <strong className="text-slate-800">{groupOrder.host_name}</strong> • Mời bạn bè quét mã QR hoặc gửi link dưới đây để mỗi người tự chọn món vào đơn!
                    </p>

                    {/* Copy Link Input */}
                    <div className="flex items-center gap-2 pt-1">
                        <input
                            type="text"
                            readOnly
                            value={shareUrl}
                            className="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono text-slate-600 outline-none"
                        />
                        <button
                            type="button"
                            onClick={handleCopy}
                            className="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white font-bold text-xs shrink-0 transition-all cursor-pointer"
                        >
                            {copied ? 'Đã chép!' : 'Sao chép link'}
                        </button>
                    </div>
                </div>

                {/* QR Code preview */}
                <div className="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                    <img src={qrUrl} alt="QR Code Group Order" className="w-32 h-32 rounded-xl border border-slate-200" />
                    <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Quét để tham gia</span>
                </div>
            </div>

            {/* Member Add Item Form */}
            {groupOrder.status === 'active' && (
                <div className="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4">
                    <h3 className="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i className="fas fa-plus-circle text-emerald-500"></i> Chọn Món Của Bạn Vào Nhóm
                    </h3>

                    <form action={routes.addItem || `/don-nhom/${groupOrder.code}/them-mon`} method="POST" className="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <input type="hidden" name="_token" value={csrfToken} />
                        
                        <div className="sm:col-span-4">
                            <label className="block text-[11px] font-bold text-slate-600 mb-1">Tên bạn:</label>
                            <input
                                type="text"
                                name="member_name"
                                required
                                value={memberName}
                                onChange={(e) => setMemberName(e.target.value)}
                                placeholder="Nhập tên bạn (VD: Mai, Tuấn...)"
                                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-emerald-500"
                            />
                        </div>

                        <div className="sm:col-span-5">
                            <label className="block text-[11px] font-bold text-slate-600 mb-1">Chọn món ăn:</label>
                            <select
                                name="dish_id"
                                value={selectedDishId}
                                onChange={(e) => setSelectedDishId(e.target.value)}
                                className="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold outline-none focus:border-emerald-500 bg-white"
                            >
                                {dishes.map(dish => (
                                    <option key={dish.id} value={dish.id}>
                                        {dish.dish_name} - {formatPrice(dish.price)}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="sm:col-span-3 flex items-end gap-2">
                            <div className="w-20">
                                <label className="block text-[11px] font-bold text-slate-600 mb-1">Số lượng:</label>
                                <input
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="20"
                                    value={quantity}
                                    onChange={(e) => setQuantity(e.target.value)}
                                    className="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-center outline-none focus:border-emerald-500"
                                />
                            </div>
                            <button
                                type="submit"
                                className="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition-colors cursor-pointer"
                            >
                                + Thêm
                            </button>
                        </div>
                    </form>
                </div>
            )}

            {/* List of items chosen by members */}
            <div className="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4">
                <div className="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 className="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i className="fas fa-clipboard-list text-emerald-500"></i> Danh Sách Món Đã Chọn ({items.length})
                    </h3>
                    <span className="text-xs font-extrabold text-slate-500">
                        Tổng tiền nhóm: <strong className="text-emerald-600 text-sm ml-1">{formatPrice(totalAmount)}</strong>
                    </span>
                </div>

                {items.length === 0 ? (
                    <div className="py-8 text-center text-xs text-slate-400">
                        Chưa có bạn bè nào chọn món. Hãy gửi link phòng để mọi người cùng chọn nhé!
                    </div>
                ) : (
                    <div className="divide-y divide-slate-100">
                        {items.map((it, idx) => (
                            <div key={`group-it-${idx}`} className="py-3 flex items-center justify-between text-xs">
                                <div className="space-y-1">
                                    <div className="flex items-center gap-2">
                                        <span className="font-bold text-slate-800">{it.dish_name || it.dish?.dish_name}</span>
                                        <span className="text-slate-400">x{it.quantity}</span>
                                    </div>
                                    <p className="text-[11px] text-slate-400">
                                        Người chọn: <strong className="text-emerald-700">{it.member_name}</strong>
                                    </p>
                                </div>
                                <span className="font-black text-slate-900">
                                    {formatPrice((it.price || 0) * (it.quantity || 1))}
                                </span>
                            </div>
                        ))}
                    </div>
                )}

                {/* Finalize order button */}
                {groupOrder.status === 'active' && items.length > 0 && (
                    <div className="pt-4 border-t border-slate-100 flex justify-end">
                        <form action={routes.checkout || `/don-nhom/${groupOrder.code}/chot-don`} method="POST">
                            <input type="hidden" name="_token" value={csrfToken} />
                            <button
                                type="submit"
                                className="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all cursor-pointer flex items-center gap-2"
                            >
                                <i className="fas fa-check"></i> Chốt đơn và Tiến hành thanh toán ({formatPrice(totalAmount)})
                            </button>
                        </form>
                    </div>
                )}
            </div>
        </div>
    );
}
