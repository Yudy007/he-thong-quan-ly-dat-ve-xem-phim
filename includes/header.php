<?php
if (!isset($_SESSION)) session_start();
$role = $_SESSION['VaiTro'] ?? null;
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
            <?php if ($role === 'admin'): ?>
                | <a href="/movie_booking/admin/dashboard.php"> Dashboard</a>
                | <a href="/movie_booking/admin/manage_movies.php"> Quản lý phim</a>
                | <a href="/movie_booking/admin/manage_schedules.php"> Quản lý suất chiếu</a>
                | <a href="/movie_booking/admin/manage_rooms.php"> Quản lý phòng & ghế</a>
                | <a href="/movie_booking/admin/manage_users.php"> Quản lý người dùng</a>
                | <a href="/movie_booking/admin/reports.php"> Thống kê</a>
            <?php elseif ($role === 'nhanvien'): ?>
                | <a href="/movie_booking/staff/dashboard.php"> Dashboard</a>
                | <a href="/movie_booking/staff/ticket_checker.php"> Kiểm tra vé</a>
            <?php elseif ($role === 'khachhang'): ?>
                | <a href="/movie_booking/customer/home.php"> Trang chủ</a>
                | <a href="/movie_booking/customer/booking.php"> Đặt vé</a>
                | <a href="/movie_booking/customer/my_tickets.php">Vé của tôi</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container" style="padding: 20px;">