<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayOSService
{
    protected $clientId;
    protected $apiKey;
    protected $checksumKey;
    protected $apiUrl = 'https://api-merchant.payos.vn/v2/payment-requests';

    public function __construct()
    {
        $this->clientId = config('services.payos.client_id', 'demo-client-id');
        $this->apiKey = config('services.payos.api_key', 'demo-api-key');
        $this->checksumKey = config('services.payos.checksum_key', 'demo-checksum-key');
    }

    /**
     * Create PayOS Payment Link
     */
    public function createPaymentLink(array $data)
    {
        // Require fields: orderCode (int), amount (int), description (string), cancelUrl (string), returnUrl (string)
        $paymentData = [
            'orderCode' => (int) $data['orderCode'],
            'amount' => (int) $data['amount'],
            'description' => substr($data['description'], 0, 25), // PayOS limit description to 25 chars
            'cancelUrl' => $data['cancelUrl'],
            'returnUrl' => $data['returnUrl'],
        ];

        // Sort keys alphabetically
        ksort($paymentData);

        // Build data string
        $dataString = http_build_query($paymentData);
        // PayOS expects simple raw query: key1=val1&key2=val2... without URL encoding for signature
        // Let's decode URL encoding to ensure exact string
        $dataString = urldecode($dataString);

        // Sign data
        $signature = hash_hmac('sha256', $dataString, $this->checksumKey);
        $paymentData['signature'] = $signature;

        Log::info('PayOS create link request', ['data' => $paymentData, 'dataString' => $dataString]);

        // If credentials are demo/unset, return mock payment response for testing
        if ($this->clientId === 'demo-client-id' || $this->apiKey === 'demo-api-key') {
            Log::warning('PayOS is running in mock mode. Please set PAYOS credentials in .env.');
            
            if (app()->environment('testing')) {
                return [
                    'success' => true,
                    'data' => [
                        'checkoutUrl' => route('thanhtoan_momo', ['order_id' => $data['orderCode'], 'amount' => $data['amount']]),
                        'qrCode' => 'https://api.vietqr.io/image/970415-113366668888-jL4XyG2.jpg?amount=' . $data['amount'] . '&addInfo=' . urlencode($paymentData['description']),
                    ]
                ];
            }

            return [
                'success' => true,
                'data' => [
                    'checkoutUrl' => route('thanhtoan_hoantat', ['id' => $data['orderCode']]),
                    'qrCode' => 'https://api.vietqr.io/image/970415-113366668888-jL4XyG2.jpg?amount=' . $data['amount'] . '&addInfo=' . urlencode($paymentData['description']),
                ]
            ];
        }

        try {
            $response = Http::withHeaders([
                'x-client-id' => $this->clientId,
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, $paymentData);

            if ($response->successful()) {
                $resData = $response->json();
                if (isset($resData['code']) && $resData['code'] === '00') {
                    return [
                        'success' => true,
                        'data' => $resData['data']
                    ];
                }
                return [
                    'success' => false,
                    'message' => $resData['desc'] ?? 'PayOS returned error code.'
                ];
            }

            return [
                'success' => false,
                'message' => 'PayOS connection failed with status: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('PayOS connection exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate Webhook Signature
     */
    public function validateWebhook(array $webhookData)
    {
        if (!isset($webhookData['data']) || !isset($webhookData['signature'])) {
            return false;
        }

        $data = $webhookData['data'];
        ksort($data);
        $dataString = urldecode(http_build_query($data));
        $calculatedSignature = hash_hmac('sha256', $dataString, $this->checksumKey);

        return hash_equals($webhookData['signature'], $calculatedSignature);
    }
}
