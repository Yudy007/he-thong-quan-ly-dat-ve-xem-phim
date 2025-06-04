<?php
require_once '../includes/auth.php';
checkRole('nhanvien');

$ticket = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticketCode = $_POST['ticket_code'];
    $ticket = verifyTicket($ticketCode);
    
    if (!$ticket) {
        $error = "Vé không hợp lệ hoặc đã được kiểm tra trước đó";
    } else {
        markTicketChecked($ticket['MaVe']);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kiểm tra vé</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Kiểm tra vé vào cửa</h1>
        
        <form method="POST" class="ticket-check-form">
            <div class="form-group">
                <label>Nhập mã vé hoặc quét QR code</label>
                <input type="text" name="ticket_code" required 
                       placeholder="VD: VE001234" autofocus>
            </div>
            
            <button type="submit" class="btn">Kiểm tra vé</button>
        </form>
        
        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($ticket): ?>
        <div class="ticket-result">
            <div class="ticket-header">
                <h2>✅ Vé hợp lệ</h2>
                <p>Mã vé: <strong><?= $ticket['MaVe'] ?></strong></p>
            </div>
            
            <div class="ticket-details">
                <div class="ticket-info">
                    <p><strong>Phim:</strong> <?= $ticket['TenPhim'] ?></p>
                    <p><strong>Phòng:</strong> <?= $ticket['MaPhong'] ?></p>
                    <p><strong>Thời gian:</strong> <?= date('d/m/Y H:i', strtotime($ticket['ThoiGianBatDau'])) ?></p>
                    <p><strong>Ghế:</strong> <?= implode(', ', $ticket['DanhSachGhe']) ?></p>
                </div>
                
                <div class="ticket-qr">
                    <img src="../assets/images/qr-codes/<?= $ticket['MaVe'] ?>.png" 
                         alt="QR Code vé <?= $ticket['MaVe'] ?>">
                </div>
            </div>
            
            <div class="ticket-actions">
                <button class="btn" onclick="window.print()">In vé</button>
                <a href="ticket_checker.php" class="btn">Kiểm tra vé khác</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>