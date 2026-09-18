<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CloudinaryService
{
    protected $cloudName;
    protected $uploadPreset;
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name');
        $this->uploadPreset = config('services.cloudinary.upload_preset');
        $this->apiKey = config('services.cloudinary.api_key');
        $this->apiSecret = config('services.cloudinary.api_secret');
    }

    /**
     * Upload Image
     * Returns the secure URL of the image, or local URL fallback.
     */
    public function upload($file)
    {
        if (empty($this->cloudName) || (empty($this->uploadPreset) && empty($this->apiSecret))) {
            Log::warning('Cloudinary is not fully configured. Falling back to local storage.');
            // Fallback to storing in public storage
            $path = $file->store('uploads', 'public');
            return 'storage/' . $path;
        }

        try {
            $timestamp = time();
            $url = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload";

            $multipart = [];
            // Add file
            $multipart[] = [
                'name' => 'file',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName()
            ];

            if ($this->apiSecret && $this->apiKey) {
                // Signed upload
                $params = [
                    'timestamp' => $timestamp
                ];
                ksort($params);
                $signStr = "timestamp={$timestamp}{$this->apiSecret}";
                $signature = sha1($signStr);

                $multipart[] = ['name' => 'timestamp', 'contents' => $timestamp];
                $multipart[] = ['name' => 'api_key', 'contents' => $this->apiKey];
                $multipart[] = ['name' => 'signature', 'contents' => $signature];
            } else {
                // Unsigned upload
                $multipart[] = ['name' => 'upload_preset', 'contents' => $this->uploadPreset];
            }

            $response = Http::asMultipart()->post($url, $multipart);

            if ($response->successful()) {
                $data = $response->json();
                return $data['secure_url'] ?? null;
            }

            Log::error('Cloudinary upload failed: ' . $response->body());
            $path = $file->store('uploads', 'public');
            return 'storage/' . $path;
        } catch (\Exception $e) {
            Log::error('Cloudinary upload exception: ' . $e->getMessage());
            $path = $file->store('uploads', 'public');
            return 'storage/' . $path;
        }
    }
}
