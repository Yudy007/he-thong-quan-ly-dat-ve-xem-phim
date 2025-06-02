<?php
// File: /staff/ticket_checker.php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('staff');

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_code = $_POST['ticket_code'];
    $result = checkTicket($ticket_code); // Hàm kiểm tra vé từ Oracle
}

include_once '../includes/header.php';
?>

<div class="container">
    <h2>Kiểm tra vé</h2>
    
    <form method="POST" class="ticket-form">
        <div class="input-group">
            <input type="text" name="ticket_code" 
                   placeholder="Nhập mã vé hoặc quét QR" required>
            <button type="submit">Kiểm tra</button>
        </div>
    </form>

    <?php if ($result): ?>
    <div class="ticket-result <?= $result['valid'] ? 'valid' : 'invalid' ?>">
        <?php if ($result['valid']): ?>
        <div class="status success">VÉ HỢP LỆ</div>
        <div class="ticket-details">
            <p><strong>Phim:</strong> <?= $result['ten_phim'] ?></p>
            <p><strong>Suất chiếu:</strong> <?= $result['gio_chieu'] ?></p>
            <p><strong>Phòng:</strong> <?= $result['phong_chieu'] ?></p>
            <p><strong>Ghế:</strong> <?= $result['so_ghe'] ?></p>
            <p><strong>Ngày đặt:</strong> <?= $result['ngay_dat'] ?></p>
        </div>
        <?php else: ?>
        <div class="status error">VÉ KHÔNG HỢP LỆ</div>
        <p class="reason"><?= $result['message'] ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>