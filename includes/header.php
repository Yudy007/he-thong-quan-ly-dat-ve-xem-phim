<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$vaiTro = $_SESSION['VaiTro'] ?? null;
$hoTen = $_SESSION['hoTen'] ?? null;
?>
<header>
    <nav class="main-nav">
        <div class="logo">
            <a href="/index.php">🎬 MovieBooking</a>
        </div>

        <ul class="nav-links">
            <li><a href="/index.php">Trang chủ</a></li>

            <?php if ($vaiTro === 'khachhang'): ?>
                <li><a href="/my_tickets.php">Vé đã đặt</a></li>
            <?php elseif ($vaiTro === 'nhanvien'): ?>
                <li><a href="/staff/check_ticket.php">Kiểm tra vé</a></li>
            <?php elseif ($vaiTro === 'admin'): ?>
                <li><a href="/admin/manage_users.php">Quản lý người dùng</a></li>
                <li><a href="/admin/manage_movies.php">Quản lý phim</a></li>
                <li><a href="/admin/manage_schedules.php">Quản lý suất</a></li>
                <li><a href="/admin/reports.php">Báo cáo</a></li>
            <?php endif; ?>
        </ul>

        <ul class="auth-links">
            <?php if ($vaiTro): ?>
                <li><span>👤 <?= htmlspecialchars($hoTen) ?> (<?= $vaiTro ?>)</span></li>
                <li><a href="/logout.php" class="btn small">Đăng xuất</a></li>
            <?php else: ?>
                <li><a href="/login.php">Đăng nhập</a></li>
                <li><a href="/register.php">Đăng ký</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
