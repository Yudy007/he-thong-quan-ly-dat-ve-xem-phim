<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_url = '/he-thong-quan-ly-dat-ve-xem-phim';

$vaiTro = $_SESSION['VaiTro'] ?? null;
$hoTen = $_SESSION['hoTen'] ?? null;
?>
<header>
    <nav class="main-nav">
        <div class="logo">
            <a href="<?= $base_url ?>/index.php">MovieBooking</a>
        </div>

        <div class="nav-links-wrapper" style="display: flex; gap: 1.5rem; align-items: center;">
            <ul class="nav-links">
                <li><a href="<?= $base_url ?>/index.php">Trang chủ</a></li>

                <?php if ($vaiTro === 'khachhang'): ?>
                    <li><a href="<?= $base_url ?>/my_tickets.php">Vé đã đặt</a></li>
                <?php elseif ($vaiTro === 'nhanvien'): ?>
                    <li><a href="<?= $base_url ?>/staff/check_ticket.php">Kiểm tra vé</a></li>
                <?php elseif ($vaiTro === 'admin'): ?>
                    <li><a href="<?= $base_url ?>/admin/manage_users.php">Quản lý người dùng</a></li>
                    <li><a href="<?= $base_url ?>/admin/manage_movies.php">Quản lý phim</a></li>
                    <li><a href="<?= $base_url ?>/admin/manage_schedules.php">Quản lý suất</a></li>
                    <li><a href="<?= $base_url ?>/admin/dashboard.php">Báo cáo</a></li>
                <?php endif; ?>
            </ul>

            <ul class="auth-links">
                <?php if ($vaiTro): ?>
                    <li><span><?= htmlspecialchars($hoTen) ?> (<?= htmlspecialchars($vaiTro) ?>)</span></li>
                    <li><a href="<?= $base_url ?>/logout.php" class="btn small">Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="<?= $base_url ?>/login.php">Đăng nhập</a></li>
                    <li><a href="<?= $base_url ?>/register.php">Đăng ký</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
