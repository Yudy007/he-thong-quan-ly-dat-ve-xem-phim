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
        'username' => $_POST['username'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'hoTen' => $_POST['hoTen'],
        'VaiTro' => 'khachhang'
    ];
    
    $result = registerUser($data);
    
    if ($result) {
        $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
        header('Location: login.php');
        exit;
    } else {
        $error = "Tên đăng nhập đã tồn tại hoặc có lỗi xảy ra";
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
        <h2>Đăng ký thành viên</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Tên đăng nhập:</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Mật khẩu:</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Họ và tên:</label>
                <input type="text" name="hoTen" required>
            </div>
            
            <button type="submit" class="btn">Đăng ký</button>
        </form>
        
        <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>