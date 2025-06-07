<?php
// Demo tạo và test tài khoản nhân viên
session_start();
require_once 'includes/functions.php';

echo "<h1>👨‍💼 Demo Tài khoản Nhân viên</h1>";

// Kiểm tra quyền admin để tạo nhân viên
if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] !== 'admin') {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>🚫 Cần quyền Admin</h2>";
    echo "<p>Chỉ có Admin mới có thể tạo tài khoản nhân viên.</p>";
    echo "<p><a href='login.php' style='color: #007bff;'>Đăng nhập Admin</a> để tiếp tục.</p>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>✅ Đã đăng nhập Admin</h2>";
    echo "<p>Xin chào <strong>" . $_SESSION['hoTen'] . "</strong>! Bạn có thể tạo tài khoản nhân viên.</p>";
    echo "</div>";
}

// Tạo tài khoản nhân viên demo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_demo_staff'])) {
    if ($_SESSION['VaiTro'] === 'admin') {
        $staff_data = [
            'TenDangNhap' => 'nhanvien01',
            'MatKhau' => 'nv123456',
            'HoTen' => 'Nguyễn Văn Nhân Viên'
        ];
        
        if (createStaffAccount($staff_data)) {
            echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
            echo "<h3>✅ Tạo tài khoản nhân viên thành công!</h3>";
            echo "<p><strong>Tên đăng nhập:</strong> nhanvien01</p>";
            echo "<p><strong>Mật khẩu:</strong> nv123456</p>";
            echo "<p><strong>Họ tên:</strong> Nguyễn Văn Nhân Viên</p>";
            echo "<p><strong>Vai trò:</strong> Nhân viên</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
            echo "<h3>❌ Lỗi tạo tài khoản</h3>";
            echo "<p>Có thể tên đăng nhập đã tồn tại hoặc có lỗi database.</p>";
            echo "</div>";
        }
    }
}

// Hiển thị danh sách nhân viên hiện có
echo "<h2>📋 Danh sách nhân viên hiện có</h2>";

try {
    $staffList = getStaffList();
    
    if (count($staffList) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th style='padding: 10px;'>Mã NV</th>";
        echo "<th style='padding: 10px;'>Tên đăng nhập</th>";
        echo "<th style='padding: 10px;'>Họ tên</th>";
        echo "<th style='padding: 10px;'>Trạng thái</th>";
        echo "</tr>";
        
        foreach ($staffList as $staff) {
            echo "<tr>";
            echo "<td style='padding: 10px;'>" . htmlspecialchars($staff['MAND']) . "</td>";
            echo "<td style='padding: 10px;'>" . htmlspecialchars($staff['TENDANGNHAP']) . "</td>";
            echo "<td style='padding: 10px;'>" . htmlspecialchars($staff['HOTEN']) . "</td>";
            echo "<td style='padding: 10px;'><span style='background: #28a745; color: white; padding: 4px 8px; border-radius: 4px;'>Hoạt động</span></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;'>";
        echo "<p>Chưa có nhân viên nào trong hệ thống.</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi lấy danh sách nhân viên: " . $e->getMessage() . "</p>";
}

// Form tạo nhân viên demo
if (isset($_SESSION['VaiTro']) && $_SESSION['VaiTro'] === 'admin') {
    echo "<h2>🆕 Tạo tài khoản nhân viên demo</h2>";
    echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<p>Tạo tài khoản nhân viên mẫu để test:</p>";
    echo "<ul>";
    echo "<li><strong>Tên đăng nhập:</strong> nhanvien01</li>";
    echo "<li><strong>Mật khẩu:</strong> nv123456</li>";
    echo "<li><strong>Họ tên:</strong> Nguyễn Văn Nhân Viên</li>";
    echo "</ul>";
    echo "<button type='submit' name='create_demo_staff' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>👨‍💼 Tạo nhân viên demo</button>";
    echo "</form>";
}

// Hướng dẫn test
echo "<h2>🧪 Hướng dẫn test tài khoản nhân viên</h2>";
echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>Bước 1: Tạo tài khoản nhân viên (Admin)</h3>";
echo "<ol>";
echo "<li>Đăng nhập với tài khoản admin</li>";
echo "<li>Truy cập <a href='admin/manage_staff.php'>Quản lý nhân viên</a></li>";
echo "<li>Hoặc dùng nút 'Tạo nhân viên demo' ở trên</li>";
echo "</ol>";

echo "<h3>Bước 2: Test đăng nhập nhân viên</h3>";
echo "<ol>";
echo "<li>Đăng xuất tài khoản admin</li>";
echo "<li>Đăng nhập với tài khoản nhân viên vừa tạo</li>";
echo "<li>Kiểm tra giao diện và quyền hạn</li>";
echo "</ol>";

echo "<h3>Bước 3: Kiểm tra quyền hạn</h3>";
echo "<ul>";
echo "<li>✅ Có thể xem Dashboard nhân viên</li>";
echo "<li>✅ Có thể kiểm tra vé</li>";
echo "<li>❌ Không thể truy cập trang admin</li>";
echo "<li>❌ Không thể tạo tài khoản</li>";
echo "</ul>";
echo "</div>";

// So sánh quyền hạn
echo "<h2>⚖️ So sánh quyền hạn</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th style='padding: 10px;'>Chức năng</th>";
echo "<th style='padding: 10px;'>👑 Admin</th>";
echo "<th style='padding: 10px;'>👨‍💼 Nhân viên</th>";
echo "<th style='padding: 10px;'>🎬 Khách hàng</th>";
echo "</tr>";

$permissions = [
    'Đăng nhập' => ['✅', '✅', '✅'],
    'Tự đăng ký' => ['❌', '❌', '✅'],
    'Quản lý phim' => ['✅', '❌', '❌'],
    'Quản lý suất chiếu' => ['✅', '❌', '❌'],
    'Quản lý phòng/ghế' => ['✅', '❌', '❌'],
    'Tạo/xóa nhân viên' => ['✅', '❌', '❌'],
    'Kiểm tra vé' => ['✅', '✅', '❌'],
    'Đặt vé' => ['✅', '❌', '✅'],
    'Xem thống kê' => ['✅', '❌', '❌']
];

foreach ($permissions as $function => $roles) {
    echo "<tr>";
    echo "<td style='padding: 10px;'><strong>$function</strong></td>";
    echo "<td style='padding: 10px; text-align: center;'>{$roles[0]}</td>";
    echo "<td style='padding: 10px; text-align: center;'>{$roles[1]}</td>";
    echo "<td style='padding: 10px; text-align: center;'>{$roles[2]}</td>";
    echo "</tr>";
}
echo "</table>";

// Test links
echo "<h2>🔗 Test Links</h2>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>Dành cho Admin:</h3>";
echo "<p><a href='admin/manage_staff.php' style='color: #007bff;'>👨‍💼 Quản lý nhân viên (Admin)</a></p>";
echo "<p><a href='admin/dashboard.php' style='color: #007bff;'>🏠 Admin Dashboard</a></p>";

echo "<h3>Dành cho Nhân viên:</h3>";
echo "<p><a href='staff/dashboard.php' style='color: #007bff;'>🏠 Staff Dashboard</a></p>";
echo "<p><a href='staff/ticket_checker.php' style='color: #007bff;'>🎫 Kiểm tra vé</a></p>";

echo "<h3>Chung:</h3>";
echo "<p><a href='login.php' style='color: #007bff;'>🔑 Đăng nhập</a></p>";
echo "<p><a href='logout.php' style='color: #007bff;'>🚪 Đăng xuất</a></p>";
echo "<p><a href='debug_login.php' style='color: #007bff;'>🔍 Debug đăng nhập</a></p>";
echo "</div>";

echo "<p style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🏠 Trang chủ</a>";
echo "<a href='test_system.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🧪 Test hệ thống</a>";
echo "</p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h2 { color: #666; margin-top: 30px; }
h3 { color: #333; }
table { margin: 20px 0; }
th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
th { background: #f8f9fa; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
