<?php
session_start();
require_once 'includes/functions.php';

// Nếu đã đăng nhập thì chuyển hướng
if (isset($_SESSION['MaND'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'TenDangNhap' => $_POST['username'],
        'MatKhau' => $_POST['password'], // Sẽ được hash trong hàm registerUser
        'HoTen' => $_POST['hoTen']
    ];

    $result = registerUser($data);

    if ($result) {
        $_SESSION['success'] = "🎉 Đăng ký thành công! Vui lòng đăng nhập để bắt đầu đặt vé.";
        header('Location: login.php');
        exit;
    } else {
        $error = "❌ Tên đăng nhập đã tồn tại hoặc có lỗi xảy ra. Vui lòng thử lại.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .register-container { max-width: 500px; margin: 50px auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .register-header { text-align: center; margin-bottom: 30px; }
        .register-header h2 { color: #333; margin-bottom: 10px; }
        .register-header p { color: #666; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-group input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-group input:focus { border-color: #007bff; outline: none; }
        .btn { width: 100%; padding: 15px; background: linear-gradient(135deg, #007bff, #0056b3); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; transition: all 0.3s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,123,255,0.3); }
        .alert { padding: 15px; margin: 20px 0; border-radius: 8px; }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .login-link { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="register-container">
        <div class="register-header">
            <h2>🎬 Đăng ký thành viên</h2>
            <p>Tạo tài khoản để đặt vé xem phim dễ dàng</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>📧 Tên đăng nhập:</label>
                <input type="text" name="username" required placeholder="Nhập tên đăng nhập">
            </div>

            <div class="form-group">
                <label>🔒 Mật khẩu:</label>
                <input type="password" name="password" required placeholder="Nhập mật khẩu mạnh">
            </div>

            <div class="form-group">
                <label>👤 Họ và tên:</label>
                <input type="text" name="hoTen" required placeholder="Nhập họ và tên đầy đủ">
            </div>

            <button type="submit" class="btn">🎫 Đăng ký ngay</button>
        </form>

        <div class="login-link">
            <p>Đã có tài khoản? <a href="login.php" style="color: #007bff; text-decoration: none; font-weight: bold;">Đăng nhập ngay</a></p>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>