<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('khachhang');

$userId = $_SESSION['MaND'];
$tickets = getMyTickets($userId);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Vé đã đặt</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Danh sách vé đã đặt</h2>

    <?php if (empty($tickets)): ?>
        <p>Bạn chưa đặt vé nào.</p>
    <?php else: ?>
        <table class="ticket-table">
            <thead>
                <tr>
                    <th>Phim</th>
                    <th>Thời gian</th>
                    <th>Ghế</th>
                    <th>Mã vé</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><?= htmlspecialchars($ticket['TENPHIM']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($ticket['THOIGIANBATDAU'])) ?></td>
                        <td><?= htmlspecialchars($ticket['SOGHE']) ?></td>
                        <td><?= htmlspecialchars($ticket['MAVE']) ?></td>
                        <td>
                            <span class="badge <?= $ticket['TRANGTHAI'] ?>">
                                <?= $ticket['TRANGTHAI'] == 'da_dat' ? 'Đã đặt' :
                                    ($ticket['TRANGTHAI'] == 'da_kiem_tra' ? 'Đã kiểm tra' : 'Đã huỷ') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
