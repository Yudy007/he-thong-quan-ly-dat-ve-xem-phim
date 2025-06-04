<?php
require_once '../includes/auth.php';
checkRole('admin');

// Xử lý bộ lọc
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');
$movieId = $_GET['movie_id'] ?? null;

// Lấy dữ liệu báo cáo
$revenueReport = getRevenueReport($startDate, $endDate, $movieId);
$topMovies = getTopMovies($startDate, $endDate);
$ticketStats = getTicketStatistics($startDate, $endDate);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Thống kê</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Báo cáo & Thống kê</h1>
        
        <div class="report-filters">
            <form method="GET" class="filter-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Từ ngày</label>
                        <input type="date" name="start_date" value="<?= $startDate ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Đến ngày</label>
                        <input type="date" name="end_date" value="<?= $endDate ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Phim</label>
                        <select name="movie_id">
                            <option value="">Tất cả phim</option>
                            <?php foreach (getActiveMovies() as $movie): ?>
                                <option value="<?= $movie['MaPhim'] ?>" 
                                    <?= $movieId == $movie['MaPhim'] ? 'selected' : '' ?>>
                                    <?= $movie['TenPhim'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Áp dụng</button>
                </div>
            </form>
        </div>
        
        <div class="stats-cards">
            <div class="stat-card">
                <h3>Tổng doanh thu</h3>
                <p class="stat-value"><?= number_format($ticketStats['total_revenue'], 0, ',', '.') ?> VNĐ</p>
            </div>
            
            <div class="stat-card">
                <h3>Tổng vé bán</h3>
                <p class="stat-value"><?= $ticketStats['total_tickets'] ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Vé trung bình/suất</h3>
                <p class="stat-value"><?= $ticketStats['avg_per_show'] ?></p>
            </div>
        </div>
        
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
        
        <div class="report-section">
            <h2>Doanh thu theo ngày</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Ngày</th>
                        <th>Doanh thu</th>
                        <th>Số vé</th>
                        <th>Phim bán chạy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($revenueReport as $row): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($row['Ngay'])) ?></td>
                        <td><?= number_format($row['DoanhThu'], 0, ',', '.') ?> VNĐ</td>
                        <td><?= $row['SoVe'] ?></td>
                        <td><?= $row['PhimBanChay'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="report-section">
            <h2>Top 5 phim bán chạy</h2>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Phim</th>
                        <th>Doanh thu</th>
                        <th>Số vé</th>
                        <th>Tỷ lệ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topMovies as $movie): ?>
                    <tr>
                        <td><?= $movie['TenPhim'] ?></td>
                        <td><?= number_format($movie['DoanhThu'], 0, ',', '.') ?> VNĐ</td>
                        <td><?= $movie['SoVe'] ?></td>
                        <td><?= number_format($movie['TyLe'], 1) ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    // Biểu đồ doanh thu
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [<?= implode(',', array_map(function($item) { 
                return "'".date('d/m', strtotime($item['Ngay']))."'"; 
            }, $revenueReport)) ?>],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: [<?= implode(',', array_column($revenueReport, 'DoanhThu')) ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' VNĐ';
                        }
                    }
                }
            }
        }
    });
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>