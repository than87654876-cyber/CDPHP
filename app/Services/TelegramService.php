<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    /**
     * Send message to Telegram Chat/Group
     */
    public function sendMessage($message)
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            Log::warning('Telegram BOT Token or Chat ID is not configured. Message skipped: ' . $message);
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            $parts = parse_url($url);
            
            $postData = json_encode([
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            // Asynchronous, non-blocking connection using fsockopen
            $fp = @fsockopen(
                'ssl://' . $parts['host'], 
                $parts['port'] ?? 443, 
                $errno, 
                $errstr, 
                1.5 // Connection timeout
            );

            if ($fp) {
                // Set stream to non-blocking
                stream_set_blocking($fp, false);

                $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
                $out .= "Host: " . $parts['host'] . "\r\n";
                $out .= "Content-Type: application/json\r\n";
                $out .= "Content-Length: " . strlen($postData) . "\r\n";
                $out .= "Connection: Close\r\n\r\n";
                $out .= $postData;

                fwrite($fp, $out);
                fclose($fp);
                return true;
            }
            
            Log::warning("Telegram connection failed, falling back to sync: $errstr ($errno)");
            
            // Fallback to synchronous Http Client if socket fails
            $response = Http::timeout(2)->post($url, [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::error('Telegram notification send failed: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('Telegram notification exception: ' . $e->getMessage());
            return false;
        }
    }
}
