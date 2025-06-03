<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Kiểm tra quyền khách hàng
checkRole('khachhang');

$movies = getMovies();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ khách hàng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h2>Phim đang chiếu</h2>
        
        <div class="movie-grid">
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <?php
                    $posterPath = "../assets/images/posters/" . $movie['HinhAnh'];
                    $defaultPoster = "../assets/images/posters/default.jpg";
                    if (!file_exists($posterPath)) $posterPath = $defaultPoster;
                    ?>
                    <img src="<?= $posterPath ?>" alt="<?= $movie['TenPhim'] ?>">
                    <h3><?= $movie['TenPhim'] ?></h3>
                    <p>Thể loại: <?= $movie['TheLoai'] ?></p>
                    <a href="movie_detail.php?id=<?= $movie['MaPhim'] ?>" class="btn">Xem chi tiết</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>