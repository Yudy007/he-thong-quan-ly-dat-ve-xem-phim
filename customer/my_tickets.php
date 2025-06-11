<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('khachhang');

$userId = $_SESSION['MaND'];
$tickets = getMyTickets($userId);

// Hàm định dạng trạng thái
function formatStatus($status) {
    $statusMap = [
        'da_dat' => ['text' => 'Đã đặt', 'class' => 'status-booked'],
        'da_kiem_tra' => ['text' => 'Đã kiểm tra', 'class' => 'status-checked'],
        'da_huy' => ['text' => 'Đã huỷ', 'class' => 'status-cancelled']
    ];
    return $statusMap[$status] ?? ['text' => $status, 'class' => 'status-unknown'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé đã đặt - Hệ thống Rạp phim</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h2 class="page-title">Danh sách vé đã đặt</h2>

    <?php if (empty($tickets)): ?>
        <div class="alert alert-info">Bạn chưa đặt vé nào.</div>
        <a href="movies.php" class="btn btn-primary">Đặt vé ngay</a>
    <?php else: ?>
        <div class="responsive-table">
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>Phim</th>
                        <th>Thời gian</th>
                        <th>Phòng</th>
                        <th>Ghế</th>
                        <th>Loại ghế</th>
                        <th>Mã vé</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): 
                        $status = formatStatus($ticket['TRANGTHAI']);
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($ticket['TENPHIM']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($ticket['THOIGIANBATDAU'])) ?></td>
                            <td><?= htmlspecialchars($ticket['TENPHONG']) ?></td>
                            <td><?= htmlspecialchars($ticket['SOGHE']) ?></td>
                            <td><?= htmlspecialchars($ticket['LOAIGHE']) ?></td>
                            <td><code><?= htmlspecialchars($ticket['MAVE']) ?></code></td>
                            <td>
                                <span class="status-badge <?= $status['class'] ?>">
                                    <?= $status['text'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>