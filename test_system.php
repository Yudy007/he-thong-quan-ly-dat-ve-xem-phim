<?php
// File test để kiểm tra hệ thống phân quyền
session_start();
require_once 'includes/functions.php';

echo "<h1>🧪 Test Hệ thống Phân quyền</h1>";

// Test 1: Kiểm tra kết nối database
echo "<h2>1. Test kết nối Database</h2>";
try {
    $conn = connectOracle();
    if ($conn) {
        echo "✅ Kết nối Oracle thành công<br>";
        oci_close($conn);
    } else {
        echo "❌ Không thể kết nối Oracle<br>";
    }
} catch (Exception $e) {
    echo "❌ Lỗi kết nối: " . $e->getMessage() . "<br>";
}

// Test 2: Kiểm tra các hàm cơ bản
echo "<h2>2. Test các hàm cơ bản</h2>";

// Test getMovies
try {
    $movies = getMovies();
    echo "✅ Hàm getMovies(): " . count($movies) . " phim<br>";
} catch (Exception $e) {
    echo "❌ Lỗi getMovies(): " . $e->getMessage() . "<br>";
}

// Test getAdminStats
try {
    $stats = getAdminStats();
    echo "✅ Hàm getAdminStats(): " . json_encode($stats) . "<br>";
} catch (Exception $e) {
    echo "❌ Lỗi getAdminStats(): " . $e->getMessage() . "<br>";
}

// Test getTodaySchedules
try {
    $schedules = getTodaySchedules();
    echo "✅ Hàm getTodaySchedules(): " . count($schedules) . " suất chiếu<br>";
} catch (Exception $e) {
    echo "❌ Lỗi getTodaySchedules(): " . $e->getMessage() . "<br>";
}

// Test getRooms
try {
    $rooms = getRooms();
    echo "✅ Hàm getRooms(): " . count($rooms) . " phòng<br>";
} catch (Exception $e) {
    echo "❌ Lỗi getRooms(): " . $e->getMessage() . "<br>";
}

// Test getStaffList
try {
    $staff = getStaffList();
    echo "✅ Hàm getStaffList(): " . count($staff) . " nhân viên<br>";
} catch (Exception $e) {
    echo "❌ Lỗi getStaffList(): " . $e->getMessage() . "<br>";
}

echo "<h2>3. Test Phân quyền</h2>";

// Kiểm tra session hiện tại
if (isset($_SESSION['MaND'])) {
    echo "👤 Đang đăng nhập: " . $_SESSION['hoTen'] . " (" . $_SESSION['VaiTro'] . ")<br>";
    
    switch ($_SESSION['VaiTro']) {
        case 'admin':
            echo "🔑 Quyền Admin: Có thể truy cập tất cả chức năng<br>";
            echo "📋 Các trang có thể truy cập:<br>";
            echo "- <a href='admin/dashboard.php'>Admin Dashboard</a><br>";
            echo "- <a href='admin/manage_movies.php'>Quản lý phim</a><br>";
            echo "- <a href='admin/manage_schedules.php'>Quản lý suất chiếu</a><br>";
            echo "- <a href='admin/manage_rooms.php'>Quản lý phòng & ghế</a><br>";
            echo "- <a href='admin/manage_users.php'>Quản lý người dùng</a><br>";
            echo "- <a href='admin/manage_staff.php'>Quản lý nhân viên</a><br>";
            echo "- <a href='admin/reports.php'>Thống kê</a><br>";
            break;
            
        case 'nhanvien':
            echo "🎫 Quyền Nhân viên: Chỉ kiểm tra vé và xác nhận vé<br>";
            echo "📋 Các trang có thể truy cập:<br>";
            echo "- <a href='staff/dashboard.php'>Staff Dashboard</a><br>";
            echo "- <a href='staff/ticket_checker.php'>Kiểm tra vé</a><br>";
            break;
            
        case 'khachhang':
            echo "🎬 Quyền Khách hàng: Đặt vé và xem vé đã đặt<br>";
            echo "📋 Các trang có thể truy cập:<br>";
            echo "- <a href='customer/home.php'>Trang chủ khách hàng</a><br>";
            echo "- <a href='customer/booking.php'>Đặt vé</a><br>";
            echo "- <a href='customer/my_tickets.php'>Vé của tôi</a><br>";
            break;
    }
} else {
    echo "🚫 Chưa đăng nhập<br>";
    echo "📋 Các trang có thể truy cập:<br>";
    echo "- <a href='index.php'>Trang chủ</a><br>";
    echo "- <a href='login.php'>Đăng nhập</a><br>";
    echo "- <a href='register.php'>Đăng ký (chỉ khách hàng)</a><br>";
}

echo "<h2>4. Test Bảo mật</h2>";

// Test truy cập trái phép
echo "🔒 Test truy cập trái phép:<br>";

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] !== 'admin') {
    echo "❌ Không thể truy cập trang admin (đúng)<br>";
} else {
    echo "✅ Có thể truy cập trang admin<br>";
}

if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] !== 'nhanvien') {
    echo "❌ Không thể truy cập trang nhân viên (đúng nếu không phải nhân viên)<br>";
} else {
    echo "✅ Có thể truy cập trang nhân viên<br>";
}

echo "<h2>5. Cấu trúc File</h2>";

$files_to_check = [
    'admin/dashboard.php' => 'Admin Dashboard',
    'admin/manage_movies.php' => 'Quản lý phim',
    'admin/manage_schedules.php' => 'Quản lý suất chiếu',
    'admin/manage_rooms.php' => 'Quản lý phòng & ghế',
    'admin/manage_users.php' => 'Quản lý người dùng',
    'admin/manage_staff.php' => 'Quản lý nhân viên',
    'admin/reports.php' => 'Thống kê',
    'staff/dashboard.php' => 'Staff Dashboard',
    'staff/ticket_checker.php' => 'Kiểm tra vé',
    'customer/home.php' => 'Trang chủ khách hàng',
    'customer/booking.php' => 'Đặt vé',
    'customer/my_tickets.php' => 'Vé của tôi',
    'includes/auth.php' => 'Hệ thống xác thực',
    'includes/functions.php' => 'Các hàm chính',
    'login.php' => 'Đăng nhập',
    'register.php' => 'Đăng ký',
    'index.php' => 'Trang chủ'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description ($file)<br>";
    } else {
        echo "❌ $description ($file) - Không tồn tại<br>";
    }
}

echo "<h2>6. Hướng dẫn sử dụng</h2>";
echo "<div style='background: #f0f8ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>🎯 Hệ thống đã được cấu hình với 3 loại tài khoản:</h3>";
echo "<ul>";
echo "<li><strong>👑 Admin:</strong> Toàn quyền quản lý hệ thống, tạo/xóa nhân viên</li>";
echo "<li><strong>👨‍💼 Nhân viên:</strong> Chỉ kiểm tra và xác nhận vé</li>";
echo "<li><strong>🎬 Khách hàng:</strong> Tự đăng ký, đặt vé, xem vé đã đặt</li>";
echo "</ul>";

echo "<h3>🔐 Phân quyền chi tiết:</h3>";
echo "<ul>";
echo "<li><strong>Admin:</strong> Đăng nhập, Quản lý phim, Quản lý suất chiếu, Quản lý ghế/phòng, Thống kê, Tạo/xóa nhân viên</li>";
echo "<li><strong>Nhân viên:</strong> Đăng nhập, Kiểm tra vé, Xác nhận vé</li>";
echo "<li><strong>Khách hàng:</strong> Đăng nhập, Đăng ký, Đặt vé, Xem vé đã đặt</li>";
echo "</ul>";

echo "<h3>🚀 Để bắt đầu:</h3>";
echo "<ol>";
echo "<li>Tạo tài khoản admin đầu tiên trong database</li>";
echo "<li>Đăng nhập với tài khoản admin</li>";
echo "<li>Tạo phòng chiếu và ghế</li>";
echo "<li>Thêm phim và suất chiếu</li>";
echo "<li>Tạo tài khoản nhân viên</li>";
echo "<li>Khách hàng có thể tự đăng ký và đặt vé</li>";
echo "</ol>";

echo "<h3>🧪 Test từng loại tài khoản:</h3>";
echo "<ul>";
echo "<li><strong>Admin:</strong> <a href='create_admin.php'>Tạo admin</a> → <a href='debug_login.php'>Test login</a></li>";
echo "<li><strong>Nhân viên:</strong> <a href='create_staff_test.php'>Tạo & test nhân viên</a></li>";
echo "<li><strong>Khách hàng:</strong> <a href='register.php'>Đăng ký khách hàng</a></li>";
echo "</ul>";
echo "</div>";

echo "<p style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🏠 Trang chủ</a>";
echo "<a href='create_admin.php' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>👑 Tạo Admin</a>";
echo "<a href='create_staff_test.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>👨‍💼 Test Nhân viên</a>";
echo "<a href='debug_login.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🔍 Debug Login</a>";
echo "</p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h2 { color: #666; margin-top: 30px; }
h3 { color: #333; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
