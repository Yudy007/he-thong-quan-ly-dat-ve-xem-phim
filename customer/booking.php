<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('khachhang'); // Chỉ khách hàng mới được đặt vé

$conn = connectOracle();

// Bước 1: Lấy danh sách suất chiếu của phim đã chọn
$phimId = $_GET['phim'] ?? null;
$suatChieuId = $_GET['suat'] ?? null;

$suatChieus = [];
$gheTrong = [];
$tenPhim = null;
$success = false;

if ($phimId) {
    $suatChieus = getSchedules($phimId);
    $tenPhim = getMovieNameById($phimId); // Cần có hàm này trong functions.php
}

if ($suatChieuId) {
    $gheTrong = getAvailableSeats($suatChieuId);
}

// Bước 2: Xử lý đặt vé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maGhe']) && $suatChieuId) {
    $maNguoiDung = $_SESSION['MaND'];
    $maGhe = $_POST['maGhe'];

    if (bookTicket($maNguoiDung, $suatChieuId, [$maGhe])) {
        $success = true;
        $gheTrong = getAvailableSeats($suatChieuId); // Cập nhật lại ghế
    } else {
        $error = "Đặt vé thất bại. Ghế đã có người đặt.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt vé xem phim</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>🎟️ Đặt vé xem phim</h2>

    <?php if ($tenPhim): ?>
        <p><strong>Phim:</strong> <?= htmlspecialchars($tenPhim) ?></p>
    <?php endif; ?>

    <!-- Bước 1: Chọn suất chiếu -->
    <form method="get" action="booking.php">
        <input type="hidden" name="phim" value="<?= $phimId ?>">
        <label>Chọn suất chiếu:</label>
        <select name="suat" required onchange="this.form.submit()">
            <option value="">-- Chọn suất --</option>
            <?php foreach ($suatChieus as $suat): ?>
                <option value="<?= $suat['MASUAT'] ?>" <?= ($suat['MASUAT'] == $suatChieuId ? 'selected' : '') ?>>
                    <?= date('d/m/Y H:i', strtotime($suat['THOIGIANBATDAU'])) ?> – 
                    Phòng <?= $suat['TENPHONG'] ?> – Giá <?= number_format($suat['GIAVE']) ?>đ
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- Bước 2: Chọn ghế -->
    <?php if ($suatChieuId): ?>
        <h3>Chọn ghế:</h3>

        <?php if ($success): ?>
            <div class="alert success">Đặt vé thành công!</div>
        <?php elseif (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="seat-grid">
                <?php foreach ($gheTrong as $ghe): ?>
                    <label class="seat">
                        <input type="radio" name="maGhe" value="<?= $ghe['MAGHE'] ?>" required>
                        <?= $ghe['SOGHE'] ?> (<?= $ghe['LOAIGHE'] ?>)
                    </label>
                <?php endforeach; ?>
            </div>
            <br>
            <button type="submit" class="btn">Xác nhận đặt vé</button>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
