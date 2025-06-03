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
    <title><?= $movie['TenPhim'] ?> - Chi tiết phim</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="movie-detail">
            <?php
            $posterPath = "../assets/images/posters/" . $movie['HinhAnh'];
            $defaultPoster = "../assets/images/posters/default.jpg";
            if (!file_exists($posterPath)) $posterPath = $defaultPoster;
            ?>
            <img src="<?= $posterPath ?>" class="poster-large">
            
            <div class="movie-info">
                <h1><?= $movie['TenPhim'] ?></h1>
                <p><strong>Thể loại:</strong> <?= $movie['TheLoai'] ?></p>
                <p><strong>Thời lượng:</strong> <?= $movie['ThoiLuong'] ?> phút</p>
                <p><strong>Mô tả:</strong> <?= $movie['MoTa'] ?></p>
            </div>
        </div>
        
        <h2>Lịch chiếu</h2>
        <?php if (empty($schedules)): ?>
            <div class="alert info">Hiện chưa có lịch chiếu cho phim này</div>
        <?php else: ?>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Ngày chiếu</th>
                        <th>Giờ bắt đầu</th>
                        <th>Phòng</th>
                        <th>Giá vé</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($schedule['ThoiGianBatDau'])) ?></td>
                            <td><?= date('H:i', strtotime($schedule['ThoiGianBatDau'])) ?></td>
                            <td>Phòng <?= $schedule['MaPhong'] ?></td>
                            <td><?= number_format($schedule['GiaVe'], 0, ',', '.') ?> VNĐ</td>
                            <td>
                                <a href="booking.php?schedule_id=<?= $schedule['MaSuat'] ?>" class="btn">Đặt vé</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>