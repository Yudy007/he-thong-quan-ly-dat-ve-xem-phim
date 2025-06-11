<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/db_connect.php'; // Thêm kết nối DB
checkRole('khachhang');
$base_url = '/he-thong-quan-ly-dat-ve-xem-phim';
// Kiểm tra và lọc dữ liệu đầu vào
$phimId = filter_input(INPUT_GET, 'phim', FILTER_SANITIZE_STRING);
$suatChieuId = filter_input(INPUT_GET, 'suat', FILTER_SANITIZE_STRING);

$suatChieus = [];
$gheTrong = [];
$tenPhim = null;
$success = false;

// Xử lý khi có chọn phim
if ($phimId) {
    $suatChieus = getSchedules($phimId);
    $tenPhim = getTenPhimById($phimId); // Sửa tên hàm cho khớp với functions.php
    
    // Kiểm tra nếu không tìm thấy phim
    if (!$tenPhim) {
        header("Location: movies.php?error=invalid_movie");
        exit;
    }
}

// Xử lý khi có chọn suất chiếu
if ($suatChieuId) {
    // Kiểm tra suất chiếu có thuộc phim đã chọn không
    $validSchedule = false;
    foreach ($suatChieus as $suat) {
        if ($suat['MASUAT'] == $suatChieuId) {
            $validSchedule = true;
            break;
        }
    }
    
    if (!$validSchedule) {
        header("Location: booking.php?phim=" . urlencode($phimId) . "&error=invalid_schedule");
        exit;
    }
    
    $gheTrong = getAvailableSeats($suatChieuId);
}

// Xử lý đặt vé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maGhe']) && $suatChieuId) {
    $maNguoiDung = $_SESSION['MaND'];
    $maGhe = filter_input(INPUT_POST, 'maGhe', FILTER_SANITIZE_STRING);
    
    try {
        // Kiểm tra ghế còn trống không (tránh race condition)
        $availableSeats = getAvailableSeats($suatChieuId);
        $isSeatAvailable = false;
        foreach ($availableSeats as $seat) {
            if ($seat['MAGHE'] == $maGhe) {
                $isSeatAvailable = true;
                break;
            }
        }
        
        if (!$isSeatAvailable) {
            throw new Exception("Ghế đã có người đặt. Vui lòng chọn ghế khác.");
        }
        
        // Thực hiện đặt vé
        if (insertVe($suatChieuId, $maGhe, $maNguoiDung)) {
            $success = true;
            $_SESSION['booking_success'] = true; // Dùng session flash
            header("Location: booking.php?phim=" . urlencode($phimId) . "&suat=" . urlencode($suatChieuId));
            exit;
        } else {
            throw new Exception("Đặt vé thất bại do lỗi hệ thống.");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Hiển thị thông báo từ session
if (isset($_SESSION['booking_success'])) {
    $success = true;
    unset($_SESSION['booking_success']);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt vé xem phim</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>🎟️ Đặt vé xem phim</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert error">
            <?= match($_GET['error']) {
                'invalid_movie' => 'Phim không tồn tại',
                'invalid_schedule' => 'Suất chiếu không hợp lệ',
                default => 'Có lỗi xảy ra'
            } ?>
        </div>
    <?php endif; ?>

    <?php if ($tenPhim): ?>
        <div class="movie-info">
            <h3><?= htmlspecialchars($tenPhim) ?></h3>
        </div>
    <?php endif; ?>

    <!-- Bước 1: Chọn suất chiếu -->
    <div class="booking-step">
        <h3>1. Chọn suất chiếu</h3>
        <form method="get" action="booking.php">
            <input type="hidden" name="phim" value="<?= htmlspecialchars($phimId) ?>">
            <div class="form-group">
                <select name="suat" class="form-control" required onchange="this.form.submit()">
                    <option value="">-- Chọn suất chiếu --</option>
                    <?php foreach ($suatChieus as $suat): 
                        $startTime = strtotime($suat['THOIGIANBATDAU']);
                        $endTime = strtotime($suat['THOIGIANKETTHUC']);
                    ?>
                        <option value="<?= htmlspecialchars($suat['MASUAT']) ?>" 
                            <?= ($suat['MASUAT'] == $suatChieuId ? 'selected' : '') ?>>
                            <?= date('d/m/Y H:i', $startTime) ?> - <?= date('H:i', $endTime) ?> | 
                            Phòng <?= htmlspecialchars($suat['TENPHONG']) ?> | 
                            <?= number_format($suat['GIAVE']) ?>₫
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Bước 2: Chọn ghế -->
    <?php if ($suatChieuId): ?>
        <div class="booking-step">
            <h3>2. Chọn ghế</h3>
            
            <?php if ($success): ?>
                <div class="alert success">
                    ✅ Đặt vé thành công! 
                    <a href="my_tickets.php">Xem vé đã đặt</a>
                </div>
            <?php elseif (isset($error)): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if (empty($gheTrong)): ?>
                <div class="alert warning">Suất chiếu này đã hết ghế trống</div>
            <?php else: ?>
                <div class="screen">MÀN HÌNH</div>
                <form method="POST" class="seat-booking">
                    <div class="seat-grid">
                        <?php foreach ($gheTrong as $ghe): ?>
                            <label class="seat">
                                <input type="radio" name="maGhe" value="<?= htmlspecialchars($ghe['MAGHE']) ?>" required>
                                <span><?= htmlspecialchars($ghe['SOGHE']) ?></span>
                                <small><?= $ghe['LOAIGHE'] ?></small>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Xác nhận đặt vé</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Xác nhận trước khi đặt vé
document.querySelector('.seat-booking')?.addEventListener('submit', function(e) {
    if (!confirm('Xác nhận đặt vé này?')) {
        e.preventDefault();
    }
});
</script>
</body>
</html>