<?php
// File: /admin/dashboard.php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('admin');

include_once '../includes/header.php';

// Lấy thống kê từ database
$stats = getStats();
?>

<div class="dashboard">
    <h2>Dashboard Quản trị</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Tổng số phim</h3>
            <p><?= $stats['total_movies'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Tổng số vé đã bán</h3>
            <p><?= $stats['total_tickets'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Doanh thu hôm nay</h3>
            <p><?= number_format($stats['today_revenue']) ?> VNĐ</p>
        </div>
        <div class="stat-card">
            <h3>Người dùng hoạt động</h3>
            <p><?= $stats['active_users'] ?></p>
        </div>
    </div>

    <!-- Biểu đồ doanh thu 7 ngày -->
    <div class="chart-container">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($stats['revenue_labels']) ?>,
            datasets: [{
                label: 'Doanh thu 7 ngày',
                data: <?= json_encode($stats['revenue_data']) ?>,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)'
            }]
        }
    });
</script>

<?php include_once '../includes/footer.php'; ?>