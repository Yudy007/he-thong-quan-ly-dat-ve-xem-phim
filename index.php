<?php
require_once 'includes/functions.php';
$movies = getAllMovies();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ Movie Booking System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
        }

        .movie-poster {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 0.8rem;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #007bff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
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
                    <?php if (!empty($movie['ANH'])): ?>
                        <img src="<?= htmlspecialchars($movie['ANH']) ?>" alt="Ảnh phim" class="movie-poster">
                    <?php else: ?>
                        <img src="assets/images/default-movie.jpg" alt="Ảnh mặc định" class="movie-poster">
                    <?php endif; ?>

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
                        <a href="customer/booking.php?phim=<?= $movie['MAPHIM'] ?>" class="btn">Đặt vé</a>

                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
