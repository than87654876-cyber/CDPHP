<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        if ($query) {
            session(['last_search_query' => $query]);
        }

        $sort = $request->input('sort', 'rating_desc');

        // Lấy tất cả các món ăn cho Tab "Tất cả"
        $allDishesQuery = \App\Models\Dish::where('is_available', true);
        if ($query) {
            $allDishesQuery->where('dish_name', 'like', '%'.$query.'%');
        }
        $allDishes = $allDishesQuery->get();

        // Sắp xếp món ăn theo tiêu chí (Mặc định: Số sao đánh giá cao nhất đẩy lên đầu)
        if ($sort === 'price_asc') {
            $allDishes = $allDishes->sortBy('price')->values();
        } elseif ($sort === 'price_desc') {
            $allDishes = $allDishes->sortByDesc('price')->values();
        } else {
            // Món có điểm đánh giá rating_score cao nhất xếp lên đầu
            $allDishes = $allDishes->sortByDesc(function ($dish) {
                return $dish->rating_score * 1000 + $dish->reviews_count;
            })->values();
        }

        // Lấy các danh mục và các món ăn thuộc danh mục đó
        $categories = Category::with(['dishes' => function ($q) use ($query) {
            $q->where('is_available', true);
            if ($query) {
                $q->where('dish_name', 'like', '%'.$query.'%');
            }
        }])->get();

        // Sắp xếp các món trong từng danh mục theo số sao đánh giá cao nhất
        foreach ($categories as $cat) {
            if ($sort === 'price_asc') {
                $cat->setRelation('dishes', $cat->dishes->sortBy('price')->values());
            } elseif ($sort === 'price_desc') {
                $cat->setRelation('dishes', $cat->dishes->sortByDesc('price')->values());
            } else {
                $cat->setRelation('dishes', $cat->dishes->sortByDesc(function ($dish) {
                    return $dish->rating_score * 1000 + $dish->reviews_count;
                })->values());
            }
        }

        // 1. THUẬT TOÁN: Đề xuất món ăn cùng loại (Cùng category_id với món đang chọn/xem)
        $selectedDishId = $request->query('dish_id') ?? session('selected_dish_id') ?? session('added_dish.id');
        $currentSelectedDish = null;

        if ($selectedDishId) {
            $currentSelectedDish = \App\Models\Dish::with('category')->where('is_available', true)->find($selectedDishId);
        }

        // Nếu chưa chọn món cụ thể, mặc định lấy món đầu tiên hoặc món bán chạy
        if (!$currentSelectedDish) {
            $topDishId = \App\Models\OrderItem::select('dish_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_qty'))
                ->groupBy('dish_id')
                ->orderByDesc('total_qty')
                ->value('dish_id');

            if ($topDishId) {
                $currentSelectedDish = \App\Models\Dish::with('category')->where('is_available', true)->find($topDishId);
            }

            if (!$currentSelectedDish) {
                $currentSelectedDish = \App\Models\Dish::with('category')->where('is_available', true)->first();
            }
        }

        $sameCategoryDishes = $currentSelectedDish ? $currentSelectedDish->getRelatedDishes(6) : collect();

        // 2. THUẬT TOÁN: Món hay mua (Lịch sử hoặc Top món)
        $frequentDishes = collect();
        if (auth()->check()) {
            $frequentDishIds = \App\Models\OrderItem::whereHas('order', function($q) {
                    $q->where('user_id', auth()->id());
                })
                ->select('dish_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_qty'))
                ->groupBy('dish_id')
                ->having('total_qty', '>=', 2)
                ->orderByDesc('total_qty')
                ->pluck('dish_id');

            $frequentDishes = \App\Models\Dish::whereIn('id', $frequentDishIds)->where('is_available', true)->get();
        }

        if ($frequentDishes->isEmpty()) {
            $topDishIds = \App\Models\OrderItem::select('dish_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_qty'))
                ->groupBy('dish_id')
                ->orderByDesc('total_qty')
                ->pluck('dish_id');

            $frequentDishes = \App\Models\Dish::whereIn('id', $topDishIds)->where('is_available', true)->take(4)->get();

            if ($frequentDishes->isEmpty()) {
                $frequentDishes = \App\Models\Dish::where('is_available', true)->take(4)->get();
            }
        }

        // 3. THUẬT TOÁN: Đề xuất theo khung giờ trong ngày
        $hour = now()->hour;
        $timeRecommendation = [
            'period' => 'Sáng',
            'title' => '🌅 Bữa Sáng Dinh Dưỡng Năng Lượng',
            'dishes' => collect()
        ];

        if ($hour >= 5 && $hour < 11) {
            $timeRecommendation['period'] = 'Sáng';
            $timeRecommendation['title'] = '🌅 Bữa Sáng Dinh Dưỡng Cân Bằng';
            $timeRecommendation['dishes'] = \App\Models\Dish::where('is_available', true)
                ->where(function($q) {
                    $q->where('dish_name', 'like', '%cháo%')
                      ->orWhere('dish_name', 'like', '%bánh mì%')
                      ->orWhere('dish_name', 'like', '%sữa%')
                      ->orWhere('dish_name', 'like', '%sinh tố%')
                      ->orWhere('category_id', 1);
                })->take(4)->get();
        } elseif ($hour >= 11 && $hour < 14) {
            $timeRecommendation['period'] = 'Trưa';
            $timeRecommendation['title'] = '☀️ Bữa Trưa Đậm Đà Năng Lượng';
            $timeRecommendation['dishes'] = \App\Models\Dish::where('is_available', true)
                ->where(function($q) {
                    $q->where('dish_name', 'like', '%cơm%')
                      ->orWhere('dish_name', 'like', '%bún%')
                      ->orWhere('dish_name', 'like', '%ức gà%');
                })->take(4)->get();
        } elseif ($hour >= 14 && $hour < 17) {
            $timeRecommendation['period'] = 'Chiều';
            $timeRecommendation['title'] = '🍰 Thức Uống & Món Nhẹ Tráng Miệng Chiều';
            $timeRecommendation['dishes'] = \App\Models\Dish::where('is_available', true)
                ->where(function($q) {
                    $q->where('dish_name', 'like', '%bánh%')
                      ->orWhere('dish_name', 'like', '%thạch%')
                      ->orWhere('dish_name', 'like', '%chè%')
                      ->orWhere('category_id', 2);
                })->take(4)->get();
        } else {
            $timeRecommendation['period'] = 'Tối';
            $timeRecommendation['title'] = '🌙 Bữa Tối Thưởng Thức Ấm Cúng';
            $timeRecommendation['dishes'] = \App\Models\Dish::where('is_available', true)->take(4)->get();
        }

        // 4. Đề xuất từ lịch sử tìm kiếm gần nhất
        $lastSearchQuery = session('last_search_query');
        $lastSearchDishes = collect();
        if ($lastSearchQuery && !$query) {
            $lastSearchDishes = \App\Models\Dish::where('is_available', true)
                ->where('dish_name', 'like', '%'.$lastSearchQuery.'%')
                ->take(4)->get();
        }

        // 5. Lịch sử món đã xem gần đây (Recently Viewed Dishes)
        $recentIds = session()->get('recently_viewed_dishes', []);
        $recentlyViewedDishes = collect();
        if (!empty($recentIds) && is_array($recentIds)) {
            $dishesById = \App\Models\Dish::with('category')
                ->whereIn('id', $recentIds)
                ->where('is_available', true)
                ->get()
                ->keyBy('id');

            foreach ($recentIds as $rId) {
                if (isset($dishesById[$rId])) {
                    $recentlyViewedDishes->push($dishesById[$rId]);
                }
            }
        }

        // 6. Lấy cấu hình trang chủ từ bảng settings (cached 1 hour)
        $settings = \Illuminate\Support\Facades\Cache::remember('site_global_settings', 3600, function () {
            return \App\Models\Setting::pluck('value', 'key')->all();
        });

        // 7. Lấy danh sách mã khuyến mãi cho trang chủ (Pop-up chào mừng & Kho ưu đãi)
        $activeCoupons = \App\Models\Coupon::active()->orderByDesc('discount_value')->get();

        $savedCouponIds = [];
        if (auth()->check()) {
            $savedCouponIds = auth()->user()->savedCoupons()->pluck('coupons.id')->toArray();
        }

        $expiringCoupons = $activeCoupons->filter(fn($c) => $c->is_expiring_soon)->values();

        return view('client.shop', compact(
            'categories', 'allDishes', 'query', 'currentSelectedDish',
            'sameCategoryDishes', 'frequentDishes', 'timeRecommendation',
            'lastSearchQuery', 'lastSearchDishes', 'recentlyViewedDishes', 'settings',
            'activeCoupons', 'savedCouponIds', 'expiringCoupons'
        ));
    }

    // Chi tiết món ăn dành cho khách hàng kèm danh sách đánh giá
    public function dishDetail($id)
    {
        $dish = \App\Models\Dish::with(['category', 'reviews.user'])
            ->where('is_available', true)
            ->findOrFail($id);

        session(['selected_dish_id' => $dish->id]);

        // Cập nhật danh sách món đã xem gần đây (LIFO - Đưa món vừa xem lên đầu)
        $recentlyViewed = session()->get('recently_viewed_dishes', []);
        if (!is_array($recentlyViewed)) {
            $recentlyViewed = [];
        }
        $recentlyViewed = array_values(array_diff($recentlyViewed, [$dish->id]));
        array_unshift($recentlyViewed, $dish->id);
        $recentlyViewed = array_slice($recentlyViewed, 0, 10);
        session(['recently_viewed_dishes' => $recentlyViewed]);

        $relatedDishes = $dish->getRelatedDishes(6);
        $reviews = $dish->reviews;

        return view('client.dish_detail', compact('dish', 'relatedDishes', 'reviews'));
    }

    // Gửi đánh giá trực tiếp cho món ăn
    public function submitDishReview(Request $request, $id)
    {
        $dish = \App\Models\Dish::findOrFail($id);

        if (!auth()->check()) {
            return back()->with('error', 'Vui lòng đăng nhập để gửi đánh giá món ăn!');
        }

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000',
        ]);

        \App\Models\Review::create([
            'user_id' => auth()->id(),
            'dish_id' => $dish->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        \App\Models\Dish::clearReviewsSummaryCache();

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá cho món ' . $dish->dish_name . '!');
    }

    // Xóa lịch sử xem món ăn gần đây
    public function clearRecentlyViewed(Request $request)
    {
        session()->forget('recently_viewed_dishes');
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('success', 'Đã xóa lịch sử món ăn đã xem gần đây.');
    }

    public function shopLogged(Request $request)
    {
        return redirect()->route('trangchu');
    }


    // Chatbot gợi ý món ăn qua Google Gemini AI
    public function geminiChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'history' => 'nullable|array',
        ]);

        $gemini = app(\App\Services\GeminiService::class);
        $reply = $gemini->getSuggestion($request->message, $request->history ?? []);

        return response()->json([
            'success' => true,
            'reply' => $reply
        ]);
    }

    // Geocoding địa chỉ lấy tọa độ từ OpenStreetMap Nominatim
    public function geocodeAddress(Request $request)
    {
        $address = $request->query('address');
        if (empty($address)) {
            return response()->json(['success' => false, 'message' => 'Address is required.']);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'FOODELICIOUS-Jollibee-App'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                return response()->json([
                    'success' => true,
                    'lat' => $data['lat'],
                    'lon' => $data['lon']
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Address not found.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Tra cứu đơn hàng dành cho khách vãng lai
    public function trackOrder(Request $request)
    {
        $orderIdInput = $request->input('order_id');
        $email = $request->input('email');
        $phone = $request->input('phone');

        $myOrders = collect();

        // 1. Nếu người dùng đã đăng nhập -> Lấy toàn bộ đơn hàng của họ (mới nhất lên đầu)
        if (auth()->check()) {
            $myOrders = \App\Models\Order::where('user_id', auth()->id())
                ->with(['orderItems.dish', 'user'])
                ->orderByDesc('created_at')
                ->get();
        } elseif ($email || $phone) {
            // Nếu chưa đăng nhập nhưng tra cứu theo Email hoặc SĐT
            $myOrders = \App\Models\Order::whereHas('user', function($q) use ($email, $phone) {
                    $q->where(function($sub) use ($email, $phone) {
                        if ($email) $sub->where('email', trim($email));
                        if ($phone) $sub->orWhere('phone', trim($phone));
                    });
                })
                ->with(['orderItems.dish', 'user'])
                ->orderByDesc('created_at')
                ->get();
        }

        // 2. Đơn hàng cụ thể đang được chọn xem tiến độ
        $selectedOrder = null;
        if ($orderIdInput) {
            $cleanId = trim(str_ireplace('FDL-', '', $orderIdInput));
            $selectedOrder = \App\Models\Order::where('id', $cleanId)->with(['orderItems.dish', 'user'])->first();
        }

        if (!$selectedOrder && $myOrders->isNotEmpty()) {
            $selectedOrder = $myOrders->first();
        }

        return view('client.track_order', compact('myOrders', 'selectedOrder', 'orderIdInput', 'email', 'phone'));
    }

    // AJAX Polling for settings and data changes (Cached 15s)
    public function pollSettings(Request $request)
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $payload = \Illuminate\Support\Facades\Cache::remember('poll_settings_response', 15, function () {
            // 1. Get all settings
            $settings = \App\Models\Setting::pluck('value', 'key')->all();
            
            // Resolve logo_url to full asset/absolute URL
            if (isset($settings['logo_url'])) {
                $settings['logo_url'] = \Illuminate\Support\Str::startsWith($settings['logo_url'], 'http') 
                    ? $settings['logo_url'] 
                    : asset($settings['logo_url']);
            } else {
                $settings['logo_url'] = asset('logo.jpg');
            }

            // Resolve banner_image to full URL
            if (isset($settings['banner_image'])) {
                $settings['banner_image'] = \Illuminate\Support\Str::startsWith($settings['banner_image'], 'http') 
                    ? $settings['banner_image'] 
                    : asset($settings['banner_image']);
            } else {
                $settings['banner_image'] = asset('client/assets/img/hero-img.png');
            }

            // 2. Fetch max update times of core tables to build a fingerprint
            $lastDishUpdate = \App\Models\Dish::max('updated_at');
            $lastCategoryUpdate = \App\Models\Category::max('updated_at');
            $lastCouponUpdate = \App\Models\Coupon::max('updated_at');
            $lastPackageUpdate = \App\Models\ServicePackage::max('updated_at');

            $fingerprint = md5(json_encode([
                'settings' => $settings,
                'dish' => $lastDishUpdate,
                'category' => $lastCategoryUpdate,
                'coupon' => $lastCouponUpdate,
                'package' => $lastPackageUpdate,
            ]));

            return [
                'fingerprint' => $fingerprint,
                'settings' => $settings,
                'timestamps' => [
                    'dish' => $lastDishUpdate,
                    'category' => $lastCategoryUpdate,
                    'coupon' => $lastCouponUpdate,
                    'package' => $lastPackageUpdate,
                ]
            ];
        });

        return response()->json($payload);
    }

    // AJAX Polling for public guest order tracking
    public function pollTrackedOrder(Request $request)
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $orderId = $request->query('order_id');
        $email = $request->query('email');
        $phone = $request->query('phone');
        $lastStatus = $request->query('last_status');
        $lastPaymentStatus = $request->query('last_payment_status');

        if (!$orderId) {
            return response()->json(['error' => 'Missing order ID'], 400);
        }

        // Clean "FDL-" prefix
        $cleanId = trim(str_ireplace('FDL-', '', $orderId));
        $query = \App\Models\Order::where('id', $cleanId);

        // Validate that this order belongs to the user matching the email or phone
        $query->whereHas('user', function($q) use ($email, $phone) {
            $q->where(function($sub) use ($email, $phone) {
                if ($email) {
                    $sub->where('email', trim($email));
                }
                if ($phone) {
                    $sub->orWhere('phone', trim($phone));
                }
            });
        });

        $order = $query->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found or access denied'], 404);
        }

        // Check if there are any status modifications
        $changed = ($order->order_status !== $lastStatus) || ($order->payment_status !== $lastPaymentStatus);

        return response()->json([
            'changed' => $changed,
            'status' => $order->order_status,
            'order_status' => $order->order_status,
            'payment_status' => $order->payment_status,
        ]);
    }

    // API Tìm kiếm món ăn trực tiếp theo tên
    public function searchDishesApi(Request $request)
    {
        $q = trim($request->query('q', ''));
        if (empty($q)) {
            return response()->json(['success' => true, 'dishes' => []]);
        }

        $dishes = \App\Models\Dish::where('is_available', true)
            ->where('dish_name', 'like', '%' . $q . '%')
            ->take(8)
            ->get()
            ->map(function ($dish) {
                return [
                    'id' => $dish->id,
                    'dish_name' => $dish->dish_name,
                    'price' => $dish->price,
                    'formatted_price' => number_format($dish->price, 0, ',', '.') . 'đ',
                    'image' => $dish->image ? asset($dish->image) : null,
                    'description' => $dish->description ?? 'FOODDAILY Store'
                ];
            });

        return response()->json([
            'success' => true,
            'dishes' => $dishes
        ]);
    }

    // API Vòng quay "Hôm nay mình ăn gì?" – trả về tối đa 12 món theo mức giá tối đa
    public function spinDishesApi(Request $request)
    {
        $maxPrice = (int) $request->query('max_price', 100000);
        $maxPrice = max(1000, min(10000000, $maxPrice)); // sanity clamp

        $dishes = \App\Models\Dish::where('is_available', true)
            ->where('price', '<=', $maxPrice)
            ->inRandomOrder()
            ->take(12)
            ->get()
            ->map(function ($dish) {
                return [
                    'id'          => $dish->id,
                    'dish_name'   => $dish->dish_name,
                    'price'       => (float) $dish->price,
                    'description' => $dish->description ?? null,
                    'image'       => $dish->image ? asset($dish->image) : null,
                ];
            });

        return response()->json([
            'success' => true,
            'dishes'  => $dishes,
            'total'   => $dishes->count(),
        ]);
    }

    // API Chatbot tư vấn món ăn & hỗ trợ khách hàng
    public function chatbotAsk(Request $request)
    {
        $userMsg = trim($request->input('message', ''));

        if (empty($userMsg)) {
            return response()->json([
                'success' => false,
                'reply' => 'Xin chào! Bạn muốn tìm món ăn gì hôm nay?'
            ]);
        }

        $lowerMsg = mb_strtolower($userMsg, 'UTF-8');

        // Tìm kiếm các món ăn khớp với câu hỏi của khách hàng
        $matchedDishes = \App\Models\Dish::where('is_available', true)
            ->where(function ($q) use ($lowerMsg) {
                $words = explode(' ', $lowerMsg);
                foreach ($words as $w) {
                    if (mb_strlen($w, 'UTF-8') >= 2 && !in_array($w, ['tôi', 'muốn', 'ăn', 'tìm', 'món', 'có', 'không', 'cho', 'xin', 'cần'])) {
                        $q->orWhere('dish_name', 'like', '%' . $w . '%')
                          ->orWhere('description', 'like', '%' . $w . '%');
                    }
                }
            })
            ->take(4)
            ->get();

        if ($matchedDishes->isEmpty()) {
            // Fallback gợi ý top bán chạy
            $topDishes = \App\Models\Dish::where('is_available', true)->take(3)->get();
            $reply = "Dạ, hiện tại em chưa tìm thấy món khớp 100% với yêu cầu \"{$userMsg}\". Tuy nhiên bạn có thể thử các món hot bán chạy nhất của FOODDAILY bên dưới:";
            $dishesData = $topDishes->map(function($d) {
                return [
                    'id' => $d->id,
                    'dish_name' => $d->dish_name,
                    'price' => number_format($d->price, 0, ',', '.') . 'đ',
                    'image' => $d->image ? asset($d->image) : null,
                ];
            });
        } else {
            $reply = "Dạ, FOODDAILY có những món ăn ngon tuyệt hảo đúng chuẩn yêu cầu của bạn nè:";
            $dishesData = $matchedDishes->map(function($d) {
                return [
                    'id' => $d->id,
                    'dish_name' => $d->dish_name,
                    'price' => number_format($d->price, 0, ',', '.') . 'đ',
                    'image' => $d->image ? asset($d->image) : null,
                ];
            });
        }

        // Trợ lý thông minh trả lời các câu hỏi thường gặp
        if (str_contains($lowerMsg, 'giao hàng') || str_contains($lowerMsg, 'ship')) {
            $reply = "🚀 **Giao hàng siêu tốc:** FOODDAILY giao hàng tận nơi chỉ từ 20 - 30 phút trong nội thành TP. Hồ Chí Minh!";
        } elseif (str_contains($lowerMsg, 'thanh toán') || str_contains($lowerMsg, 'chuyển khoản') || str_contains($lowerMsg, 'momo')) {
            $reply = "💳 **Phương thức thanh toán:** FOODDAILY hỗ trợ Tiền mặt (COD), Chuyển khoản ngân hàng VietQR và Ví MoMo tiện lợi!";
        } elseif (str_contains($lowerMsg, 'tra cứu') || str_contains($lowerMsg, 'đơn hàng')) {
            $reply = "📦 Bạn có thể tra cứu tiến độ đơn hàng cực kỳ dễ dàng tại menu **[Tra cứu đơn]** phía trên header bằng Mã đơn FDL-xxx!";
        }

        return response()->json([
            'success' => true,
            'reply' => $reply,
            'dishes' => $dishesData ?? []
        ]);
    }

    // API Lưu mã khuyến mãi vào ví của User
    public function saveCoupon(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'message' => 'Vui lòng đăng nhập tài khoản để lưu mã khuyến mãi vào ví ưu đãi của bạn!'
            ]);
        }

        $couponId = $request->input('coupon_id');
        $couponCode = $request->input('coupon_code');

        $coupon = null;
        if ($couponId) {
            $coupon = \App\Models\Coupon::find($couponId);
        } elseif ($couponCode) {
            $coupon = \App\Models\Coupon::where('coupon_code', strtoupper($couponCode))->first();
        }

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã khuyến mãi không tồn tại.'
            ]);
        }

        $user = auth()->user();
        $existing = \App\Models\UserCoupon::where('user_id', $user->id)->where('coupon_id', $coupon->id)->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'already_saved' => true,
                'message' => 'Mã ' . $coupon->coupon_code . ' đã có sẵn trong ví ưu đãi của bạn rồi!'
            ]);
        }

        \App\Models\UserCoupon::create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'is_used' => false,
        ]);

        return response()->json([
            'success' => true,
            'saved' => true,
            'coupon_code' => $coupon->coupon_code,
            'message' => 'Đã lưu mã ' . $coupon->coupon_code . ' vào ví ưu đãi thành công!'
        ]);
    }

    // API Lưu tất cả mã khuyến mãi khả dụng vào ví
    public function saveAllCoupons(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'require_login' => true,
                'message' => 'Vui lòng đăng nhập tài khoản để thu thập tất cả mã ưu đãi!'
            ]);
        }

        $now = now()->format('Y-m-d');
        $activeCoupons = \App\Models\Coupon::where(function($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->where(function($q) {
                $q->whereNull('usage_limit')->orWhere('usage_limit', '>', 0);
            })
            ->get();

        $user = auth()->user();
        $savedCount = 0;
        foreach ($activeCoupons as $coupon) {
            $created = \App\Models\UserCoupon::firstOrCreate([
                'user_id' => $user->id,
                'coupon_id' => $coupon->id,
            ], [
                'is_used' => false,
            ]);
            if ($created->wasRecentlyCreated) {
                $savedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'saved_count' => $savedCount,
            'total_active' => $activeCoupons->count(),
            'message' => 'Đã thu thập thành công tất cả ' . $activeCoupons->count() . ' mã ưu đãi vào ví của bạn!'
        ]);
    }

    // API Lấy danh sách ưu đãi của user (đã lưu, sắp hết hạn, khả dụng)
    public function getMyCoupons(Request $request)
    {
        $now = now()->format('Y-m-d');
        $allCoupons = \App\Models\Coupon::where(function($q) use ($now) {
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
        $usedCouponIds = [];
        if (auth()->check()) {
            $userCoupons = \App\Models\UserCoupon::where('user_id', auth()->id())->get();
            $savedCouponIds = $userCoupons->pluck('coupon_id')->toArray();
            $usedCouponIds = $userCoupons->where('is_used', true)->pluck('coupon_id')->toArray();
        }

        $formatted = $allCoupons->map(function($c) use ($savedCouponIds, $usedCouponIds) {
            return [
                'id' => $c->id,
                'coupon_code' => $c->coupon_code,
                'discount_type' => $c->discount_type,
                'discount_value' => (float) $c->discount_value,
                'formatted_discount' => $c->formatted_discount,
                'min_order_value' => (float) $c->min_order_value,
                'formatted_min_order' => $c->formatted_min_order,
                'is_expiring_soon' => $c->is_expiring_soon,
                'days_remaining' => $c->days_remaining,
                'end_date' => $c->end_date ? $c->end_date->format('d/m/Y') : 'Không thời hạn',
                'is_saved' => in_array($c->id, $savedCouponIds),
                'is_used' => in_array($c->id, $usedCouponIds),
            ];
        });

        return response()->json([
            'success' => true,
            'is_logged_in' => auth()->check(),
            'coupons' => $formatted,
            'saved_count' => count($savedCouponIds),
        ]);
    }
}
