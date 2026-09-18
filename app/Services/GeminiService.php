<?php

namespace App\Services;

use App\Models\Dish;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Get Dish Suggestion from Gemini AI
     */
    public function getSuggestion($userPrompt, $chatHistory = [])
    {
        if (empty($this->apiKey)) {
            Log::warning('GEMINI_API_KEY is not set in config/services.php. Returning simulated FOODELICIOUS Chatbot response.');
            return $this->getMockResponse($userPrompt);
        }

        // Fetch available dishes with short-lived cache (30 minutes)
        $dishesJson = \Illuminate\Support\Facades\Cache::remember('gemini_dishes_menu', 1800, function () {
            $dishes = Dish::with('category')->where('is_available', true)->get()->map(function ($dish) {
                return [
                    'Tên món' => $dish->dish_name,
                    'Danh mục' => $dish->category ? $dish->category->category_name : 'Món ăn',
                    'Giá' => number_format($dish->price, 0, ',', '.') . ' VNĐ',
                    'Mô tả' => $dish->description
                ];
            })->toArray();

            return json_encode($dishes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        });

        $systemInstruction = "Bạn là trợ lý ảo/Chatbot tư vấn ẩm thực của cửa hàng thức ăn nhanh FOODELICIOUS. "
            . "Nhiệm vụ của bạn là lắng nghe nhu cầu của khách hàng, trả lời thân thiện, lịch sự và gợi ý món ăn phù hợp nhất từ thực đơn dưới đây. "
            . "Chỉ giới thiệu các món có trong danh sách thực đơn này. Trả lời ngắn gọn, có cấu trúc rõ ràng bằng tiếng Việt.\n\n"
            . "Thực đơn FOODELICIOUS hiện tại:\n{$dishesJson}";

        // Format contents including system context
        $contents = [];
        
        // Add history if any
        foreach ($chatHistory as $msg) {
            $contents[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['content']]]
            ];
        }

        // Add system instruction as part of prompt if history is empty, or pre-prompt it
        $prompt = $systemInstruction . "\n\nYêu cầu của khách hàng: " . $userPrompt;
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ];

        try {
            $response = Http::post("{$this->apiUrl}?key={$this->apiKey}", [
                'contents' => $contents
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($reply) {
                    return $reply;
                }
            }

            Log::error('Gemini API call failed: ' . $response->body());
            return $this->getMockResponse($userPrompt);
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
            return $this->getMockResponse($userPrompt);
        }
    }

    /**
     * Fallback mock chatbot responses
     */
    protected function getMockResponse($prompt)
    {
        $promptLower = mb_strtolower($prompt, 'UTF-8');
        
        if (str_contains($promptLower, 'gà') || str_contains($promptLower, 'chicken')) {
            return "Dạ FOODELICIOUS có món **Gà Rán** rất giòn rụm và đậm đà đó ạ! Ngoài ra bạn có thể dùng kèm **Mì Ý Sốt Bò Bằm** hoặc gọi **Combo Gà Rán + Khoai Tây Chiên + Nước Ngọt** để tiết kiệm hơn nhé! Mời bạn thêm món vào giỏ hàng.";
        }
        
        if (str_contains($promptLower, 'mì') || str_contains($promptLower, 'spaghetti') || str_contains($promptLower, 'ý')) {
            return "Dạ món **Mì Ý FOODELICIOUS** sốt bò bằm ngọt dịu chuẩn vị, phủ phô mai béo ngậy là lựa chọn tuyệt vời ạ! Bạn có muốn kết hợp thêm một miếng **Gà Rán Giòn Vui Vẻ** không ạ?";
        }

        if (str_contains($promptLower, 'khuyến mãi') || str_contains($promptLower, 'rẻ') || str_contains($promptLower, 'combo')) {
            return "Chào bạn, FOODELICIOUS đang có các **Combo Tiết Kiệm** như Combo Gà Rán & Mì Ý giảm giá 15% kèm nước ngọt. Bạn cũng có thể áp dụng mã giảm giá trong phần thanh toán nhé!";
        }

        return "Xin chào! Tôi là trợ lý ảo FOODELICIOUS. Tôi có thể giúp bạn gợi ý các món ăn ngon như **Gà Rán**, **Mì Ý Sốt Bò Bằm**, **Burger Tôm/Bò** và các phần **Combo**. Bạn muốn ăn gì hôm nay ạ?";
    }
}
