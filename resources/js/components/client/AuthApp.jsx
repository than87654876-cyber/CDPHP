import React, { useState } from 'react';

export default function AuthApp({
    mode = 'login', // 'login' | 'register' | 'forgot_password' | 'otp_verify'
    csrfToken = '',
    routes = {},
    errors = [],
    statusMessage = '',
    oldInput = {}
}) {
    const [showPassword, setShowPassword] = useState(false);

    return (
        <div className="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div className="max-w-md w-full bg-white/95 backdrop-blur-md rounded-3xl p-8 shadow-xl border border-slate-200/80 space-y-6">
                
                {/* Brand Logo & Heading */}
                <div className="text-center space-y-2">
                    <a href={routes.shop || '/'} className="inline-flex items-center gap-2">
                        <img src="/logo.jpg" alt="FOODDAILY Logo" className="w-12 h-12 rounded-2xl object-cover shadow-sm mx-auto" />
                    </a>
                    <h2 className="text-2xl font-black text-slate-900 tracking-tight">
                        {mode === 'login' && 'Đăng Nhập FOODDAILY'}
                        {mode === 'register' && 'Đăng Ký Tài Khoản Mới'}
                        {mode === 'forgot_password' && 'Khôi Phục Mật Khẩu'}
                        {mode === 'otp_verify' && 'Xác Thực Mã OTP'}
                    </h2>
                    <p className="text-xs text-slate-400">
                        {mode === 'login' && 'Thưởng thức hàng ngàn món ăn ngon giao siêu tốc 20 phút'}
                        {mode === 'register' && 'Tạo tài khoản để tích điểm thưởng và nhận voucher giảm 50%'}
                        {mode === 'forgot_password' && 'Nhập email để nhận mã OTP xác thực khôi phục mật khẩu'}
                        {mode === 'otp_verify' && 'Vui lòng kiểm tra email của bạn để lấy mã OTP gồm 6 chữ số'}
                    </p>
                </div>

                {/* Error & Status Alerts */}
                {errors && errors.length > 0 && (
                    <div className="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold space-y-1">
                        {errors.map((err, i) => (
                            <p key={i} className="flex items-center gap-1.5">
                                <i className="fas fa-exclamation-circle text-rose-500"></i> {err}
                            </p>
                        ))}
                    </div>
                )}

                {statusMessage && (
                    <div className="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-1.5">
                        <i className="fas fa-check-circle text-emerald-500"></i> {statusMessage}
                    </div>
                )}

                {/* LOGIN FORM */}
                {mode === 'login' && (
                    <form action={routes.login || '/dang-nhap'} method="POST" className="space-y-4">
                        <input type="hidden" name="_token" value={csrfToken} />
                        
                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1">Email hoặc Số điện thoại:</label>
                            <input
                                type="text"
                                name="email"
                                required
                                defaultValue={oldInput.email || ''}
                                placeholder="name@example.com"
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                            />
                        </div>

                        <div>
                            <div className="flex items-center justify-between mb-1">
                                <label className="text-xs font-bold text-slate-700">Mật khẩu:</label>
                                <a href={routes.forgotPassword || '/quen-mat-khau'} className="text-[11px] font-bold text-[#ee4d2d] hover:underline">
                                    Quên mật khẩu?
                                </a>
                            </div>
                            <div className="relative">
                                <input
                                    type={showPassword ? 'text' : 'password'}
                                    name="password"
                                    required
                                    placeholder="••••••••"
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer"
                                >
                                    <i className={`fas ${showPassword ? 'fa-eye-slash' : 'fa-eye'}`}></i>
                                </button>
                            </div>
                        </div>

                        <div className="flex items-center">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                className="h-4 w-4 text-[#ee4d2d] focus:ring-[#ee4d2d] border-slate-300 rounded"
                            />
                            <label htmlFor="remember_me" className="ml-2 block text-xs text-slate-600 font-semibold cursor-pointer">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>

                        <button
                            type="submit"
                            className="w-full py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs transition-colors shadow-md shadow-rose-500/25 cursor-pointer"
                        >
                            Đăng Nhập Ngay
                        </button>

                        {/* Google Login Option */}
                        {routes.googleAuth && (
                            <a
                                href={routes.googleAuth}
                                className="w-full py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors flex items-center justify-center gap-2"
                            >
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" className="w-4 h-4" />
                                Đăng nhập bằng Google
                            </a>
                        )}

                        <div className="text-center pt-2">
                            <p className="text-xs text-slate-400">
                                Chưa có tài khoản?{' '}
                                <a href={routes.register || '/dang-ky'} className="font-extrabold text-[#ee4d2d] hover:underline">
                                    Đăng ký thành viên
                                </a>
                            </p>
                        </div>
                    </form>
                )}

                {/* REGISTER FORM */}
                {mode === 'register' && (
                    <form action={routes.register || '/dang-ky'} method="POST" className="space-y-4">
                        <input type="hidden" name="_token" value={csrfToken} />

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1">Họ và tên của bạn:</label>
                            <input
                                type="text"
                                name="name"
                                required
                                defaultValue={oldInput.name || ''}
                                placeholder="Nguyễn Văn A"
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1">Địa chỉ Email:</label>
                            <input
                                type="email"
                                name="email"
                                required
                                defaultValue={oldInput.email || ''}
                                placeholder="email@example.com"
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1">Số điện thoại:</label>
                            <input
                                type="tel"
                                name="phone"
                                required
                                defaultValue={oldInput.phone || ''}
                                placeholder="0901234567"
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1">Mật khẩu:</label>
                            <input
                                type="password"
                                name="password"
                                required
                                placeholder="Ít nhất 6 ký tự"
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1">Xác nhận lại mật khẩu:</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                required
                                placeholder="Nhập lại mật khẩu"
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                            />
                        </div>

                        <button
                            type="submit"
                            className="w-full py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs transition-colors shadow-md shadow-rose-500/25 cursor-pointer"
                        >
                            Đăng Ký Ngay
                        </button>

                        <div className="text-center pt-2">
                            <p className="text-xs text-slate-400">
                                Đã có tài khoản?{' '}
                                <a href={routes.login || '/dang-nhap'} className="font-extrabold text-[#ee4d2d] hover:underline">
                                    Đăng nhập
                                </a>
                            </p>
                        </div>
                    </form>
                )}

                {/* FORGOT PASSWORD FORM */}
                {mode === 'forgot_password' && (
                    <form action={routes.forgotPassword || '/quen-mat-khau'} method="POST" className="space-y-4">
                        <input type="hidden" name="_token" value={csrfToken} />

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1">Nhập Email đăng ký:</label>
                            <input
                                type="email"
                                name="email"
                                required
                                defaultValue={oldInput.email || ''}
                                placeholder="email@example.com"
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]"
                            />
                        </div>

                        <button
                            type="submit"
                            className="w-full py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs transition-colors shadow-md shadow-rose-500/25 cursor-pointer"
                        >
                            Gửi Mã Xác Nhận
                        </button>

                        <div className="text-center pt-2">
                            <a href={routes.login || '/dang-nhap'} className="text-xs font-bold text-slate-500 hover:text-[#ee4d2d]">
                                ← Quay lại đăng nhập
                            </a>
                        </div>
                    </form>
                )}

                {/* OTP VERIFY FORM */}
                {mode === 'otp_verify' && (
                    <form action={routes.otpVerify || '/xac-thuc-otp'} method="POST" className="space-y-4">
                        <input type="hidden" name="_token" value={csrfToken} />
                        <input type="hidden" name="email" value={oldInput.email || ''} />

                        <div>
                            <label className="block text-xs font-bold text-slate-700 mb-1 text-center">Nhập mã OTP gồm 6 chữ số:</label>
                            <input
                                type="text"
                                name="otp"
                                maxLength="6"
                                required
                                placeholder="123456"
                                className="w-full px-4 py-3 rounded-xl border border-slate-200 text-lg font-black tracking-widest text-center text-slate-800 outline-none focus:border-[#ee4d2d]"
                            />
                        </div>

                        <button
                            type="submit"
                            className="w-full py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition-colors shadow-md shadow-emerald-500/25 cursor-pointer"
                        >
                            Xác Nhận OTP
                        </button>
                    </form>
                )}

            </div>
        </div>
    );
}
