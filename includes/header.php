<?php
if (!isset($_SESSION)) session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống đặt vé xem phim</title>
    <link rel="stylesheet" href="/movie_booking/assets/css/style.css">
</head>
<body>
    <header style="background: #003366; color: white; padding: 10px;">
        <div class="container">
            <h1 style="margin: 0;">🎬 Movie Booking System</h1>
            <?php if (isset($_SESSION['MaND'])): ?>
                <div style="font-size: 14px;">
                    Xin chào, <strong><?= htmlspecialchars($_SESSION['hoTen']) ?></strong> 
                    (<?= htmlspecialchars($_SESSION['VaiTro']) ?>) |
                    <a href="/movie_booking/logout.php" style="color: white;">Đăng xuất</a>
                </div>
            <?php else: ?>
                <div style="font-size: 14px;">
                    <a href="/movie_booking/login.php" style="color: white;">Đăng nhập</a> |
                    <a href="/movie_booking/register.php" style="color: white;">Đăng ký</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <nav style="background: #f2f2f2; padding: 10px;">
        <div class="container">
            <a href="/movie_booking/index.php">Trang chủ</a>
            <?php if (isset($_SESSION['VaiTro']) && $_SESSION['VaiTro'] === 'admin'): ?>
                | <a href="/movie_booking/admin/dashboard.php">Admin Dashboard</a>
            <?php elseif (isset($_SESSION['VaiTro']) && $_SESSION['VaiTro'] === 'nhanvien'): ?>
                | <a href="/movie_booking/staff/dashboard.php">Staff Dashboard</a>
            <?php elseif (isset($_SESSION['VaiTro']) && $_SESSION['VaiTro'] === 'khachhang'): ?>
                | <a href="/movie_booking/customer/my_tickets.php">Vé của tôi</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container" style="padding: 20px;">
