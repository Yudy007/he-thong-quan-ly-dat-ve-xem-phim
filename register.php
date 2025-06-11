<?php
session_start();
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $hoTen = trim($_POST['hoTen']);

    if ($password !== $confirm) {
        $error = "Mật khẩu xác nhận không khớp.";
    } else {
        // Tạo dữ liệu
        $data = [
            'TENDANGNHAP' => $username,
            'MATKHAU' => $password,
            'HOTEN' => $hoTen,
            'VAITRO' => 'khachhang',
            'EMAIL' => null,
            'SDT' => null
        ];

        // Gọi hàm thêm user
        $success = registerUser($data);

        if ($success) {
            $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
            header('Location: login.php');
            exit;
        } else {
            $error = "Tên đăng nhập đã tồn tại hoặc có lỗi xảy ra.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>📝 Đăng ký tài khoản khách hàng</h2>

    <?php if (isset($error)): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Họ và tên *</label>
            <input type="text" name="hoTen" required>
        </div>

        <div class="form-group">
            <label>Tên đăng nhập *</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Mật khẩu *</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group">
            <label>Nhập lại mật khẩu *</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button type="submit" class="btn">Tạo tài khoản</button>
    </form>

    <p>Đã có tài khoản? <a href="login.php">Đăng nhập tại đây</a></p>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
