<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - BrewLearn LMS</title>
    <!-- Nhúng Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f6f0;
            font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            background: #ffffff;
        }
        .btn-brand {
            background-color: #c86218;
            color: white;
            border: none;
            font-weight: 600;
        }
        .btn-brand:hover {
            background-color: #a84f12;
            color: white;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 p-md-5 login-card" style="width: 100%; max-width: 420px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark mb-1">BrewLearn</h3>
            <p class="text-muted small">Hệ thống đào tạo & Đánh giá nhân sự</p>
        </div>

        <!-- HIỂN THỊ THÔNG BÁO LỖI SESSION -->
        <?php if(session('error')): ?>
            <div class="alert alert-danger py-2 small mb-3">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <!-- HIỂN THỊ LỖI VALIDATION (SAI EMAIL/PASSWORD) -->
        <?php if($errors->any()): ?>
            <div class="alert alert-danger py-2 small mb-3">
                <ul class="mb-0 ps-3">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- FORM ĐĂNG NHẬP (Đã chỉnh route('login')) -->
        <form action="<?php echo e(route('login')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="mb-3">
                <label class="form-label text-secondary small fw-semibold">Email đăng nhập</label>
                <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="nhanvien@brewlearn.com" required value="<?php echo e(old('email')); ?>">
            </div>
            
            <div class="mb-4">
                <label class="form-label text-secondary small fw-semibold">Mật khẩu</label>
                <input type="password" name="password" class="form-control form-control-lg fs-6" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2 fs-6 rounded-3">Đăng Nhập</button>
        </form>
    </div>
</div>

</body>
</html><?php /**PATH D:\wamp64\Brewlearn\resources\views/auth/login.blade.php ENDPATH**/ ?>