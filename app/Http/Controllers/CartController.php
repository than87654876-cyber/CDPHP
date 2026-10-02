<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedMail;

class CartController extends Controller
{
    // Thêm món ăn vào giỏ hàng
    public function add(Request $request)
    {
        $request->validate([
            'dish_id' => 'required|integer|exists:dishes,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $dish = Dish::findOrFail($request->dish_id);
        $quantity = max(1, intval($request->input('quantity', 1)));
        session(['selected_dish_id' => $dish->id]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm ' . $dish->dish_name . ' vào giỏ hàng!',
                'dish' => $dish,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('muahang')->with('added_dish', [
            'id' => $dish->id,
            'name' => $dish->dish_name,
            'price' => $dish->price,
            'quantity' => $quantity,
            'image' => $dish->image ? asset($dish->image) : '',
        ])->with('success', 'Đã thêm ' . $dish->dish_name . ' vào giỏ hàng!');
    }

    // Cập nhật số lượng giỏ hàng
    public function update(Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Đã cập nhật giỏ hàng.');
    }

    // Xóa món ăn khỏi giỏ hàng
    public function remove(Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Đã xóa món ăn khỏi giỏ hàng.');
    }

    // Hiển thị danh sách lịch sử đơn hàng của User
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        $query = Order::where('user_id', $user->id)
            ->with(['orderItems.dish', 'reviews'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%'.$search.'%')
                    ->orWhere('payment_method', 'like', '%'.$search.'%')
                    ->orWhere('order_status', 'like', '%'.$search.'%')
                    ->orWhereHas('orderItems.dish', function ($subQuery) use ($search) {
                        $subQuery->where('dish_name', 'like', '%'.$search.'%');
                    });
            });
        }

        $orders = $query->get();

        return view('client.cart', compact('orders', 'search'));
    }

    // Xử lý gửi đơn đặt hàng từ giỏ hàng (localStorage gửi lên)
    public function processCheckout(Request $request)
    {
        $validationRules = [
            'cart_phone' => 'required|string',
            'cart_address' => 'required|string',
            'cart_time' => 'required|string',
            'cart_payment' => 'required|string',
            'cart_items' => 'required|string', // Chuỗi JSON chứa danh sách sản phẩm
        ];

        if (!Auth::check()) {
            $validationRules['cart_fullname'] = 'required|string|max:255';
            $validationRules['cart_email'] = 'required|email';
        }

        $request->validate($validationRules);

        $items = json_decode($request->cart_items, true);
        if (empty($items)) {
            return back()->withErrors(['cart_items' => 'Giỏ hàng của bạn đang trống.']);
        }

        // Xác định ID người dùng (Nếu là khách vãng lai thì tìm hoặc tạo tài khoản guest ẩn)
        $userId = Auth::id();
        $shouldAutoLogin = false;

        if (!$userId) {
            $email = trim($request->input('cart_email'));
            $phone = trim($request->input('cart_phone'));
            $fullname = trim($request->input('cart_fullname'));

            // Tìm xem email hoặc số điện thoại đã tồn tại trong hệ thống chưa
            $existingUser = \App\Models\User::where(function($q) use ($email, $phone) {
                if ($email) {
                    $q->where('email', $email);
                }
                if ($phone) {
                    $q->orWhere('phone', $phone);
                }
            })->first();

            if ($existingUser) {
                if ($existingUser->role === 'guest') {
                    if ($fullname) {
                        $existingUser->update(['fullname' => $fullname]);
                    }
                    $shouldAutoLogin = true;
                }
                // If it's a registered customer/admin, link the order without hijacking session
                $userId = $existingUser->id;
            } else {
                $guestUser = \App\Models\User::create([
                    'fullname' => $fullname ?: 'Khách vãng lai',
                    'email' => $email,
                    'phone' => $phone,
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                    'role' => 'guest',
                    'status' => true,
                ]);
                $userId = $guestUser->id;
                $shouldAutoLogin = true;
            }
        }

        // BẮT BUỘC: Mã khuyến mãi chỉ dùng được sau khi đăng nhập thành công
        if ($request->filled('coupon_code') && !Auth::check()) {
            return back()->withInput()->withErrors(['coupon_code' => 'Mã khuyến mãi chỉ sử dụng được sau khi đăng nhập thành công. Vui lòng đăng nhập để áp dụng ưu đãi!']);
        }

        DB::beginTransaction();
        try {
            // Tính tổng giá trị đơn hàng
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($items as $item) {
                $dish = Dish::find($item['id']);
                if ($dish) {
                    $qty = max(1, intval($item['quantity']));
                    $price = $dish->price;
                    $totalAmount += $price * $qty;

                    $orderItemsData[] = [
                        'dish_id' => $dish->id,
                        'quantity' => $qty,
                        'price' => $price,
                    ];
                }
            }

            // Kiểm tra xem có sản phẩm hợp lệ nào không
            if (empty($orderItemsData)) {
                DB::rollBack();
                return back()->withErrors(['cart_items' => 'Không có món ăn hợp lệ nào trong giỏ hàng.']);
            }

            // Tính giảm giá coupon nếu có
            $discountAmount = 0;
            $couponId = null;
            if ($request->filled('coupon_code')) {
                $code = strtoupper($request->input('coupon_code'));
                $coupon = \App\Models\Coupon::where('coupon_code', $code)->first();
                if ($coupon) {
                    $validation = $coupon->isValidFor($totalAmount);
                    if ($validation['valid']) {
                        $couponId = $coupon->id;
                        $discountAmount = $coupon->calculateDiscount($totalAmount);

                        if ($coupon->usage_limit !== null) {
                            $coupon->decrement('usage_limit');
                        }

                        // Đánh dấu mã đã dùng trong ví ưu đãi của user
                        \App\Models\UserCoupon::updateOrCreate(
                            ['user_id' => $userId, 'coupon_id' => $coupon->id],
                            ['is_used' => true, 'used_at' => now()]
                        );
                    }
                }
            }

            // Tạo đơn hàng mới
            $order = new Order;
            $order->user_id = $userId;
            $order->order_type = 'single';
            $order->total_amount = $totalAmount;
            $order->final_amount = max(0, $totalAmount - $discountAmount);
            if ($couponId) {
                $order->coupon_id = $couponId;
            }

            // Map payment methods: cod -> cash, atm -> bank_transfer, momo -> momo
            $paymentMethod = 'cash';
            if ($request->cart_payment === 'atm') {
                $paymentMethod = 'bank_transfer';
            } elseif ($request->cart_payment === 'momo') {
                $paymentMethod = 'momo';
            }
            $order->payment_method = $paymentMethod;
            $order->payment_status = 'pending';
            if ($paymentMethod === 'cash') {
                $order->order_status = 'pending';
            } else {
                $order->order_status = 'pending';
            }
            $order->health_notes = 'Giao hàng: '.$request->cart_time.'. SĐT: '.$request->cart_phone.'. Địa chỉ: '.$request->cart_address;
            $order->save();

            // Lưu danh sách sản phẩm của đơn hàng
            foreach ($orderItemsData as $itemData) {
                $orderItem = new OrderItem;
                $orderItem->order_id = $order->id;
                $orderItem->dish_id = $itemData['dish_id'];
                $orderItem->quantity = $itemData['quantity'];
                $orderItem->price = $itemData['price'];
                $orderItem->save();
            }

            DB::commit();

            try {
                event(new \App\Events\OrderUpdated($order, 'created'));
            } catch (\Exception $broadcastException) {
                \Illuminate\Support\Facades\Log::warning('Broadcasting failed: ' . $broadcastException->getMessage());
            }

            try {
                if ($order->user && $order->user->email) {
                    Mail::to($order->user->email)->queue(new OrderPlacedMail($order));
                }
            } catch (\Exception $mailError) {
                \Illuminate\Support\Facades\Log::warning('OrderPlacedMail queueing warning: ' . $mailError->getMessage());
            }

            if (!Auth::check() && $shouldAutoLogin) {
                Auth::loginUsingId($userId);
            }

            if ($paymentMethod === 'momo') {
                return redirect()->route('thanhtoan_momo', ['order_id' => $order->id, 'amount' => $order->final_amount]);
            }

            if ($paymentMethod === 'bank_transfer') {
                return redirect()->route('thanhtoan_chuyenkhoan', ['order_id' => $order->id, 'amount' => $order->final_amount]);
            }

            return redirect()->route('giohang')->with('success', 'Đơn hàng FDL-'.$order->id.' đã được đặt thành công!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Đã xảy ra lỗi trong quá trình đặt hàng: '.$e->getMessage()]);
        }
    }

    // Trang hiển thị mua hàng/giỏ hàng
    public function checkoutPage()
    {
        $selectedDishId = session('selected_dish_id') ?? session('added_dish.id');
        $currentDish = $selectedDishId ? Dish::with('category')->find($selectedDishId) : null;
        $relatedDishes = $currentDish ? $currentDish->getRelatedDishes(6) : collect();

        $now = now()->format('Y-m-d');
        $availableCoupons = \App\Models\Coupon::where(function($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->where(function($q) {
                $q->whereNull('usage_limit')->orWhere('usage_limit', '>', 0);
            })
            ->orderBy('discount_value', 'desc')
            ->get();

        $savedCouponIds = [];
        if (Auth::check()) {
            $savedCouponIds = Auth::user()->savedCoupons()->pluck('coupons.id')->toArray();
        }

        return view('client.buy', compact('relatedDishes', 'currentDish', 'availableCoupons', 'savedCouponIds'));
    }

    // Xử lý khách hàng hủy đơn hàng
    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'cancel_reason' => 'required|string',
            'cancel_detail' => 'nullable|string',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->order_status !== 'pending') {
            return redirect()->route('giohang')->withErrors(['error' => 'Chỉ có thể hủy đơn hàng ở trạng thái chờ xác nhận.']);
        }

        $order->order_status = 'cancelled';
        $order->health_notes = ($order->health_notes ? $order->health_notes."\n" : '').
            '[Hủy đơn - Lý do: '.$request->cancel_reason.($request->cancel_detail ? ' (Chi tiết: '.$request->cancel_detail.')' : '').']';
        $order->save();

        try {
            event(new \App\Events\OrderUpdated($order, 'cancelled'));
        } catch (\Exception $broadcastException) {
            \Illuminate\Support\Facades\Log::warning('Broadcasting failed: ' . $broadcastException->getMessage());
        }

        return redirect()->route('giohang')->with('success', 'Đơn hàng FDL-'.$order->id.' đã được hủy thành công.');
    }

    // Xử lý gửi đánh giá chất lượng đơn hàng
    // Hiển thị trang đánh giá món ăn sau khi giao hàng thành công (Có thể đánh giá hoặc không đánh giá)
    public function showOrderReviewPage(Request $request, $id)
    {
        $order = Order::with(['orderItems.dish.category', 'reviews.user', 'user'])->findOrFail($id);

        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập trang đánh giá của đơn hàng này.');
        }

        $existingReview = $order->reviews->whereNull('dish_id')->first() ?? $order->reviews->first();

        return view('client.order_review', compact('order', 'existingReview'));
    }

    // Xử lý gửi đánh giá đơn hàng và các món ăn
    public function reviewOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'rating_stars' => 'required|integer|between:1,5',
            'review_comment' => 'required|string',
        ]);

        $order = Order::with('orderItems')->where('id', $request->order_id)
            ->where(function($q) {
                if (Auth::check()) {
                    $q->where('user_id', Auth::id());
                }
            })
            ->firstOrFail();

        $redirectTo = $request->input('redirect_to', 'giohang');

        // Kiểm tra xem đã đánh giá chưa
        $exists = Review::where('order_id', $order->id)->whereNull('dish_id')->exists();
        if ($exists) {
            if ($redirectTo === 'review_page') {
                return redirect()->route('donhang.danhgia', $order->id)->withErrors(['error' => 'Đơn hàng này đã được đánh giá trước đó.']);
            }
            return redirect()->route('giohang')->withErrors(['error' => 'Đơn hàng này đã được đánh giá trước đó.']);
        }

        $userId = Auth::id() ?? $order->user_id;

        // 1. Lưu đánh giá chung cho đơn hàng
        Review::create([
            'user_id' => $userId,
            'order_id' => $order->id,
            'rating' => $request->rating_stars,
            'comment' => $request->review_comment,
        ]);

        // 2. Tự động lưu đánh giá cho từng món ăn trong đơn hàng
        foreach ($order->orderItems as $item) {
            Review::create([
                'user_id' => $userId,
                'order_id' => $order->id,
                'dish_id' => $item->dish_id,
                'rating' => $request->rating_stars,
                'comment' => $request->review_comment,
            ]);
        }

        \App\Models\Dish::clearReviewsSummaryCache();
        $order->touch();

        try {
            event(new \App\Events\OrderUpdated($order, 'reviewed'));
        } catch (\Exception $broadcastException) {
            \Illuminate\Support\Facades\Log::warning('Broadcasting failed: ' . $broadcastException->getMessage());
        }

        if ($redirectTo === 'review_page') {
            return redirect()->route('donhang.danhgia', $order->id)->with('success', 'Cảm ơn bạn đã gửi đánh giá cho đơn hàng FDL-'.$order->id.'!');
        }

        return redirect()->route('giohang')->with('success', 'Cảm ơn bạn đã gửi đánh giá cho đơn hàng FDL-'.$order->id.'!');
    }

    // Trang thanh toán / quét mã QR (VietQR / MoMo)
    public function momoMethod(Request $request)
    {
        return $this->handleTransferView($request, 'momo');
    }

    public function atmMethod(Request $request)
    {
        return $this->handleTransferView($request, 'bank');
    }

    public function transferPayment(Request $request)
    {
        return $this->handleTransferView($request, 'bank');
    }

    protected function handleTransferView(Request $request, string $payment_type)
    {
        $order_id = $request->input('order_id');

        if (!$order_id) {
            return redirect()->route('giohang')->with('error', 'Vui lòng cung cấp mã đơn hàng cần thanh toán.');
        }

        $order = Order::with('user')->find($order_id);
        if (!$order) {
            return redirect()->route('giohang')->with('error', 'Đơn hàng #FDL-' . $order_id . ' không tồn tại trong hệ thống.');
        }

        // Nếu đơn hàng đã hoàn tất thanh toán, chuyển hướng tới trang tra cứu đơn hàng
        if ($order->payment_status === 'paid') {
            return redirect()->route('tracuu', ['order_id' => 'FDL-' . $order->id])
                ->with('success', 'Đơn hàng #FDL-' . $order->id . ' đã được thanh toán thành công!');
        }

        $amount = (float) $order->final_amount;

        return view('client.transfer_payment', compact('order', 'order_id', 'amount', 'payment_type'));
    }

    public function paymentPage(Request $request, $id)
    {
        $order = Order::with('user')->findOrFail($id);

        if (!Auth::check() || Auth::id() !== $order->user_id) {
            abort(403);
        }

        if ($order->payment_method === 'cash') {
            return view('client.payment_choice', compact('order'));
        }

        if ($order->payment_method === 'momo') {
            return redirect()->route('thanhtoan_momo', ['order_id' => $order->id, 'amount' => $order->final_amount]);
        }

        return redirect()->route('thanhtoan_chuyenkhoan', ['order_id' => $order->id, 'amount' => $order->final_amount]);
    }

    public function confirmCod(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('payment_method', 'cash')
            ->firstOrFail();

        return redirect()->route('giohang')
            ->with('success', 'Bạn đã chọn thanh toán tiền mặt khi nhận hàng. Shipper sẽ thu tiền khi giao đơn FDL-'.$order->id.'.');
    }

    public function selectPaymentMethod(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string|in:cash,bank_transfer,momo',
        ]);

        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $order->payment_method = $request->payment_method;
        $order->payment_status = 'pending';

        if ($request->payment_method === 'momo') {
            $order->save();
            return redirect()->route('thanhtoan_momo', ['order_id' => $order->id, 'amount' => $order->final_amount]);
        }

        if ($request->payment_method === 'bank_transfer') {
            $order->save();
            return redirect()->route('thanhtoan_chuyenkhoan', ['order_id' => $order->id, 'amount' => $order->final_amount]);
        }

        // Nếu là tiền mặt (COD) -> Đơn hàng chuyển sang trạng thái đang chuẩn bị và chuyển hướng sang trang tra cứu đơn hàng
        $order->order_status = 'preparing';
        $order->save();

        // Xóa giỏ hàng session
        session()->forget('cart');

        return redirect()->route('tracuu', ['order_id' => 'FDL-' . $order->id])
            ->with('success', 'Đặt hàng thành công! Đơn hàng #FDL-' . $order->id . ' đã được tiếp nhận và đang được bếp chế biến.');
    }

    public function completePayment($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return redirect()->route('giohang')
            ->with('success', 'Thanh toán đơn hàng FDL-'.$order->id.' thành công! Đơn hàng đang được nhà hàng chuẩn bị.');
    }

    public function notifyBankTransferPayment(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
            'amount' => 'required|numeric',
            'content' => 'required|string',
            'status' => 'required|string|in:success,failed',
            'transaction_id' => 'nullable|string',
            'bank_reference' => 'nullable|string',
            'bank_code' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu chuyển khoản không hợp lệ.',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::findOrFail($request->order_id);
        $expectedContent = 'FDL-' . $order->id;
        $receivedContent = strtoupper(trim($request->content));
        $expectedAmount = (float) $order->final_amount;
        $receivedAmount = (float) $request->amount;

        if ($request->status !== 'success') {
            return response()->json(['success' => false, 'message' => 'Giao dịch chưa thành công.']);
        }

        if ($receivedContent !== $expectedContent && $receivedContent !== (string)$order->id && strpos($receivedContent, 'FDL' . $order->id) === false) {
            \Illuminate\Support\Facades\Log::warning('Bank transfer content mismatch', [
                'order_id' => $order->id,
                'expected' => $expectedContent,
                'received' => $request->content,
            ]);
            return response()->json(['success' => false, 'message' => 'Nội dung chuyển khoản không khớp.']);
        }

        if (abs($receivedAmount - $expectedAmount) > 0.01) {
            \Illuminate\Support\Facades\Log::warning('Bank transfer amount mismatch', [
                'order_id' => $order->id,
                'expected' => $expectedAmount,
                'received' => $receivedAmount,
            ]);
            return response()->json(['success' => false, 'message' => 'Số tiền chuyển khoản không đúng.']);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['success' => true, 'message' => 'Đơn hàng đã được xác nhận trước đó.']);
        }

        $order->payment_status = 'paid';
        $order->payment_transaction_id = $request->transaction_id;
        $order->payment_bank_reference = $request->bank_reference;
        $order->payment_bank = $request->bank_code;
        $order->payment_amount = $receivedAmount;
        $order->payment_content = $request->content;
        $order->payment_paid_at = $request->paid_at ?: now();
        if ($order->order_status === 'pending') {
            $order->order_status = 'confirmed';
        }
        $order->save();

        try {
            event(new \App\Events\OrderUpdated($order, 'paid'));
        } catch (\Exception $broadcastException) {
            \Illuminate\Support\Facades\Log::warning('Broadcasting failed: ' . $broadcastException->getMessage());
        }

        try {
            if ($order->user && $order->user->email) {
                Mail::to($order->user->email)->send(new OrderPlacedMail($order));
            }
        } catch (\Exception $mailError) {
            \Illuminate\Support\Facades\Log::warning('OrderPlacedMail sending warning: ' . $mailError->getMessage());
        }

        try {
            $telegram = app(\App\Services\TelegramService::class);
            $telegram->sendMessage("✅ <b>Đơn hàng đã thanh toán thành công (Chuyển khoản):</b>\nMã đơn: #FDL-{$order->id}\nTổng tiền: " . number_format($order->final_amount, 0, ',', '.') . " VNĐ\nTrạng thái: Đã nhận tiền. Bếp đang chuẩn bị món!");
        } catch (\Exception $telError) {
            \Illuminate\Support\Facades\Log::warning('Telegram sending warning: ' . $telError->getMessage());
        }

        return response()->json(['success' => true, 'payment_status' => 'paid']);
    }

    public function payosWebhook(Request $request)
    {
        $webhookData = $request->all();

        $payOS = app(\App\Services\PayOSService::class);

        \Illuminate\Support\Facades\Log::info('PayOS Webhook received', $webhookData);

        if ($payOS->validateWebhook($webhookData)) {
            $data = $webhookData['data'];
            $orderCode = $data['orderCode'];

            $order = Order::find($orderCode);
            if ($order && $order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
                if ($order->order_status === 'pending') {
                    $order->order_status = 'preparing';
                }
                $order->payment_paid_at = now();
                $order->payment_transaction_id = $data['reference'] ?? ('PAYOS_' . time());
                $order->save();

                try {
                    event(new \App\Events\OrderUpdated($order, 'paid'));
                } catch (\Exception $broadcastException) {
                    \Illuminate\Support\Facades\Log::warning('Broadcasting failed: ' . $broadcastException->getMessage());
                }

                try {
                    if ($order->user && $order->user->email) {
                        Mail::to($order->user->email)->send(new OrderPlacedMail($order));
                    }
                } catch (\Exception $mailError) {
                    \Illuminate\Support\Facades\Log::warning('OrderPlacedMail sending warning: ' . $mailError->getMessage());
                }

                try {
                    $telegram = app(\App\Services\TelegramService::class);
                    $telegram->sendMessage("✅ <b>Đơn hàng đã thanh toán qua Webhook:</b>\nMã đơn: #FDL-{$order->id}\nTổng tiền: " . number_format($order->final_amount, 0, ',', '.') . " VNĐ\nTrạng thái: Đã nhận tiền. Bếp đang chuẩn bị món!");
                } catch (\Exception $telError) {
                    \Illuminate\Support\Facades\Log::warning('Telegram sending warning: ' . $telError->getMessage());
                }
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
    }

    // AJAX Polling for Order updates
    public function pollOrders(Request $request)
    {
        $since = $request->query('since');

        if (!$since) {
            return response()->json([
                'updates' => [],
                'timestamp' => now()->toDateTimeString(),
            ]);
        }

        try {
            $sinceDate = \Carbon\Carbon::parse($since);
        } catch (\Exception $e) {
            return response()->json([
                'updates' => [],
                'timestamp' => now()->toDateTimeString(),
            ]);
        }

        $query = \App\Models\Order::with(['user', 'orderItems.dish']);

        if (\Illuminate\Support\Facades\Auth::check() && in_array(\Illuminate\Support\Facades\Auth::user()->role, ['admin', 'staff'])) {
            // Admins and staff get updates for all orders
        } else {
            // Regular customers get updates only for their own orders
            $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
        }

        $orders = $query->where('updated_at', '>', $sinceDate)
                        ->orderBy('updated_at', 'asc')
                        ->get();

        $updates = [];
        foreach ($orders as $order) {
            $action = 'status_updated';
            
            $isReviewed = \App\Models\Review::where('order_id', $order->id)->exists();

            if ($order->created_at->gt($sinceDate) || $order->created_at->eq($order->updated_at)) {
                $action = 'created';
            } elseif ($order->order_status === 'cancelled') {
                $action = 'cancelled';
            } elseif ($isReviewed && $order->updated_at->gt($sinceDate)) {
                $action = 'reviewed';
            } elseif ($order->payment_status === 'paid' && $order->updated_at->gt($sinceDate)) {
                $action = 'paid';
            }

            $orderData = $order->toArray();
            $orderData['is_reviewed'] = \App\Models\Review::where('order_id', $order->id)->exists();

            $updates[] = [
                'order' => $orderData,
                'action' => $action,
            ];
        }

        return response()->json([
            'updates' => $updates,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }


    public function validateCoupon(Request $request)
    {
        // 1. BẮT BUỘC ĐĂNG NHẬP: Mã khuyến mãi chỉ dùng được sau khi đăng nhập thành công
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'message' => 'Mã khuyến mãi chỉ dùng được sau khi đăng nhập thành công. Vui lòng đăng nhập để áp dụng ưu đãi!'
            ]);
        }

        $code = strtoupper(trim($request->input('code')));
        $totalAmount = floatval($request->input('total_amount'));

        $coupon = \App\Models\Coupon::where('coupon_code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi không tồn tại.'
            ]);
        }

        $check = $coupon->isValidFor($totalAmount);
        if (!$check['valid']) {
            return response()->json([
                'success' => false,
                'message' => $check['message']
            ]);
        }

        // Tính giảm giá
        $discountAmount = $coupon->calculateDiscount($totalAmount);

        return response()->json([
            'success' => true,
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->coupon_code,
            'discount_amount' => $discountAmount,
            'formatted_discount' => $coupon->formatted_discount,
            'final_amount' => max(0, $totalAmount - $discountAmount),
            'message' => 'Áp dụng mã ' . $coupon->coupon_code . ' thành công! (' . $coupon->formatted_discount . ')'
        ]);
    }

    // Tự động tìm & áp dụng mã khuyến mãi TỐT NHẤT phù hợp với giỏ hàng
    public function getBestAvailableCoupon(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'message' => 'Vui lòng đăng nhập để hệ thống tự động áp dụng mã khuyến mãi phù hợp.'
            ]);
        }

        $totalAmount = floatval($request->input('total_amount', 0));
        $user = Auth::user();

        $now = now()->format('Y-m-d');
        // Lấy tất cả coupon active
        $allCoupons = \App\Models\Coupon::where(function($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->where(function($q) {
                $q->whereNull('usage_limit')->orWhere('usage_limit', '>', 0);
            })
            ->get();

        $savedCouponIds = $user->savedCoupons()->pluck('coupons.id')->toArray();

        $eligibleCoupons = [];
        $ineligibleCoupons = [];
        $bestCoupon = null;
        $maxDiscount = 0;

        foreach ($allCoupons as $coupon) {
            $isSaved = in_array($coupon->id, $savedCouponIds);
            $check = $coupon->isValidFor($totalAmount);

            $itemData = [
                'id' => $coupon->id,
                'coupon_code' => $coupon->coupon_code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => (float) $coupon->discount_value,
                'formatted_discount' => $coupon->formatted_discount,
                'min_order_value' => (float) $coupon->min_order_value,
                'formatted_min_order' => $coupon->formatted_min_order,
                'is_expiring_soon' => $coupon->is_expiring_soon,
                'days_remaining' => $coupon->days_remaining,
                'end_date' => $coupon->end_date ? $coupon->end_date->format('d/m/Y') : null,
                'is_saved' => $isSaved,
            ];

            if ($check['valid']) {
                $discount = $coupon->calculateDiscount($totalAmount);
                $itemData['discount_amount'] = $discount;
                $itemData['is_eligible'] = true;
                $eligibleCoupons[] = $itemData;

                if ($discount > $maxDiscount) {
                    $maxDiscount = $discount;
                    $bestCoupon = $coupon;
                }
            } else {
                $itemData['discount_amount'] = 0;
                $itemData['is_eligible'] = false;
                $itemData['ineligible_reason'] = $check['message'];
                $itemData['need_more'] = $check['need_more'] ?? 0;
                $ineligibleCoupons[] = $itemData;
            }
        }

        // Sắp xếp mã đủ điều kiện theo số tiền giảm giảm dần
        usort($eligibleCoupons, fn($a, $b) => $b['discount_amount'] <=> $a['discount_amount']);

        return response()->json([
            'success' => true,
            'has_coupon' => $bestCoupon !== null,
            'best_coupon' => $bestCoupon ? [
                'id' => $bestCoupon->id,
                'coupon_code' => $bestCoupon->coupon_code,
                'discount_amount' => $maxDiscount,
                'formatted_discount' => $bestCoupon->formatted_discount,
                'formatted_min_order' => $bestCoupon->formatted_min_order,
                'final_amount' => max(0, $totalAmount - $maxDiscount),
                'message' => 'Đã tự động chọn mã ' . $bestCoupon->coupon_code . ' (' . $bestCoupon->formatted_discount . ')'
            ] : null,
            'eligible_coupons' => $eligibleCoupons,
            'ineligible_coupons' => $ineligibleCoupons,
            'all_coupons' => array_merge($eligibleCoupons, $ineligibleCoupons),
        ]);
    }

    public function pollUserOrders(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['orders' => []]);
        }

        $orders = Order::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get(['id', 'order_status', 'payment_status', 'updated_at']);

        return response()->json([
            'orders' => $orders,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    // API kiểm tra trạng thái thanh toán đơn hàng (dùng cho polling chuyển khoản/momo)
    public function getPaymentStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'id' => $order->id,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
        ]);
    }
}


