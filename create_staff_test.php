<?php
// Script để tạo tài khoản nhân viên test và kiểm tra giao diện nhân viên
session_start();
require_once 'includes/db_connect.php';

echo "<h1>👨‍💼 Tạo và Test Tài khoản Nhân viên</h1>";

// Tạo tài khoản nhân viên test
if (isset($_POST['create_staff'])) {
    try {
        $conn = connectOracle();
        
        $staff_data = [
            'MaND' => 'NV001',
            'TenDangNhap' => 'nhanvien01',
            'MatKhau' => 'nv123',
            'HoTen' => 'Nguyễn Văn Nhân Viên',
            'VaiTro' => 'nhanvien'
        ];
        
        // Kiểm tra xem đã tồn tại chưa
        $check_sql = "SELECT COUNT(*) as count FROM NguoiDung WHERE TenDangNhap = :username";
        $check_stmt = oci_parse($conn, $check_sql);
        oci_bind_by_name($check_stmt, ":username", $staff_data['TenDangNhap']);
        oci_execute($check_stmt);
        $result = oci_fetch_assoc($check_stmt);
        
        if ($result['COUNT'] > 0) {
            echo "<p style='color: orange;'>⚠️ Tài khoản nhân viên đã tồn tại!</p>";
        } else {
            $insert_sql = "INSERT INTO NguoiDung (MaND, TenDangNhap, MatKhau, HoTen, VaiTro) 
                           VALUES (:mand, :tendn, :matkhau, :hoten, :vaitro)";
            
            $insert_stmt = oci_parse($conn, $insert_sql);
            oci_bind_by_name($insert_stmt, ":mand", $staff_data['MaND']);
            oci_bind_by_name($insert_stmt, ":tendn", $staff_data['TenDangNhap']);
            oci_bind_by_name($insert_stmt, ":matkhau", $staff_data['MatKhau']);
            oci_bind_by_name($insert_stmt, ":hoten", $staff_data['HoTen']);
            oci_bind_by_name($insert_stmt, ":vaitro", $staff_data['VaiTro']);
            
            if (oci_execute($insert_stmt)) {
                echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
                echo "<p>✅ <strong>Tạo tài khoản nhân viên thành công!</strong></p>";
                echo "<h3>🔑 Thông tin đăng nhập:</h3>";
                echo "<p><strong>Tên đăng nhập:</strong> nhanvien01</p>";
                echo "<p><strong>Mật khẩu:</strong> nv123</p>";
                echo "<p><strong>Vai trò:</strong> Nhân viên</p>";
                echo "</div>";
            } else {
                $error = oci_error($insert_stmt);
                echo "<p style='color: red;'>❌ Lỗi tạo nhân viên: " . $error['message'] . "</p>";
            }
        }
        
        oci_close($conn);
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
    }
}

// Test đăng nhập nhân viên
if (isset($_POST['test_staff_login'])) {
    require_once 'includes/functions.php';
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "<h3>🔍 Test đăng nhập nhân viên:</h3>";
    
    $user = loginUser($username, $password);
    
    if ($user && $user['VAITRO'] === 'nhanvien') {
        $_SESSION['MaND'] = $user['MAND'];
        $_SESSION['VaiTro'] = $user['VAITRO'];
        $_SESSION['hoTen'] = $user['HOTEN'];
        
        echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<p>✅ <strong>Đăng nhập nhân viên thành công!</strong></p>";
        echo "<p><strong>Mã NV:</strong> " . $user['MAND'] . "</p>";
        echo "<p><strong>Họ tên:</strong> " . $user['HOTEN'] . "</p>";
        echo "<p><strong>Vai trò:</strong> " . $user['VAITRO'] . "</p>";
        echo "<p>🎫 <a href='staff/dashboard.php' style='color: #007bff; font-weight: bold;'>Truy cập Staff Dashboard</a></p>";
        echo "</div>";
    } else {
        echo "<p style='color: red;'>❌ Đăng nhập thất bại hoặc không phải nhân viên</p>";
    }
}

// Hiển thị danh sách nhân viên hiện có
echo "<h2>📋 Danh sách nhân viên hiện có</h2>";

try {
    $conn = connectOracle();
    $sql = "SELECT MaND, TenDangNhap, HoTen FROM NguoiDung WHERE VaiTro = 'nhanvien' ORDER BY MaND";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='padding: 10px;'>Mã NV</th>";
    echo "<th style='padding: 10px;'>Tên đăng nhập</th>";
    echo "<th style='padding: 10px;'>Họ tên</th>";
    echo "<th style='padding: 10px;'>Thao tác</th>";
    echo "</tr>";
    
    $count = 0;
    while ($staff = oci_fetch_assoc($stmt)) {
        $count++;
        echo "<tr>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($staff['MAND']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($staff['TENDANGNHAP']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($staff['HOTEN']) . "</td>";
        echo "<td style='padding: 10px;'>";
        echo "<form method='POST' style='display: inline;'>";
        echo "<input type='hidden' name='username' value='" . $staff['TENDANGNHAP'] . "'>";
        echo "<input type='hidden' name='password' value='nv123'>";
        echo "<button type='submit' name='test_staff_login' style='background: #28a745; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;'>🔑 Test Login</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    
    if ($count == 0) {
        echo "<tr><td colspan='4' style='padding: 20px; text-align: center; color: #666;'>Chưa có nhân viên nào</td></tr>";
    }
    
    echo "</table>";
    
    oci_close($conn);
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

// Form tạo nhân viên
echo "<h2>➕ Tạo tài khoản nhân viên test</h2>";
echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<p>Tạo tài khoản nhân viên với thông tin mặc định:</p>";
echo "<ul>";
echo "<li><strong>Tên đăng nhập:</strong> nhanvien01</li>";
echo "<li><strong>Mật khẩu:</strong> nv123</li>";
echo "<li><strong>Họ tên:</strong> Nguyễn Văn Nhân Viên</li>";
echo "</ul>";
echo "<button type='submit' name='create_staff' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>👨‍💼 Tạo nhân viên</button>";
echo "</form>";

// Form test đăng nhập thủ công
echo "<h2>🧪 Test đăng nhập nhân viên thủ công</h2>";
echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Tên đăng nhập:</label><br>";
echo "<input type='text' name='username' value='nhanvien01' style='width: 200px; padding: 8px; margin: 5px 0;'>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Mật khẩu:</label><br>";
echo "<input type='password' name='password' value='nv123' style='width: 200px; padding: 8px; margin: 5px 0;'>";
echo "</div>";
echo "<button type='submit' name='test_staff_login' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>🔍 Test đăng nhập</button>";
echo "</form>";

// Kiểm tra quyền truy cập
echo "<h2>🔐 Kiểm tra quyền truy cập nhân viên</h2>";

if (isset($_SESSION['VaiTro']) && $_SESSION['VaiTro'] === 'nhanvien') {
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<p>✅ <strong>Đang đăng nhập với quyền nhân viên</strong></p>";
    echo "<p><strong>Họ tên:</strong> " . ($_SESSION['hoTen'] ?? 'N/A') . "</p>";
    echo "<h3>📋 Các trang có thể truy cập:</h3>";
    echo "<ul>";
    echo "<li>✅ <a href='staff/dashboard.php'>Staff Dashboard</a></li>";
    echo "<li>✅ <a href='staff/ticket_checker.php'>Kiểm tra vé</a></li>";
    echo "</ul>";
    echo "<h3>🚫 Các trang KHÔNG thể truy cập:</h3>";
    echo "<ul>";
    echo "<li>❌ <a href='admin/dashboard.php'>Admin Dashboard</a> (sẽ bị chặn)</li>";
    echo "<li>❌ <a href='admin/manage_staff.php'>Quản lý nhân viên</a> (sẽ bị chặn)</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<p>❌ <strong>Chưa đăng nhập với quyền nhân viên</strong></p>";
    echo "<p>Hãy tạo và đăng nhập tài khoản nhân viên để test</p>";
    echo "</div>";
}

// Hướng dẫn
echo "<h2>📖 Hướng dẫn test giao diện nhân viên</h2>";
echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>🔧 Các bước test:</h3>";
echo "<ol>";
echo "<li>Tạo tài khoản nhân viên bằng nút ở trên</li>";
echo "<li>Test đăng nhập với form test</li>";
echo "<li>Truy cập Staff Dashboard để xem giao diện</li>";
echo "<li>Test chức năng kiểm tra vé</li>";
echo "<li>Thử truy cập trang admin (sẽ bị chặn)</li>";
echo "</ol>";

echo "<h3>🎯 Đặc điểm giao diện nhân viên:</h3>";
echo "<ul>";
echo "<li>🎫 Dashboard chuyên biệt cho nhân viên</li>";
echo "<li>📊 Thống kê suất chiếu hôm nay</li>";
echo "<li>🎬 Danh sách phim đang chiếu</li>";
echo "<li>🔍 Chức năng kiểm tra vé</li>";
echo "<li>🚫 Không có quyền quản lý hệ thống</li>";
echo "</ul>";
echo "</div>";

echo "<p style='text-align: center; margin: 30px 0;'>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🔑 Đăng nhập chính thức</a>";
echo "<a href='debug_login.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🔍 Debug Login</a>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🏠 Trang chủ</a>";
echo "</p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h1 { color: #333; border-bottom: 3px solid #28a745; padding-bottom: 10px; }
h2 { color: #666; margin-top: 30px; }
h3 { color: #333; }
table { margin: 20px 0; }
th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
th { background: #f8f9fa; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
