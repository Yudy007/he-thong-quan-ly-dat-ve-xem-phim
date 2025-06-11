<?php
// Khởi động session nếu chưa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/he-thong-quan-ly-dat-ve-xem-phim';

// Lấy thông tin người dùng với xử lý an toàn
$vaiTro = $_SESSION['VaiTro'] ?? null;
$hoTen = isset($_SESSION['hoTen']) ? htmlspecialchars($_SESSION['hoTen'], ENT_QUOTES, 'UTF-8') : null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="navbar-brand">
                <a href="<?= $base_url ?>/index.php" class="logo-link">
                    <span class="logo-text">MovieBooking</span>
                </a>
                <button class="navbar-toggler" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="<?= $base_url ?>/index.php" class="nav-link">Trang chủ</a>
                    </li>

                    <?php if ($vaiTro === 'khachhang'): ?>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/customer/home.php" class="nav-link">Trang cá nhân</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/customer/my_tickets.php" class="nav-link">Vé của tôi</a>
                        </li>
                    <?php elseif ($vaiTro === 'nhanvien'): ?>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/staff/dashboard.php" class="nav-link">Bảng điều khiển</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle">Quản lý vé</a>
                            <div class="dropdown-menu">
                                <a href="<?= $base_url ?>/staff/seat_adjust.php" class="dropdown-item">Điều chỉnh ghế</a>
                            </div>
                        </li>
                        
                    <?php elseif ($vaiTro === 'admin'): ?>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle">Quản lý</a>
                            <div class="dropdown-menu">
                                <a href="<?= $base_url ?>/admin/manage_users.php" class="dropdown-item">Người dùng</a>
                                <a href="<?= $base_url ?>/admin/manage_movies.php" class="dropdown-item">Phim</a>
                                <a href="<?= $base_url ?>/admin/manage_schedules.php" class="dropdown-item">Suất chiếu</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/admin/dashboard.php" class="nav-link">Báo cáo</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-auth">
                    <?php if ($vaiTro): ?>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle user-profile">
                                <span class="user-name"><?= $hoTen ?></span>
                                <span class="user-role">(<?= htmlspecialchars($vaiTro) ?>)</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="<?= $base_url ?>/logout.php" class="dropdown-item">Đăng xuất</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/login.php" class="nav-link">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>/register.php" class="btn btn-primary btn-sm">Đăng ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
</body>
</html>

