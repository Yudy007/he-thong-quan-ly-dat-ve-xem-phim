<?php
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$stats = getStats();
$summary = getAdminStats();
$activities = getRecentActivities();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang tổng quan - Quản trị</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<main class="dashboard-container">
    <h2 class="dashboard-title">📊 BÁO CÁO & THỐNG KÊ HỆ THỐNG</h2>

    <!-- Tổng quan -->
    <section class="dashboard-summary">
        <div class="summary-box bg-blue">
            <h4>🎬 Tổng phim</h4>
            <p><?= $summary['total_movies'] ?></p>
        </div>
        <div class="summary-box bg-green">
            <h4>🎟️ Vé đã bán</h4>
            <p><?= $summary['total_tickets'] ?></p>
        </div>
        <div class="summary-box bg-yellow">
            <h4>💰 Doanh thu tháng</h4>
            <p><?= number_format($summary['monthly_revenue']) ?> VNĐ</p>
        </div>
        <div class="summary-box bg-dark">
            <h4>👤 Người dùng</h4>
            <p><?= $summary['total_users'] ?></p>
        </div>
    </section>

    <!-- Thống kê theo phim -->
    <section class="dashboard-section">
        <h3>📈 Thống kê theo phim</h3>
        <?php if (!empty($stats)): ?>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Tên phim</th>
                        <th>Số vé đã bán</th>
                        <th>Doanh thu (VNĐ)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['TENPHIM']) ?></td>
                            <td><?= $row['SOVE'] ?></td>
                            <td><?= number_format($row['DOANHTHU']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">Chưa có dữ liệu thống kê.</p>
        <?php endif; ?>
    </section>

    <!-- Lịch sử hoạt động -->
    <section class="dashboard-section">
        <h3>🕓 Hoạt động gần đây</h3>
        <?php if (!empty($activities)): ?>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Người dùng</th>
                        <th>Hoạt động</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $act): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($act['THOIGIAN'])) ?></td>
                            <td><?= htmlspecialchars($act['HOTEN']) ?></td>
                            <td><?= $act['HOATDONG'] ?></td>
                            <td><?= htmlspecialchars($act['CHITIET']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">Không có hoạt động nào gần đây.</p>
        <?php endif; ?>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>
