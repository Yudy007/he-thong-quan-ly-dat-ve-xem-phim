<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Kiểm tra quyền khách hàng
checkRole('khachhang');

$movies = getMovies();
$myTickets = getMyTickets($_SESSION['MaND']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ khách hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .welcome-section { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; border-radius: 15px; margin: 20px 0; text-align: center; }
        .quick-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; padding: 20px; border-radius: 10px; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; }
        .movie-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin: 20px 0; }
        .movie-card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .movie-card:hover { transform: translateY(-5px); }
        .movie-card img { width: 100%; height: 400px; object-fit: cover; }
        .movie-card-content { padding: 20px; }
        .movie-card h3 { color: #333; margin-bottom: 10px; font-size: 1.2em; }
        .movie-card p { color: #666; margin-bottom: 15px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 25px; display: inline-block; transition: all 0.3s; }
        .btn:hover { background: #0056b3; transform: scale(1.05); }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .section-title { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; margin: 30px 0 20px 0; }
    </style>
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