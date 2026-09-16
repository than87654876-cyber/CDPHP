<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực đăng nhập - FOODDAILY</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: #f8fafc;
            color: #0f172a;
            padding: 1.5rem;
            text-align: center;
        }
        .auth-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }
        .icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            background: #ecfdf5;
            color: #059669;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 1.25rem;
        }
        .title {
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0 0 0.5rem;
            color: #0f172a;
        }
        .desc {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0 0 1.5rem;
            line-height: 1.5;
        }
        .spinner {
            display: inline-block;
            width: 24px;
            height: 24px;
            border: 3px solid #e2e8f0;
            border-top-color: #ee4d2d;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        @if(isset($error))
            <div class="icon-wrapper" style="background: #fef2f2; color: #dc2626;">✕</div>
            <h2 class="title">Đăng nhập chưa thành công</h2>
            <p class="desc">{{ $error }}</p>
        @else
            <div class="icon-wrapper">✔</div>
            <h2 class="title">Đăng nhập thành công!</h2>
            <p class="desc">Chào mừng bạn quay lại FOODDAILY. Cửa sổ này đang tự động đồng bộ và đóng lại...</p>
            <div class="spinner"></div>
        @endif
    </div>

    <script>
        const targetUrl = @json($targetUrl ?? route('trangchu'));
        const errorMessage = @json($error ?? null);

        if (window.opener && !window.opener.closed) {
            try {
                if (errorMessage) {
                    window.opener.postMessage({ type: 'GOOGLE_AUTH_ERROR', message: errorMessage }, '*');
                } else {
                    window.opener.postMessage({ type: 'GOOGLE_AUTH_SUCCESS', redirectUrl: targetUrl }, '*');
                    window.opener.location.href = targetUrl;
                }
            } catch (e) {
                console.error(e);
            }
            setTimeout(function() {
                window.close();
            }, 600);
        } else {
            setTimeout(function() {
                window.location.href = errorMessage ? "{{ route('dangnhap') }}" : targetUrl;
            }, 600);
        }
    </script>
</body>
</html>
