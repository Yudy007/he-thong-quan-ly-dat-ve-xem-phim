<?php
require_once '../includes/auth.php'; // Thêm dòng này để kiểm tra đăng nhập
require_once '../includes/functions.php';
require_once '../includes/db_connect.php'; // Thêm kết nối DB

checkRole('admin'); // Chỉ admin mới được truy cập

if (session_status() === PHP_SESSION_NONE) session_start();

$stats = getStats();
$summary = getAdminStats();
$activities = getRecentActivities(15); // Giới hạn 15 hoạt động
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang tổng quan - Quản trị</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Thêm CSS tùy chỉnh */
        .summary-box { transition: transform 0.3s; }
        .summary-box:hover { transform: scale(1.05); }
        .dashboard-table td:last-child { white-space: nowrap; }
        .text-muted { color: #6c757d; }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<main class="dashboard-container">
    <h2 class="dashboard-title">📊 BÁO CÁO & THỐNG KÊ HỆ THỐNG</h2>

    <!-- Tổng quan -->
    <section class="dashboard-summary">
        <div class="summary-box bg-blue" title="Tổng số phim trong hệ thống">
            <h4>🎬 Tổng phim</h4>
            <p><?= htmlspecialchars($summary['total_movies'] ?? 0) ?></p>
        </div>
        <div class="summary-box bg-green" title="Tổng số vé đã được đặt">
            <h4>🎟️ Vé đã bán</h4>
            <p><?= htmlspecialchars($summary['total_tickets'] ?? 0) ?></p>
        </div>
        <div class="summary-box bg-yellow" title="Doanh thu tháng hiện tại">
            <h4>💰 Doanh thu tháng</h4>
            <p><?= isset($summary['monthly_revenue']) ? number_format($summary['monthly_revenue']) : '0' ?> VNĐ</p>
        </div>
        <div class="summary-box bg-dark" title="Tổng số tài khoản người dùng">
            <h4>👤 Người dùng</h4>
            <p><?= htmlspecialchars($summary['total_users'] ?? 0) ?></p>
        </div>
    </section>

    <!-- Thống kê theo phim -->
    <section class="dashboard-section">
        <h3>📈 Top phim bán chạy</h3>
        <?php if (!empty($stats)): ?>
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Phim</th>
                            <th>Vé đã bán</th>
                            <th>Doanh thu</th>
                            <th>Tỷ lệ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalRevenue = array_sum(array_column($stats, 'DOANHTHU'));
                        foreach ($stats as $row): 
                            $percentage = $totalRevenue > 0 ? ($row['DOANHTHU'] / $totalRevenue * 100) : 0;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['TENPHIM']) ?></td>
                                <td><?= htmlspecialchars($row['SOVE']) ?></td>
                                <td><?= number_format($row['DOANHTHU']) ?>₫</td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress" style="width: <?= round($percentage) ?>%"></div>
                                        <span><?= round($percentage) ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert info">📭 Chưa có dữ liệu thống kê</div>
        <?php endif; ?>
    </section>

    <!-- Lịch sử hoạt động -->
    <section class="dashboard-section">
        <h3>🕓 Hoạt động gần đây</h3>
        <?php if (!empty($activities)): ?>
            <div class="activity-feed">
                <?php foreach ($activities as $act): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <?= match($act['HOATDONG']) {
                                'Đặt vé' => '🎫',
                                'Hủy vé' => '❌',
                                'Đăng nhập' => '🔐',
                                default => '⚡'
                            } ?>
                        </div>
                        <div class="activity-details">
                            <span class="activity-user"><?= htmlspecialchars($act['HOTEN']) ?></span>
                            <span class="activity-action"><?= $act['HOATDONG'] ?></span>
                            <span class="activity-desc"><?= htmlspecialchars($act['CHITIET']) ?></span>
                            <small class="activity-time"><?= date('H:i d/m/Y', strtotime($act['THOIGIAN'])) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert info">📭 Không có hoạt động nào trong 24h qua</div>
        <?php endif; ?>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

<script>
// Có thể thêm JS để làm mới dữ liệu định kỳ
setInterval(() => {
    fetch(window.location.href)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            // Cập nhật các phần tử cần thiết
        });
}, 300000); // 5 phút làm mới 1 lần
</script>
</body>
</html>