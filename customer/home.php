<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
$base_url = '/he-thong-quan-ly-dat-ve-xem-phim';
// Kiểm tra quyền khách hàng
checkRole('khachhang');

$movies = getAllMovies();
$myTickets = getMyTickets($_SESSION['MaND']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ khách hàng</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="welcome-section">
            <h1>🎬 Chào mừng <?= htmlspecialchars($_SESSION['hoTen']) ?>!</h1>
            <p>Khám phá và đặt vé xem phim yêu thích của bạn</p>
        </div>

        <!-- Thống kê nhanh -->
        <div class="quick-stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($movies) ?></div>
                <div>Phim đang chiếu</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count($myTickets) ?></div>
                <div>Vé đã đặt</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($myTickets, function($ticket) { return strtotime($ticket['THOIGIANBATDAU']) > time(); })) ?></div>
                <div>Vé sắp tới</div>
            </div>
        </div>

        <!-- Liên kết nhanh -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="booking.php" class="btn btn-success" style="margin: 0 10px; font-size: 1.1em;">🎫 Đặt vé ngay</a>
            <a href="my_tickets.php" class="btn" style="margin: 0 10px; font-size: 1.1em;">📋 Xem vé đã đặt</a>
        </div>

        <h2 class="section-title">🎬 Phim đang chiếu</h2>

        <?php if (count($movies) > 0): ?>
            <div class="movie-grid">
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card">
                        <?php
                        $posterPath = "../assets/images/posters/" . ($movie['HINHANH'] ?? 'default.jpg');
                        $defaultPoster = "../assets/images/posters/default.jpg";
                        if (!file_exists($posterPath)) $posterPath = $defaultPoster;
                        ?>
                        <img src="<?= $posterPath ?>" alt="<?= htmlspecialchars($movie['TENPHIM']) ?>">
                        <div class="movie-card-content">
                            <h3><?= htmlspecialchars($movie['TENPHIM']) ?></h3>
                            <p><strong>Thể loại:</strong> <?= htmlspecialchars($movie['THELOAI']) ?></p>
                            <p><strong>Thời lượng:</strong> <?= $movie['THOILUONG'] ?> phút</p>
                            <p><strong>Trạng thái:</strong>
                                <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.9em;">
                                    <?= $movie['TRANGTHAI'] == 'dang_chieu' ? 'Đang chiếu' : ucfirst($movie['TRANGTHAI']) ?>
                                </span>
                            </p>
                            <a href="movie_detail.php?id=<?= $movie['MAPHIM'] ?>" class="btn">Xem chi tiết & Đặt vé</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 10px;">
                <h3>😔 Hiện tại chưa có phim nào đang chiếu</h3>
                <p>Vui lòng quay lại sau để xem các bộ phim mới nhất!</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>