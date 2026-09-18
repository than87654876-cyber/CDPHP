import React, { useState } from 'react';

export default function SubscriptionApp({
    subscriptions = [],
    csrfToken = '',
    routes = {}
}) {
    const [selectedSub, setSelectedSub] = useState(subscriptions[0] || null);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    return (
        <div className="py-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <a href={routes.shop || '/'} className="inline-flex items-center gap-2 text-xs font-bold text-rose-500 hover:text-rose-600 mb-2 transition-colors">
                        <i className="fas fa-arrow-left"></i> Quay lại cửa hàng
                    </a>
                    <h1 className="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                        <i className="fas fa-box-open text-[#ee4d2d]"></i> Gói Dịch Vụ Ăn Uống Dài Hạn Của Tôi
                    </h1>
                </div>
            </div>

            {subscriptions.length === 0 ? (
                <div className="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-3">
                    <div className="w-16 h-16 rounded-full bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-2xl mx-auto">
                        <i className="fas fa-calendar-check"></i>
                    </div>
                    <h3 className="text-base font-bold text-slate-800">Bạn chưa đăng ký gói ăn uống định kỳ nào</h3>
                    <p className="text-xs text-slate-400">Tiết kiệm tới 30% khi đăng ký thực đơn tuần/tháng với các món ăn dinh dưỡng được giao đúng giờ mỗi ngày.</p>
                    <a 
                        href={`${routes.shop || '/'}#menu`}
                        className="inline-block px-5 py-2.5 rounded-xl bg-[#ee4d2d] text-white text-xs font-bold shadow-sm hover:bg-red-600 transition-colors"
                    >
                        Xem các gói ưu đãi
                    </a>
                </div>
            ) : (
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* List of packages */}
                    <div className="space-y-4 lg:col-span-1">
                        <h3 className="text-xs font-black uppercase text-slate-400 tracking-wider">
                            Gói đã đăng ký ({subscriptions.length})
                        </h3>
                        {subscriptions.map(sub => (
                            <div
                                key={`sub-${sub.id}`}
                                onClick={() => setSelectedSub(sub)}
                                className={`p-5 rounded-2xl border transition-all cursor-pointer space-y-2 ${
                                    selectedSub?.id === sub.id
                                        ? 'bg-white border-[#ee4d2d] shadow-md ring-2 ring-rose-500/20'
                                        : 'bg-white/80 border-slate-200 hover:border-slate-300'
                                }`}
                            >
                                <div className="flex items-center justify-between">
                                    <h4 className="font-extrabold text-sm text-slate-800">
                                        {sub.package?.package_name || 'Gói Dinh Dưỡng'}
                                    </h4>
                                    <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase ${
                                        sub.status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'
                                    }`}>
                                        {sub.status === 'active' ? 'Đang kích hoạt' : 'Tạm ngưng'}
                                    </span>
                                </div>
                                <p className="text-xs text-slate-400 font-semibold">
                                    Thời hạn: {sub.package?.duration_days || 7} ngày • Đơn #FDL-{sub.order_id}
                                </p>
                            </div>
                        ))}
                    </div>

                    {/* Active Package Schedule View */}
                    <div className="lg:col-span-2">
                        {selectedSub && (
                            <div className="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
                                <div className="flex items-center justify-between pb-4 border-b border-slate-100">
                                    <div>
                                        <h3 className="font-black text-lg text-slate-900">
                                            {selectedSub.package?.package_name}
                                        </h3>
                                        <p className="text-xs text-slate-400">
                                            Bắt đầu: {selectedSub.start_date || 'Hôm nay'} • Kết thúc: {selectedSub.end_date || 'Sau 30 ngày'}
                                        </p>
                                    </div>

                                    {/* Action toggle */}
                                    <form action={`/goi-dinh-ky/${selectedSub.id}/toggle-status`} method="POST">
                                        <input type="hidden" name="_token" value={csrfToken} />
                                        <button
                                            type="submit"
                                            className={`px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer ${
                                                selectedSub.status === 'active'
                                                    ? 'bg-amber-50 text-amber-700 hover:bg-amber-100'
                                                    : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                                            }`}
                                        >
                                            {selectedSub.status === 'active' ? 'Tạm ngưng nhận món' : 'Tiếp tục nhận món'}
                                        </button>
                                    </form>
                                </div>

                                {/* Schedule list */}
                                <div className="space-y-3">
                                    <h4 className="text-xs font-black uppercase tracking-wider text-slate-700">
                                        Lịch giao món từng ngày trong gói
                                    </h4>
                                    <div className="divide-y divide-slate-100">
                                        {(selectedSub.package?.dishes || []).map((d, index) => (
                                            <div key={`sched-${index}`} className="py-3 flex items-center justify-between text-xs">
                                                <div className="flex items-center gap-3">
                                                    <span className="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-black text-xs shrink-0">
                                                        N{index + 1}
                                                    </span>
                                                    <div>
                                                        <h5 className="font-bold text-slate-800">{d.dish_name}</h5>
                                                        <p className="text-[11px] text-slate-400">{d.category?.category_name || 'Bữa chính'}</p>
                                                    </div>
                                                </div>
                                                <span className="text-emerald-600 font-bold text-[11px]">
                                                    Chuẩn bị sẵn sàng
                                                </span>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}
