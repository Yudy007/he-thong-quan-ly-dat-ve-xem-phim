<?php
// File: /admin/reports.php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('admin');

$report_type = $_GET['type'] ?? 'daily';

// Lấy dữ liệu báo cáo
$report_data = getReport($report_type);

include_once '../includes/header.php';
?>

<div class="container">
    <h2>Báo cáo & Thống kê</h2>
    
    <div class="report-filters">
        <select id="reportType">
            <option value="daily" <?= $report_type === 'daily' ? 'selected' : '' ?>>Doanh thu ngày</option>
            <option value="monthly" <?= $report_type === 'monthly' ? 'selected' : '' ?>>Doanh thu tháng</option>
            <option value="movies" <?= $report_type === 'movies' ? 'selected' : '' ?>>Vé bán theo phim</option>
        </select>
    </div>
    
    <div class="report-results">
        <?php if ($report_type === 'movies'): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tên phim</th>
                    <th>Số vé bán</th>
                    <th>Doanh thu</th>
                    <th>Tỷ lệ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report_data as $row): ?>
                <tr>
                    <td><?= $row['TEN_PHIM'] ?></td>
                    <td><?= $row['SO_VE'] ?></td>
                    <td><?= number_format($row['DOANH_THU']) ?> VNĐ</td>
                    <td><?= $row['TY_LE'] ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <canvas id="reportChart" height="400"></canvas>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('reportType').addEventListener('change', function() {
    window.location = 'reports.php?type=' + this.value;
});

<?php if ($report_type !== 'movies'): ?>
const ctx = document.getElementById('reportChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($report_data, 'label')) ?>,
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?= json_encode(array_column($report_data, 'value')) ?>,
            backgroundColor: '#4e73df'
        }]
    }
});
<?php endif; ?>
</script>

<?php include_once '../includes/footer.php'; ?>