<?php
session_start();

// Chuyển hướng người dùng đã đăng nhập
if (isset($_SESSION['MaND'])) {
    switch ($_SESSION['VaiTro']) {
        case 'admin':
            header('Location: admin/dashboard.php');
            exit;
        case 'nhanvien':
            header('Location: staff/dashboard.php');
            exit;
        case 'khachhang':
            header('Location: customer/home.php');
            exit;
    }
}

// Lấy danh sách phim đang chiếu
require_once 'includes/functions.php';
$movies = getMovies();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Đặt Vé Xem Phim</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <img src="assets/images/logo.png" alt="Cinema Logo">
                <h1>Rạp Phim ABC</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="#" class="active">Trang chủ</a></li>
                    <li><a href="#now-showing">Phim đang chiếu</a></li>
                    <li><a href="#cinemas">Hệ thống rạp</a></li>
                    <li><a href="#contact">Liên hệ</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <a href="login.php" class="btn btn-login">Đăng nhập</a>
                <a href="register.php" class="btn btn-register">Đăng ký</a>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Trải nghiệm điện ảnh tuyệt vời</h1>
                <p>Đặt vé trực tuyến dễ dàng, nhanh chóng và tiện lợi</p>
                <a href="#now-showing" class="btn btn-primary">Xem phim ngay</a>
            </div>
        </div>
    </section>

    <section id="now-showing" class="now-showing">
        <div class="container">
            <h2>Phim đang chiếu</h2>
            
            <div class="movie-grid">
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <img src="assets/images/posters/<?= htmlspecialchars($movie['HinhAnh']) ?>" alt="<?= htmlspecialchars($movie['TenPhim']) ?>">
                        </div>
                        <div class="movie-info">
                            <h3><?= htmlspecialchars($movie['TenPhim']) ?></h3>
                            <p class="genre"><?= htmlspecialchars($movie['TheLoai']) ?></p>
                            <div class="rating">
                                <span>★</span> <?= htmlspecialchars($movie['DanhGia']) ?>/10
                            </div>
                            <a href="customer/movie_detail.php?id=<?= $movie['MaPhim'] ?>" class="btn">Chi tiết & Đặt vé</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="cinemas" class="cinemas">
        <div class="container">
            <h2>Hệ thống rạp</h2>
            <div class="cinema-grid">
                <div class="cinema-card">
                    <div class="cinema-image">
                        <img src="assets/images/cinema1.jpg" alt="Rạp Quận 1">
                    </div>
                    <div class="cinema-info">
                        <h3>Rạp Quận 1</h3>
                        <p>123 Đường Nguyễn Huệ, Quận 1, TP.HCM</p>
                        <p>Hotline: 0909 123 456</p>
                    </div>
                </div>
                
                <div class="cinema-card">
                    <div class="cinema-image">
                        <img src="assets/images/cinema2.jpg" alt="Rạp Quận 7">
                    </div>
                    <div class="cinema-info">
                        <h3>Rạp Quận 7</h3>
                        <p>456 Đường Nguyễn Văn Linh, Quận 7, TP.HCM</p>
                        <p>Hotline: 0909 789 012</p>
                    </div>
                </div>
                
                <div class="cinema-card">
                    <div class="cinema-image">
                        <img src="assets/images/cinema3.jpg" alt="Rạp Gò Vấp">
                    </div>
                    <div class="cinema-info">
                        <h3>Rạp Gò Vấp</h3>
                        <p>789 Đường Quang Trung, Gò Vấp, TP.HCM</p>
                        <p>Hotline: 0909 345 678</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="contact-content">
                <div class="contact-info">
                    <h2>Liên hệ với chúng tôi</h2>
                    <p>Có câu hỏi hoặc cần hỗ trợ? Hãy liên hệ với chúng tôi</p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <i>📞</i>
                            <span>Hotline: 1900 1234</span>
                        </div>
                        <div class="contact-item">
                            <i>✉️</i>
                            <span>Email: support@rapabc.vn</span>
                        </div>
                        <div class="contact-item">
                            <i>🏢</i>
                            <span>Trụ sở: 123 Đường ABC, Quận XYZ, TP.HCM</span>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <form>
                        <div class="form-group">
                            <input type="text" placeholder="Họ và tên" required>
                        </div>
                        <div class="form-group">
                            <input type="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" placeholder="Số điện thoại">
                        </div>
                        <div class="form-group">
                            <textarea placeholder="Nội dung liên hệ" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn">Gửi liên hệ</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Về chúng tôi</h3>
                    <p>Hệ thống rạp chiếu phim hiện đại với chất lượng dịch vụ hàng đầu.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Liên kết nhanh</h3>
                    <ul>
                        <li><a href="#">Trang chủ</a></li>
                        <li><a href="#now-showing">Phim đang chiếu</a></li>
                        <li><a href="#cinemas">Hệ thống rạp</a></li>
                        <li><a href="#contact">Liên hệ</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Kết nối với chúng tôi</h3>
                    <div class="social-links">
                        <a href="#"><img src="assets/images/facebook.png" alt="Facebook"></a>
                        <a href="#"><img src="assets/images/zalo.png" alt="Zalo"></a>
                        <a href="#"><img src="assets/images/instagram.png" alt="Instagram"></a>
                        <a href="#"><img src="assets/images/tiktok.png" alt="TikTok"></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2023 Rạp Phim ABC. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/scripts.js"></script>
</body>
</html>