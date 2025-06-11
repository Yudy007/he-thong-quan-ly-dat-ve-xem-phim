<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('khachhang');
$base_url = '/he-thong-quan-ly-dat-ve-xem-phim';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: home.php');
    exit;
}

$movieId = $_GET['id'];
$movie = getMovieById($movieId);

if (!$movie) {
    header('Location: home.php?error=invalid_movie');
    exit;
}

$schedules = getSchedules($movieId);
$currentDate = date('Y-m-d');
$upcomingSchedules = array_filter($schedules, function($schedule) use ($currentDate) {
    return date('Y-m-d', strtotime($schedule['ThoiGianBatDau'])) >= $currentDate;
});
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($movie['TenPhim']) ?> - Chi tiết phim</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="movie-detail-container">
            <div class="movie-poster">
                <img src="../assets/images/posters/<?= htmlspecialchars($movie['HinhAnh'] ?? 'default-poster.jpg') ?>" 
                     alt="<?= htmlspecialchars($movie['TenPhim']) ?>"
                     class="poster-image">
                <?php if ($movie['DanhGia'] > 0): ?>
                    <div class="rating-badge">
                        <?= number_format($movie['DanhGia'], 1) ?>/10
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="movie-content">
                <h1 class="movie-title"><?= htmlspecialchars($movie['TenPhim']) ?></h1>
                
                <div class="movie-meta">
                    <div class="meta-item">
                        <span class="meta-label">Thể loại:</span>
                        <span class="meta-value"><?= htmlspecialchars($movie['THELOAI']) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Thời lượng:</span>
                        <span class="meta-value"><?= (int)$movie['THOILUONG'] ?> phút</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Trạng thái:</span>
                        <span class="meta-value">
                            <?= match($movie['TRANGTHAI']) {
                                'dang_chieu' => 'Đang chiếu',
                                'sap_chieu' => 'Sắp chiếu',
                                'ngung_chieu' => 'Ngừng chiếu',
                                default => $movie['TRANGTHAI']
                            } ?>
                        </span>
                    </div>
                </div>
                
                <div class="section">
                    <h3 class="section-title">Nội dung phim</h3>
                    <p class="movie-description"><?= nl2br(htmlspecialchars($movie['MoTa'])) ?></p>
                </div>
                
                <div class="section">
                    <h3 class="section-title">Diễn viên</h3>
                    <p class="movie-cast"><?= htmlspecialchars($movie['DienVien']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="schedule-section">
            <h2 class="section-heading">Lịch chiếu</h2>
            
            <?php if (empty($upcomingSchedules)): ?>
                <div class="empty-state">
                    <img src="../assets/images/empty-schedule.svg" alt="No schedules" class="empty-icon">
                    <p class="empty-message">Hiện chưa có suất chiếu cho phim này</p>
                    <a href="movies.php" class="btn btn-primary">Xem phim khác</a>
                </div>
            <?php else: ?>
                <div class="schedule-tabs">
                    <?php 
                    $groupedSchedules = [];
                    foreach ($upcomingSchedules as $schedule) {
                        $date = date('Y-m-d', strtotime($schedule['ThoiGianBatDau']));
                        $groupedSchedules[$date][] = $schedule;
                    }
                    ?>
                    
                    <?php foreach ($groupedSchedules as $date => $dateSchedules): ?>
                        <div class="schedule-day">
                            <h3 class="schedule-date"><?= date('l, d/m/Y', strtotime($date)) ?></h3>
                            
                            <div class="schedule-grid">
                                <?php foreach ($dateSchedules as $schedule): ?>
                                    <div class="schedule-card">
                                        <div class="schedule-time">
                                            <?= date('H:i', strtotime($schedule['ThoiGianBatDau'])) ?>
                                            <small><?= date('H:i', strtotime($schedule['ThoiGianKetThuc'])) ?></small>
                                        </div>
                                        <div class="schedule-room">
                                            <span>Phòng</span>
                                            <strong><?= htmlspecialchars($schedule['TenPhong']) ?></strong>
                                        </div>
                                        <div class="schedule-price">
                                            <?= number_format($schedule['GiaVe'], 0, ',', '.') ?> VNĐ
                                        </div>
                                        <a href="booking.php?schedule_id=<?= $schedule['MaSuat'] ?>" 
                                           class="btn btn-book">
                                           Đặt vé
                                        </a>
                                    </div>
                                <?php endforeach; ?>
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