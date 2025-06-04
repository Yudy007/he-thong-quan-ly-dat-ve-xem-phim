<?php
require_once '../includes/auth.php';
checkRole('admin');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng điều khiển Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Bảng điều khiển Quản trị</h1>
        
        <div class="stats-grid">
            <?php
            $stats = getAdminStats();
            ?>
            <div class="stat-card">
                <h3>Tổng số phim</h3>
                <p class="stat-value"><?= $stats['total_movies'] ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Tổng số vé đã bán</h3>
                <p class="stat-value"><?= $stats['total_tickets'] ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Doanh thu tháng</h3>
                <p class="stat-value"><?= number_format($stats['monthly_revenue'], 0, ',', '.') ?> VNĐ</p>
            </div>
            
            <div class="stat-card">
                <h3>Số người dùng</h3>
                <p class="stat-value"><?= $stats['total_users'] ?></p>
            </div>
        </div>
        
        <div class="quick-links">
            <a href="manage_movies.php" class="btn">Quản lý Phim</a>
            <a href="manage_schedules.php" class="btn">Quản lý Suất chiếu</a>
            <a href="manage_users.php" class="btn">Quản lý Người dùng</a>
            <a href="reports.php" class="btn">Xem Báo cáo</a>
        </div>
        
        <div class="recent-activity">
            <h2>Hoạt động gần đây</h2>
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Người dùng</th>
                        <th>Hành động</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (getRecentActivities() as $activity): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($activity['ThoiGian'])) ?></td>
                        <td><?= $activity['HoTen'] ?></td>
                        <td><?= $activity['HoatDong'] ?></td>
                        <td><?= $activity['ChiTiet'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>