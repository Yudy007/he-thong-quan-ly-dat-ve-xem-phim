<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('admin');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng điều khiển Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .stat-value { font-size: 2.5em; font-weight: bold; margin: 10px 0; }
        .quick-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 30px 0; }
        .quick-links .btn { display: block; padding: 15px; text-align: center; background: #28a745; color: white; text-decoration: none; border-radius: 8px; transition: all 0.3s; }
        .quick-links .btn:hover { background: #218838; transform: translateY(-2px); }
        .recent-activity { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .activity-table { width: 100%; border-collapse: collapse; }
        .activity-table th, .activity-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .activity-table th { background: #007bff; color: white; }
        .welcome-section { background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%); color: white; padding: 30px; border-radius: 15px; margin: 20px 0; text-align: center; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="welcome-section">
            <h1>🎬 Chào mừng Admin <?= htmlspecialchars($_SESSION['hoTen']) ?>!</h1>
            <p>Quản lý toàn bộ hệ thống rạp chiếu phim một cách hiệu quả</p>
        </div>

        <div class="stats-grid">
            <?php
            $stats = getAdminStats();
            ?>
            <div class="stat-card">
                <h3>🎬 Tổng số phim</h3>
                <p class="stat-value"><?= $stats['total_movies'] ?></p>
            </div>

            <div class="stat-card">
                <h3>🎫 Vé đã bán</h3>
                <p class="stat-value"><?= $stats['total_tickets'] ?></p>
            </div>

            <div class="stat-card">
                <h3>💰 Doanh thu tháng</h3>
                <p class="stat-value"><?= number_format($stats['monthly_revenue'], 0, ',', '.') ?> VNĐ</p>
            </div>

            <div class="stat-card">
                <h3>👥 Người dùng</h3>
                <p class="stat-value"><?= $stats['total_users'] ?></p>
            </div>
        </div>

        <div class="quick-links">
            <a href="manage_movies.php" class="btn">🎬 Quản lý Phim</a>
            <a href="manage_schedules.php" class="btn">📅 Quản lý Suất chiếu</a>
            <a href="manage_rooms.php" class="btn">🏢 Quản lý Phòng & Ghế</a>
            <a href="manage_users.php" class="btn">👥 Quản lý Người dùng</a>
            <a href="manage_staff.php" class="btn">👨‍💼 Quản lý Nhân viên</a>
            <a href="reports.php" class="btn">📊 Thống kê & Báo cáo</a>
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