<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GzipResponse
{
    /**
     * Nén phản hồi HTTP bằng GZIP để giảm dung lượng HTML/JSON truyền qua mạng,
     * giúp tăng tốc FCP/LCP và cải thiện tối đa điểm số Performance trong Lighthouse.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Bỏ qua nếu là tải file hoặc StreamedResponse
        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return $response;
        }

        // Chỉ nén nếu client hỗ trợ gzip và máy chủ có zlib
        $acceptEncoding = $request->header('Accept-Encoding', '');
        if (!str_contains($acceptEncoding, 'gzip') || !extension_loaded('zlib')) {
            return $response;
        }

        // Chỉ nén HTML, JSON, Text, SVG
        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/') && !str_contains($contentType, 'application/json') && !str_contains($contentType, 'application/javascript')) {
            return $response;
        }

        // Nếu response đã được nén trước đó thì bỏ qua
        if ($response->headers->has('Content-Encoding')) {
            return $response;
        }

        $content = $response->getContent();
        if (is_string($content) && strlen($content) > 1024) {
            $compressed = gzencode($content, 6);
            if ($compressed !== false) {
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Content-Length', (string) strlen($compressed));
                $response->headers->set('Vary', 'Accept-Encoding', false);
            }
        }

        return $response;
    }
}
