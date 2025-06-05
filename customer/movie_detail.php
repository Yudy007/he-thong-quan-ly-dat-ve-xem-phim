<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('khachhang');

if (!isset($_GET['id'])) {
    header('Location: home.php');
    exit;
}

$movieId = $_GET['id'];
$movie = getMovieDetails($movieId);
$schedules = getSchedules($movieId);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($movie['TenPhim']) ?> - Chi tiết phim</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="movie-detail">
            <div class="movie-poster">
                <img src="../assets/images/posters/<?= htmlspecialchars($movie['HinhAnh']) ?>" alt="<?= htmlspecialchars($movie['TenPhim']) ?>">
            </div>
            
            <div class="movie-info">
                <h1><?= htmlspecialchars($movie['TenPhim']) ?></h1>
                
                <div class="movie-meta">
                    <span><strong>Thể loại:</strong> <?= htmlspecialchars($movie['TheLoai']) ?></span>
                    <span><strong>Thời lượng:</strong> <?= htmlspecialchars($movie['ThoiLuong']) ?> phút</span>
                    <span><strong>Đánh giá:</strong> <?= htmlspecialchars($movie['DanhGia']) ?>/10</span>
                </div>
                
                <div class="movie-description">
                    <h3>Nội dung phim</h3>
                    <p><?= htmlspecialchars($movie['MoTa']) ?></p>
                </div>
                
                <div class="movie-cast">
                    <h3>Diễn viên</h3>
                    <p><?= htmlspecialchars($movie['DienVien']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="schedule-section">
            <h2>Lịch chiếu</h2>
            
            <?php if (empty($schedules)): ?>
                <div class="alert info">
                    <p>Hiện chưa có suất chiếu cho phim này.</p>
                </div>
            <?php else: ?>
                <div class="schedule-list">
                    <?php foreach ($schedules as $schedule): ?>
                        <div class="schedule-card">
                            <div class="schedule-time">
                                <div class="schedule-date"><?= date('d/m/Y', strtotime($schedule['ThoiGianBatDau'])) ?></div>
                                <div class="schedule-hour"><?= date('H:i', strtotime($schedule['ThoiGianBatDau'])) ?></div>
                            </div>
                            
                            <div class="schedule-info">
                                <div class="schedule-room">Phòng <?= htmlspecialchars($schedule['MaPhong']) ?></div>
                                <div class="schedule-price"><?= number_format($schedule['GiaVe'], 0, ',', '.') ?> VNĐ</div>
                            </div>
                            
                            <div class="schedule-action">
                                <a href="booking.php?schedule_id=<?= $schedule['MaSuat'] ?>" class="btn">Đặt vé</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>