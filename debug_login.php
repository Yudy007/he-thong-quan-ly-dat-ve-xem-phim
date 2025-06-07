<?php
// File debug để kiểm tra quá trình đăng nhập
session_start();
require_once 'includes/functions.php';

echo "<h1>🔍 Debug Đăng nhập</h1>";

// Kiểm tra session hiện tại
echo "<h2>1. Session hiện tại</h2>";
if (isset($_SESSION['MaND'])) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<p>✅ <strong>Đã đăng nhập:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Mã ND:</strong> " . ($_SESSION['MaND'] ?? 'Không có') . "</li>";
    echo "<li><strong>Vai trò:</strong> " . ($_SESSION['VaiTro'] ?? 'Không có') . "</li>";
    echo "<li><strong>Họ tên:</strong> " . ($_SESSION['hoTen'] ?? 'Không có') . "</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<p>❌ <strong>Chưa đăng nhập</strong></p>";
    echo "</div>";
}

// Test đăng nhập
echo "<h2>2. Test đăng nhập</h2>";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['test_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "<h3>🔍 Chi tiết quá trình đăng nhập:</h3>";
    echo "<p><strong>Username:</strong> " . htmlspecialchars($username) . "</p>";
    echo "<p><strong>Password:</strong> " . htmlspecialchars($password) . "</p>";
    
    try {
        // Test kết nối database
        $conn = connectOracle();
        echo "<p>✅ Kết nối database thành công</p>";
        
        // Test query
        $sql = "SELECT * FROM NguoiDung WHERE TenDangNhap = :username AND MatKhau = :password";
        echo "<p><strong>SQL Query:</strong> " . $sql . "</p>";
        
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":username", $username);
        oci_bind_by_name($stmt, ":password", $password);
        
        if (oci_execute($stmt)) {
            echo "<p>✅ Query thực thi thành công</p>";
            
            $result = oci_fetch_assoc($stmt);
            
            if ($result) {
                echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                echo "<p>✅ <strong>Tìm thấy người dùng:</strong></p>";
                echo "<pre>" . print_r($result, true) . "</pre>";
                
                // Lưu session
                $_SESSION['MaND'] = $result['MAND'];
                $_SESSION['VaiTro'] = $result['VAITRO'];
                $_SESSION['hoTen'] = $result['HOTEN'];
                
                echo "<p>✅ Đã lưu session</p>";
                echo "</div>";
                
                // Test chuyển hướng
                echo "<h3>🔄 Test chuyển hướng:</h3>";
                switch ($result['VAITRO']) {
                    case 'admin':
                        echo "<p>👑 Admin → <a href='admin/dashboard.php'>admin/dashboard.php</a></p>";
                        break;
                    case 'nhanvien':
                        echo "<p>👨‍💼 Nhân viên → <a href='staff/dashboard.php'>staff/dashboard.php</a></p>";
                        break;
                    case 'khachhang':
                        echo "<p>🎬 Khách hàng → <a href='customer/home.php'>customer/home.php</a></p>";
                        break;
                }
                
            } else {
                echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                echo "<p>❌ <strong>Không tìm thấy người dùng với thông tin này</strong></p>";
                echo "</div>";
            }
        } else {
            $error = oci_error($stmt);
            echo "<p>❌ Lỗi thực thi query: " . $error['message'] . "</p>";
        }
        
        oci_close($conn);
        
    } catch (Exception $e) {
        echo "<p>❌ Lỗi: " . $e->getMessage() . "</p>";
    }
}

// Form test đăng nhập
echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>🧪 Test đăng nhập:</h3>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Tên đăng nhập:</label><br>";
echo "<input type='text' name='username' value='admin' style='width: 200px; padding: 8px; margin: 5px 0;'>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label>Mật khẩu:</label><br>";
echo "<input type='password' name='password' value='admin123' style='width: 200px; padding: 8px; margin: 5px 0;'>";
echo "</div>";
echo "<button type='submit' name='test_login' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>🔍 Test đăng nhập</button>";
echo "</form>";

// Kiểm tra database
echo "<h2>3. Kiểm tra Database</h2>";

try {
    $conn = connectOracle();
    
    // Kiểm tra bảng NguoiDung
    $sql = "SELECT MaND, TenDangNhap, HoTen, VaiTro FROM NguoiDung ORDER BY VaiTro";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    
    echo "<h3>👥 Danh sách người dùng trong database:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='padding: 10px;'>Mã ND</th>";
    echo "<th style='padding: 10px;'>Tên đăng nhập</th>";
    echo "<th style='padding: 10px;'>Họ tên</th>";
    echo "<th style='padding: 10px;'>Vai trò</th>";
    echo "</tr>";
    
    $count = 0;
    while ($row = oci_fetch_assoc($stmt)) {
        $count++;
        echo "<tr>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($row['MAND']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($row['TENDANGNHAP']) . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($row['HOTEN']) . "</td>";
        echo "<td style='padding: 10px;'>";
        switch ($row['VAITRO']) {
            case 'admin':
                echo "👑 Admin";
                break;
            case 'nhanvien':
                echo "👨‍💼 Nhân viên";
                break;
            case 'khachhang':
                echo "🎬 Khách hàng";
                break;
            default:
                echo htmlspecialchars($row['VAITRO']);
        }
        echo "</td>";
        echo "</tr>";
    }
    
    if ($count == 0) {
        echo "<tr><td colspan='4' style='padding: 20px; text-align: center; color: #666;'>Không có người dùng nào trong database</td></tr>";
    }
    
    echo "</table>";
    echo "<p><strong>Tổng số người dùng:</strong> $count</p>";
    
    oci_close($conn);
    
} catch (Exception $e) {
    echo "<p>❌ Lỗi kiểm tra database: " . $e->getMessage() . "</p>";
}

// Kiểm tra file và quyền
echo "<h2>4. Kiểm tra File và Quyền</h2>";

$files_to_check = [
    'admin/dashboard.php' => 'Admin Dashboard',
    'staff/dashboard.php' => 'Staff Dashboard', 
    'customer/home.php' => 'Customer Home',
    'includes/auth.php' => 'Auth System',
    'includes/functions.php' => 'Functions'
];

foreach ($files_to_check as $file => $name) {
    if (file_exists($file)) {
        echo "<p>✅ $name ($file)</p>";
    } else {
        echo "<p>❌ $name ($file) - Không tồn tại</p>";
    }
}

// Hướng dẫn
echo "<h2>5. Hướng dẫn Debug</h2>";
echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>🔧 Nếu gặp lỗi đăng nhập:</h3>";
echo "<ol>";
echo "<li>Chạy <a href='create_admin.php'>create_admin.php</a> để tạo tài khoản admin</li>";
echo "<li>Kiểm tra kết nối database Oracle</li>";
echo "<li>Đảm bảo bảng NguoiDung đã được tạo</li>";
echo "<li>Kiểm tra tên cột trong database (phân biệt hoa/thường)</li>";
echo "<li>Test với form ở trên để debug chi tiết</li>";
echo "</ol>";
echo "</div>";

echo "<p style='text-align: center; margin: 30px 0;'>";
echo "<a href='create_admin.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🔧 Tạo Admin</a>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🔑 Đăng nhập</a>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🏠 Trang chủ</a>";
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
pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>
