<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="0; url={{ route('dangnhap') }}">
    <script>
        window.location.href = "{{ route('dangnhap') }}";
    </script>
    <title>Redirecting...</title>
</head>
<body>
    <p>Đang chuyển hướng sang trang đăng nhập dùng chung... Nếu không tự chuyển hướng, <a href="{{ route('dangnhap') }}">bấm vào đây</a>.</p>
</body>
</html>
