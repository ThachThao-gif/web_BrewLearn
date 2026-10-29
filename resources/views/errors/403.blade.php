<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kiểm tra tài khoản • BrewLearn</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{box-sizing:border-box}body{margin:0;background:#faf7f2;color:#2c190e;font-family:'Montserrat','Segoe UI',Arial,sans-serif;min-height:100vh;display:grid;place-items:center;padding:24px}.card{width:100%;max-width:560px;background:white;border:1px solid #e5d8c5;border-radius:24px;padding:32px;box-shadow:0 8px 24px #4a2e1b10}.brand{font-weight:800;font-size:20px;color:#6e4e30}h1{font-size:24px;margin:24px 0 16px}p{font-size:15px;line-height:1.7;color:#64748b}.account{padding:16px;border-radius:12px;background:#faf7f2}.actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:24px}button,a{display:inline-block;border-radius:12px;padding:13px 18px;font:inherit;font-weight:700;text-decoration:none;cursor:pointer}button{background:#4a2e1b;border:1px solid #4a2e1b;color:white}a{border:1px solid #e5d8c5;color:#6e4e30}
    </style>
</head>
<body>
<main class="card">
    <div class="brand">BrewLearn</div>
    @if(request()->is('manager/*') && auth()->check())
        <h1>Trang này cần tài khoản quản lý cửa hàng</h1>
        <p class="account">Bạn đang đăng nhập: <strong>{{ auth()->user()->name }}</strong><br>Vị trí: {{ auth()->user()->ChucVu ?? 'Chưa xác định' }}</p>
        <p>Để mở phân hệ quản lý, chọn <strong>Đổi tài khoản</strong> và đăng nhập tài khoản Ngọc Bích: <strong>ngocbich@brewlearn.com</strong>.</p>
        <p>Các tab trong cùng một trình duyệt dùng chung phiên đăng nhập. Khi đổi tài khoản ở một tab, những tab khác cũng sử dụng tài khoản mới.</p>
    @else
        <h1>Không thể truy cập nội dung này</h1>
        <p>Tài khoản hiện tại không có quyền mở nội dung bạn yêu cầu.</p>
    @endif
    <div class="actions">
        @auth<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Đổi tài khoản</button></form>@endauth
        <a href="{{ route('dashboard') }}">Về giao diện của tôi</a>
    </div>
</main>
</body>
</html>
