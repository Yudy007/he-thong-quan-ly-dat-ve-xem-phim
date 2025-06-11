<?php
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Lấy danh sách phim với filter (nếu có)
$filter = $_GET['filter'] ?? 'dang_chieu';
$validFilters = ['dang_chieu', 'sap_chieu', 'tat_ca'];
$filter = in_array($filter, $validFilters) ? $filter : 'dang_chieu';

$movies = getAllMovies($filter === 'tat_ca' ? null : $filter);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách phim - Movie Booking System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/movies.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Danh sách phim</h1>
        
        <div class="filter-tabs">
            <a href="?filter=dang_chieu" class="filter-tab <?= $filter === 'dang_chieu' ? 'active' : '' ?>">
                Đang chiếu
            </a>
            <a href="?filter=sap_chieu" class="filter-tab <?= $filter === 'sap_chieu' ? 'active' : '' ?>">
                Sắp chiếu
            </a>
            <a href="?filter=tat_ca" class="filter-tab <?= $filter === 'tat_ca' ? 'active' : '' ?>">
                Tất cả phim
            </a>
        </div>
    </div>

    <?php if (empty($movies)): ?>
        <div class="empty-state">
            <img src="assets/images/no-movies.svg" alt="Không có phim" class="empty-icon">
            <p class="empty-message">Hiện không có phim <?= $filter === 'dang_chieu' ? 'đang chiếu' : 'sắp chiếu' ?></p>
            <?php if ($filter !== 'tat_ca'): ?>
                <a href="?filter=tat_ca" class="btn btn-outline">Xem tất cả phim</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="movie-grid">
            <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <div class="movie-poster-container">
                        <img src="<?= !empty($movie['ANH']) ? htmlspecialchars($movie['ANH']) : 'assets/images/default-movie.jpg' ?>" 
                             alt="<?= htmlspecialchars($movie['TENPHIM']) ?>" 
                             class="movie-poster"
                             loading="lazy">
                        <?php if ($movie['TRANGTHAI'] === 'sap_chieu'): ?>
                            <div class="coming-soon-badge">Sắp chiếu</div>
                        <?php endif; ?>
                    </div>

                    <div class="movie-details">
                        <h3 class="movie-title"><?= htmlspecialchars($movie['TENPHIM']) ?></h3>
                        
                        <div class="movie-meta">
                            <span class="meta-item">
                                <i class="icon icon-theloai"></i>
                                <?= htmlspecialchars($movie['THELOAI']) ?>
                            </span>
                            <span class="meta-item">
                                <i class="icon icon-time"></i>
                                <?= $movie['THOILUONG'] ?> phút
                            </span>
                        </div>

                        <div class="movie-actions">
                            <a href="movie_detail.php?id=<?= $movie['MAPHIM'] ?>" class="btn btn-outline">
                                Chi tiết
                            </a>
                            <?php if ($movie['TRANGTHAI'] === 'dang_chieu'): ?>
                                <a href="booking.php?phim=<?= $movie['MAPHIM'] ?>" class="btn btn-primary">
                                    Đặt vé
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>