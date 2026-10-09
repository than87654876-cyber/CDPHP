<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SePayService
{
    protected ?string $apiKey;
    protected string $accountNumber;
    protected string $bank;
    protected string $accountName;

    public function __construct()
    {
        $this->apiKey = config('services.sepay.api_key', env('SEPAY_API_KEY'));
        $this->accountNumber = (string) config('services.sepay.account_number', env('SEPAY_ACCOUNT_NUMBER', '8899408675'));
        $this->bank = (string) config('services.sepay.bank', env('SEPAY_BANK', 'BIDV'));
        $this->accountName = (string) config('services.sepay.account_name', env('SEPAY_ACCOUNT_NAME', 'Tran Le Than'));
    }

    /**
     * Xác thực Webhook Request từ SePay.
     * SePay gửi header: Authorization: Apikey <API_KEY>
     */
    public function validateAuthorization(Request $request): bool
    {
        if (empty($this->apiKey)) {
            // Nếu chưa cấu hình API_KEY (môi trường dev/test), cho phép để dễ kiểm thử
            Log::info('SePay: SEPAY_API_KEY chưa cấu hình trong .env, bỏ qua bước kiểm tra Authorization.');
            return true;
        }

        $authHeader = $request->header('Authorization', '');
        
        // Hỗ trợ "Apikey <KEY>", "Bearer <KEY>", hoặc so sánh trực tiếp
        $providedKey = '';
        if (preg_match('/(?:Apikey|Bearer)\s+(.+)$/i', $authHeader, $matches)) {
            $providedKey = trim($matches[1]);
        } else {
            $providedKey = trim($authHeader);
        }

        // Hỗ trợ thêm token truyền qua query/body nếu cần
        if (empty($providedKey)) {
            $providedKey = (string) ($request->query('api_key') ?? $request->input('api_key', ''));
        }

        $isValid = !empty($providedKey) && hash_equals($this->apiKey, $providedKey);
        if (!$isValid) {
            Log::warning('SePay Webhook Authorization key mismatch', [
                'expected_key' => $this->apiKey,
                'received_key' => $providedKey,
                'received_header' => $authHeader,
                'ip' => $request->ip(),
            ]);

            // Môi trường Localhost/Dev: Cho phép thông qua để không bị chặn khi test
            if (app()->isLocal() || config('app.debug')) {
                Log::info('SePay: Đang ở môi trường Local/Debug, tự động chấp nhận Webhook request.');
                return true;
            }
        }

        return $isValid;
    }

    /**
     * Chuẩn hóa dữ liệu Webhook từ SePay
     */
    public function parsePayload(array $payload): array
    {
        $content = (string) ($payload['content'] ?? $payload['description'] ?? '');
        $orderId = null;

        // Trích xuất mã đơn hàng dạng FDL-123, FDL123, FDL_123 hoặc FDL 123
        if (preg_match('/FDL[\s\-_]?(\d+)/i', $content, $matches)) {
            $orderId = (int) $matches[1];
        }

        return [
            'id' => $payload['id'] ?? null,
            'gateway' => $payload['gateway'] ?? $payload['bank_brand_name'] ?? $this->bank,
            'transaction_date' => $payload['transactionDate'] ?? $payload['transaction_date'] ?? now()->toDateTimeString(),
            'account_number' => $payload['accountNumber'] ?? $payload['account_number'] ?? $this->accountNumber,
            'sub_account' => $payload['subAccount'] ?? $payload['sub_account'] ?? null,
            'transfer_type' => strtolower($payload['transferType'] ?? $payload['transfer_type'] ?? 'in'),
            'transfer_amount' => (float) ($payload['transferAmount'] ?? $payload['transfer_amount'] ?? 0),
            'accumulated' => (float) ($payload['accumulated'] ?? 0),
            'reference_code' => $payload['referenceCode'] ?? $payload['reference_number'] ?? $payload['id'] ?? null,
            'content' => $content,
            'order_id' => $orderId,
            'raw' => $payload,
        ];
    }

    /**
     * Tạo URL ảnh VietQR hiển thị cho khách quét mã thanh toán SePay
     */
    public function generateVietQrUrl(int|string $orderId, float $amount, string $template = 'compact2'): string
    {
        $amountInt = (int) $amount;
        $content = "FDL-{$orderId}";
        $accountNameEncoded = urlencode($this->accountName);

        return "https://img.vietqr.io/image/{$this->bank}-{$this->accountNumber}-{$template}.jpg?amount={$amountInt}&addInfo={$content}&accountName={$accountNameEncoded}";
    }

    public function getBank(): string
    {
        return $this->bank;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }
}
