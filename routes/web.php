<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDishController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminPackageController;
use App\Http\Controllers\AdminPromotionController;
use App\Http\Controllers\AdminRefundController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminSubscriptionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerBackupController;
use App\Http\Controllers\GroupOrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StaffWorkspaceController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - FOODDAILY Application
|--------------------------------------------------------------------------
*/

// --- Các trang công khai / Client ---
Route::get('/', [ShopController::class, 'index'])->name('trangchu');
Route::get('/trangchu', [ShopController::class, 'index']);
Route::get('/trangchu_dangnhap', [ShopController::class, 'shopLogged'])->name('trangchu_dangnhap');
Route::get('/mon/{id}', [ShopController::class, 'dishDetail'])->name('dish.detail');

// Đặt đơn theo nhóm
Route::post('/nhom/tao', [GroupOrderController::class, 'create'])->name('nhom.create');
Route::get('/nhom/{code}', [GroupOrderController::class, 'show'])->name('nhom.show');
Route::post('/nhom/{code}/them-mon', [GroupOrderController::class, 'addItem'])->name('nhom.add_item');
Route::post('/nhom/{code}/xoa-mon/{itemId}', [GroupOrderController::class, 'removeItem'])->name('nhom.remove_item');
Route::post('/nhom/{code}/chot-don', [GroupOrderController::class, 'checkout'])->name('nhom.checkout');
Route::get('/api/nhom/{code}/poll', [GroupOrderController::class, 'pollItems'])->name('nhom.poll');

// API Hệ thống & Thanh toán & Chatbot
Route::get('/api/dishes/search', [ShopController::class, 'searchDishesApi'])->name('api.dishes.search');
Route::post('/api/chatbot/ask', [ShopController::class, 'chatbotAsk'])->name('api.chatbot.ask');
Route::get('/api/orders/{id}/payment-status', [CartController::class, 'getPaymentStatus'])->name('api.orders.payment-status');
Route::post('/api/payments/bank-transfer/notify', [CartController::class, 'notifyBankTransferPayment'])->name('api.payments.bank-transfer.notify');
Route::post('/api/payments/webhook', [CartController::class, 'payosWebhook'])->name('api.payments.webhook');
Route::post('/api/payos/webhook', [CartController::class, 'payosWebhook'])->name('api.payos.webhook');
Route::get('/api/orders/track/poll', [ShopController::class, 'pollTrackedOrder'])->name('api.orders.track.poll');
Route::get('/api/settings/poll', [ShopController::class, 'pollSettings'])->name('api.settings.poll');
Route::post('/api/coupon/validate', [CartController::class, 'validateCoupon'])->name('api.coupon.validate');
Route::post('/api/gemini/chat', [ShopController::class, 'geminiChat'])->name('api.gemini.chat');
Route::get('/api/geocode', [ShopController::class, 'geocodeAddress'])->name('api.geocode');

// Tra cứu đơn hàng dành cho khách vãng lai
Route::get('/tracuu', [ShopController::class, 'trackOrder'])->name('tracuu');

// Đăng nhập / Đăng xuất / Đăng ký dùng chung
Route::get('/trangchu/dangnhap', function () {
    return redirect()->route('dangnhap');
})->name('trangchu/dangnhap');

Route::get('/dangnhap', [AuthController::class, 'showLogin'])->name('dangnhap');
Route::post('/dangnhap', [AuthController::class, 'login'])->name('dangnhap.post');
Route::post('/trangchu/dangnhap', [AuthController::class, 'login'])->name('trangchu/dangnhap.post');
Route::get('/dangxuat', [AuthController::class, 'logout'])->name('dangxuat');

// OAuth Đăng nhập bằng Google
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Đăng ký tài khoản Khách hàng
Route::get('/trangchu/dangky', [AuthController::class, 'showClientRegister'])->name('trangchu/dangky');
Route::post('/trangchu/dangky', [AuthController::class, 'clientRegister'])->name('trangchu/dangky.post');

// Quên mật khẩu & Đặt lại mật khẩu qua Email / OTP
Route::get('/quenmatkhau', function () {
    return redirect()->route('trangchu/quenmatkhau');
})->name('quenmatkhau');

Route::get('/trangchu/quenmatkhau', function () {
    return view('client.forget_password');
})->name('trangchu/quenmatkhau');

Route::post('/trangchu/quenmatkhau', [AuthController::class, 'sendClientResetLinkEmail'])->name('trangchu/quenmatkhau.post');
Route::get('/trangchu/quenmatkhau/xacnhan', [AuthController::class, 'showClientOtpVerify'])->name('trangchu/quenmatkhau/xacnhan');
Route::post('/trangchu/quenmatkhau/xacnhan', [AuthController::class, 'verifyClientOtpAndResetPassword'])->name('trangchu/quenmatkhau/xacnhan.post');
Route::get('/trangchu/doimatkhau/{token}', [AuthController::class, 'showClientResetPassword'])->name('trangchu/doimatkhau');
Route::get('/doimatkhau/{token}', [AuthController::class, 'showClientResetPassword'])->name('doimatkhau');
Route::post('/trangchu/doimatkhau', [AuthController::class, 'resetClientPassword'])->name('trangchu/doimatkhau.post');

// Các route Giỏ hàng & Thanh toán
Route::get('/giohang', [CartController::class, 'index'])->name('giohang');
Route::post('/giohang/add', [CartController::class, 'add'])->name('giohang.add');
Route::post('/giohang/update', [CartController::class, 'update'])->name('giohang.update');
Route::post('/giohang/remove', [CartController::class, 'remove'])->name('giohang.remove');

// Quy trình mua hàng & Checkout
Route::get('/muahang', [CartController::class, 'checkoutPage'])->name('muahang');
Route::post('/muahang/process', [CartController::class, 'processCheckout'])->name('muahang.process');
Route::get('/muahang/thanhtoan/{id}', [CartController::class, 'paymentPage'])->name('muahang.thanhtoan');
Route::post('/muahang/thanhtoan/select-method/{id}', [CartController::class, 'selectPaymentMethod'])->name('muahang.thanhtoan.select');
Route::post('/muahang/thanhtoan/confirm-cod/{id}', [CartController::class, 'confirmCod'])->name('muahang.thanhtoan.confirm-cod');
Route::get('/thanhtoan_chuyenkhoan', [CartController::class, 'transferPayment'])->name('thanhtoan_chuyenkhoan');
Route::get('/thanhtoan_ATM', [CartController::class, 'atmMethod'])->name('thanhtoan_ATM');
Route::get('/thanhtoan_momo', [CartController::class, 'momoMethod'])->name('thanhtoan_momo');
Route::get('/thanhtoan_hoantat/{id}', [CartController::class, 'completePayment'])->name('thanhtoan_hoantat');

// Hủy đơn hàng, Đổi/Hoàn tiền, Đánh giá sản phẩm
Route::post('/order/cancel', [CartController::class, 'cancelOrder'])->name('order.cancel');
Route::get('/yeucauhoan', [CartController::class, 'refundsList'])->name('yeucauhoan');
Route::post('/order/refund', [CartController::class, 'refundOrder'])->name('order.refund');
Route::post('/order/review', [CartController::class, 'reviewOrder'])->name('order.review');

// Dynamic Realtime Orders Polling API
Route::get('/api/orders/poll', [CartController::class, 'pollUserOrders'])->name('api.orders.poll');

// Gói dịch vụ khách hàng (Subscriptions)
Route::middleware(['auth'])->group(function () {
    Route::get('/goidichvu', [SubscriptionController::class, 'index'])->name('goidichvu');
    Route::post('/goidichvu/doimon', [SubscriptionController::class, 'changeMenu'])->name('goidichvu.change_menu');
    Route::post('/goidichvu/ghichu', [SubscriptionController::class, 'addNote'])->name('goidichvu.add_note');
    Route::post('/goidichvu/tamngung', [SubscriptionController::class, 'pause'])->name('goidichvu.pause');
    Route::post('/goidichvu/huy', [SubscriptionController::class, 'cancel'])->name('goidichvu.cancel');
    Route::post('/goidichvu/danhgia', [SubscriptionController::class, 'review'])->name('goidichvu.review');
    Route::post('/goidichvu/mua', [SubscriptionController::class, 'buyPackage'])->name('goidichvu.buy');
});

// --- Phân hệ Quản trị (Admin & Staff) ---
Route::middleware(['auth', 'admin'])->group(function () {

    // Chỉ Super Admin mới được truy cập các quản lý hệ thống cao cấp
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/quanly', [AdminDashboardController::class, 'index'])->name('quanly');
        Route::get('/quanly_trangchu', function () {
            return redirect()->route('quanly');
        })->name('quanly_trangchu');

        // Cấu hình trang chủ
        Route::get('/quanly_cauhinh', [AdminSettingController::class, 'index'])->name('quanly_cauhinh');
        Route::post('/quanly_cauhinh', [AdminSettingController::class, 'update'])->name('quanly_cauhinh.post');

        // Quản lý Gói dịch vụ Combo
        Route::get('/quanly_goidichvu', [AdminPackageController::class, 'index'])->name('quanly_goidichvu');
        Route::get('/goidichvu_them', [AdminPackageController::class, 'create'])->name('goidichvu_them');
        Route::post('/goidichvu_them', [AdminPackageController::class, 'store'])->name('goidichvu_them.post');
        Route::get('/goidichvu_xem/{id}', [AdminPackageController::class, 'show'])->name('goidichvu_xem');
        Route::get('/goidichvu_chinhsua/{id}', [AdminPackageController::class, 'edit'])->name('goidichvu_chinhsua');
        Route::post('/goidichvu_chinhsua/{id}', [AdminPackageController::class, 'update'])->name('goidichvu_chinhsua.post');
        Route::post('/goidichvu_xoa/{id}', [AdminPackageController::class, 'destroy'])->name('goidichvu_xoa');

        // Quản lý Nhân viên
        Route::get('/quanly_nhanvien', [AdminUserController::class, 'employeesList'])->name('quanly_nhanvien');
        Route::get('/nhanvien_them', [AdminUserController::class, 'employeeCreate'])->name('nhanvien_them');
        Route::post('/nhanvien_them', [AdminUserController::class, 'employeeStore'])->name('nhanvien_them.post');
        Route::get('/nhanvien_xem/{id}', [AdminUserController::class, 'employeeShow'])->name('nhanvien_xem');
        Route::get('/nhanvien_chinhsua/{id}', [AdminUserController::class, 'employeeEdit'])->name('nhanvien_chinhsua');
        Route::post('/nhanvien_chinhsua/{id}', [AdminUserController::class, 'employeeUpdate'])->name('nhanvien_chinhsua.post');
        Route::post('/nhanvien_xoa/{id}', [AdminUserController::class, 'employeeDestroy'])->name('nhanvien_xoa');

        // Quản lý Mã Khuyến mãi
        Route::get('/quanly_khuyenmai', [AdminPromotionController::class, 'index'])->name('quanly_khuyenmai');
        Route::get('/khuyenmai_them', [AdminPromotionController::class, 'create'])->name('khuyenmai_them');
        Route::post('/khuyenmai_them', [AdminPromotionController::class, 'store'])->name('khuyenmai_them.post');
        Route::get('/khuyenmai_xem/{id}', [AdminPromotionController::class, 'show'])->name('khuyenmai_xem');
        Route::get('/khuyenmai_chinhsua/{id}', [AdminPromotionController::class, 'edit'])->name('khuyenmai_chinhsua');
        Route::post('/khuyenmai_chinhsua/{id}', [AdminPromotionController::class, 'update'])->name('khuyenmai_chinhsua.post');
        Route::post('/khuyenmai_xoa/{id}', [AdminPromotionController::class, 'destroy'])->name('khuyenmai_xoa');

        // Gửi mã coupon
        Route::get('/quanly_guima', [AdminPromotionController::class, 'showSendCoupon'])->name('quanly_guima');
        Route::post('/quanly_guima', [AdminPromotionController::class, 'sendCoupon'])->name('quanly_guima.post');

        // Sao lưu Khách hàng (Bảo mật: Chỉ Superadmin)
        Route::get('/quanly/backup-khachhang', [CustomerBackupController::class, 'index'])->name('backup_khachhang_index');
        Route::post('/quanly/backup-khachhang/create', [CustomerBackupController::class, 'createBackup'])->name('backup_khachhang_create');
        Route::get('/quanly/backup-khachhang/download/{id}', [CustomerBackupController::class, 'downloadBackup'])->name('backup_khachhang_download');
        Route::post('/quanly/backup-khachhang/restore/{id}', [CustomerBackupController::class, 'restoreBackup'])->name('backup_khachhang_restore');
        Route::delete('/quanly/backup-khachhang/delete/{id}', [CustomerBackupController::class, 'destroy'])->name('backup_khachhang_delete');
    });

    // Các chức năng dùng chung cho cả Admin và Staff (Bàn làm việc, thực đơn, đơn hàng, bếp)

    // Bàn làm việc nhân viên
    Route::get('/quanly_banlamviec', [StaffWorkspaceController::class, 'index'])->name('quanly_banlamviec');

    // Quản lý Danh mục
    Route::get('/quanly_danhmuc', [AdminCategoryController::class, 'index'])->name('quanly_danhmuc');
    Route::get('/danhmuc_xem/{id}', [AdminCategoryController::class, 'show'])->name('danhmuc_xem');
    Route::get('/danhmuc_them', [AdminCategoryController::class, 'create'])->name('danhmuc_them');
    Route::post('/danhmuc_them', [AdminCategoryController::class, 'store'])->name('danhmuc_them.post');
    Route::get('/danhmuc_chinhsua/{id}', [AdminCategoryController::class, 'edit'])->name('danhmuc_chinhsua');
    Route::post('/danhmuc_chinhsua/{id}', [AdminCategoryController::class, 'update'])->name('danhmuc_chinhsua.post');
    Route::post('/danhmuc_xoa/{id}', [AdminCategoryController::class, 'destroy'])->name('danhmuc_xoa');

    // Quản lý Món ăn đơn
    Route::get('/quanly_monandon', [AdminDishController::class, 'index'])->name('quanly_monandon');
    Route::get('/monandon_them', [AdminDishController::class, 'create'])->name('monandon_them');
    Route::post('/monandon_them', [AdminDishController::class, 'store'])->name('monandon_them.post');
    Route::get('/monandon_chinhsua/{id}', [AdminDishController::class, 'edit'])->name('monandon_chinhsua');
    Route::post('/monandon_chinhsua/{id}', [AdminDishController::class, 'update'])->name('monandon_chinhsua.post');
    Route::get('/monandon_xem/{id}', [AdminDishController::class, 'show'])->name('monandon_xem');
    Route::post('/monandon_xoa/{id}', [AdminDishController::class, 'destroy'])->name('monandon_xoa');

    // Quản lý Đơn hàng
    Route::get('/quanly_donhang', [AdminOrderController::class, 'index'])->name('quanly_donhang');
    Route::get('/donhang_xem/{id}', [AdminOrderController::class, 'show'])->name('donhang_xem');
    Route::get('/donhang_chinhsua/{id}', [AdminOrderController::class, 'edit'])->name('donhang_chinhsua');
    Route::post('/donhang_chinhsua/{id}', [AdminOrderController::class, 'update'])->name('donhang_chinhsua.post');
    Route::post('/donhang_refund/{id}', [AdminRefundController::class, 'processRefund'])->name('donhang_refund');
    Route::post('/donhang_xoa/{id}', [AdminOrderController::class, 'destroy'])->name('donhang_xoa');

    // Quản lý Đánh giá
    Route::get('/quanly_reviews', [AdminOrderController::class, 'reviewsList'])->name('quanly_reviews');
    Route::post('/quanly_reviews/xoa/{id}', [AdminOrderController::class, 'destroyReview'])->name('quanly_reviews.xoa');

    // Bếp chuẩn bị món
    Route::get('/quanly_bep', [AdminOrderController::class, 'kitchenReport'])->name('quanly_bep');

    // Quản lý Gói đăng ký
    Route::get('/quanly_goidangky', [AdminSubscriptionController::class, 'index'])->name('quanly_goidangky');
    Route::get('/goidangky_xem/{id}', [AdminSubscriptionController::class, 'show'])->name('goidangky_xem');
    Route::get('/goidangky_chinhsua/{id}', [AdminSubscriptionController::class, 'edit'])->name('goidangky_chinhsua');
    Route::post('/goidangky_chinhsua/{id}', [AdminSubscriptionController::class, 'update'])->name('goidangky_chinhsua.post');
    Route::post('/goidangky_xoa/{id}', [AdminSubscriptionController::class, 'destroy'])->name('goidangky_xoa');
    Route::post('/goidangky_tao_don/{id}', [AdminSubscriptionController::class, 'createDailyOrder'])->name('goidangky_tao_don');

    // Quản lý Hoàn tiền
    Route::get('/quanly_yeucauhoan', [AdminRefundController::class, 'refundsList'])->name('quanly_yeucauhoan');
    Route::get('/yeucauhoan_xem/{id}', [AdminRefundController::class, 'refundShow'])->name('yeucauhoan_xem');
    Route::post('/yeucauhoan_duyet/{id}', [AdminRefundController::class, 'refundApprove'])->name('yeucauhoan_duyet');

    // Quản lý Khách hàng
    Route::get('/quanly_khachhang', [AdminUserController::class, 'customersList'])->name('quanly_khachhang');
    Route::get('/khachhang_xem/{id}', [AdminUserController::class, 'customerShow'])->name('khachhang_xem');
    Route::get('/khachhang_chinhsua/{id}', [AdminUserController::class, 'customerEdit'])->name('khachhang_chinhsua');
    Route::post('/khachhang_chinhsua/{id}', [AdminUserController::class, 'customerUpdate'])->name('khachhang_chinhsua.post');
    Route::post('/khachhang_xoa/{id}', [AdminUserController::class, 'customerDestroy'])->name('khachhang_xoa');
    Route::get('/quanly_khachvanglai', [AdminUserController::class, 'guestsList'])->name('quanly_khachvanglai');

    // Báo cáo & Xuất CSV / Excel
    Route::get('/quanly/baocao/xuat-orders', [AdminOrderController::class, 'exportOrdersCsv'])->name('baocao_xuat_orders');
    Route::get('/quanly/baocao/xuat-customers', [AdminUserController::class, 'exportCustomersExcel'])->name('baocao_xuat_customers');
    Route::get('/quanly/baocao/xuat-dishes', [AdminDishController::class, 'exportDishesCsv'])->name('baocao_xuat_dishes');
    Route::get('/quanly/baocao/xuat-refunds', [AdminRefundController::class, 'exportRefundsCsv'])->name('baocao_xuat_refunds');
});

Route::get('/test-broadcast', function () {
    broadcast(new \App\Events\MessageSent('Chào bạn, đây là tin nhắn thời gian thực từ Pusher!'));
    return 'Broadcast Sent!';
});
