<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Tự động chuyển đổi giá từ USD/số nhỏ sang VNĐ nếu phát hiện giá nhỏ hơn 100
        $smallPriceDishes = \App\Models\Dish::where('price', '<', 100)->get();
        if ($smallPriceDishes->isNotEmpty()) {
            foreach ($smallPriceDishes as $dish) {
                if ($dish->price < 15) {
                    $dish->price = $dish->price * 10000;
                } else {
                    $dish->price = $dish->price * 1000;
                }
                $dish->save();
            }
        }

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

        // 5. Lấy cấu hình trang chủ từ bảng settings
        $settings = \App\Models\Setting::pluck('value', 'key')->all();

        return view('client.shop', compact('categories', 'allDishes', 'query', 'currentSelectedDish', 'sameCategoryDishes', 'frequentDishes', 'timeRecommendation', 'lastSearchQuery', 'lastSearchDishes', 'settings'));
    }

    // Chi tiết món ăn dành cho khách hàng
    public function dishDetail($id)
    {
        $dish = \App\Models\Dish::with('category')->where('is_available', true)->findOrFail($id);
        session(['selected_dish_id' => $dish->id]);
        $relatedDishes = $dish->getRelatedDishes(6);

        return view('client.dish_detail', compact('dish', 'relatedDishes'));
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

    // AJAX Polling for settings and data changes
    public function pollSettings(Request $request)
    {
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

        return response()->json([
            'fingerprint' => $fingerprint,
            'settings' => $settings,
            'timestamps' => [
                'dish' => $lastDishUpdate,
                'category' => $lastCategoryUpdate,
                'coupon' => $lastCouponUpdate,
                'package' => $lastPackageUpdate,
            ]
        ]);
    }

    // AJAX Polling for public guest order tracking
    public function pollTrackedOrder(Request $request)
    {
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
}
