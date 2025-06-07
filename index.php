<?php
require_once 'includes/functions.php';
$movies = getAllMovies(); // Hàm này đã viết trong functions.php
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ Movie Booking System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h1 class="page-title">Danh sách phim</h1>

    <div class="movie-list">
        <?php if (empty($movies)): ?>
            <p>Không có phim nào được hiển thị.</p>
        <?php else: ?>
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <h3><?= htmlspecialchars($movie['TENPHIM']) ?></h3>
                    <p><strong>Thể loại:</strong> <?= htmlspecialchars($movie['THELOAI']) ?></p>
                    <p><strong>Thời lượng:</strong> <?= $movie['THOILUONG'] ?> phút</p>
                    <p><strong>Trạng thái:</strong> 
                        <span class="badge <?= $movie['TRANGTHAI'] ?>">
                            <?= $movie['TRANGTHAI'] == 'dang_chieu' ? 'Đang chiếu' : 
                                ($movie['TRANGTHAI'] == 'sap_chieu' ? 'Sắp chiếu' : 'Ngừng chiếu') ?>
                        </span>
                    </p>
                    <?php if ($movie['TRANGTHAI'] != 'ngung_chieu'): ?>
                        <a href="booking.php?phim=<?= $movie['MAPHIM'] ?>" class="btn">Đặt vé</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
