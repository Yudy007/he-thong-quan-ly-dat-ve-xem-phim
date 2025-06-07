# 🎬 Hệ thống Đặt vé Xem phim - Phân quyền 3 cấp

## 📋 Tổng quan

Hệ thống đặt vé xem phim với 3 loại tài khoản có phân quyền rõ ràng:
- **👑 Admin**: Toàn quyền quản lý hệ thống
- **👨‍💼 Nhân viên**: Kiểm tra và xác nhận vé
- **🎬 Khách hàng**: Đặt vé và quản lý vé cá nhân

## 🔐 Phân quyền chi tiết

### 👑 Admin
- ✅ Đăng nhập hệ thống
- ✅ Quản lý phim (thêm, sửa, xóa)
- ✅ Quản lý suất chiếu
- ✅ Quản lý phòng chiếu và ghế
- ✅ Quản lý tất cả người dùng
- ✅ **Tạo và xóa tài khoản nhân viên**
- ✅ Xem thống kê và báo cáo
- ❌ Không thể tự tạo tài khoản (phải được tạo trực tiếp trong DB)

### 👨‍💼 Nhân viên
- ✅ Đăng nhập hệ thống
- ✅ Kiểm tra vé của khách hàng
- ✅ Xác nhận vé (đánh dấu đã kiểm tra)
- ❌ **Không thể tạo hoặc xóa tài khoản**
- ❌ Không thể quản lý phim, suất chiếu
- ❌ Không thể xem thống kê tổng thể

### 🎬 Khách hàng
- ✅ **Tự đăng ký tài khoản**
- ✅ Đăng nhập hệ thống
- ✅ Đặt vé xem phim
- ✅ Xem danh sách vé đã đặt
- ✅ Xem thông tin phim và suất chiếu
- ❌ Không thể truy cập khu vực quản trị

## 📁 Cấu trúc thư mục

```
📦 abc/
├── 📁 admin/                    # Khu vực Admin
│   ├── dashboard.php           # Dashboard admin
│   ├── manage_movies.php       # Quản lý phim
│   ├── manage_schedules.php    # Quản lý suất chiếu
│   ├── manage_rooms.php        # Quản lý phòng & ghế (MỚI)
│   ├── manage_users.php        # Quản lý người dùng
│   ├── manage_staff.php        # Quản lý nhân viên (MỚI)
│   └── reports.php            # Thống kê
├── 📁 staff/                   # Khu vực Nhân viên
│   ├── dashboard.php          # Dashboard nhân viên
│   └── ticket_checker.php     # Kiểm tra vé
├── 📁 customer/               # Khu vực Khách hàng
│   ├── home.php              # Trang chủ khách hàng
│   ├── booking.php           # Đặt vé
│   ├── movie_detail.php      # Chi tiết phim
│   └── my_tickets.php        # Vé đã đặt
├── 📁 includes/              # Thư viện chung
│   ├── auth.php             # Xác thực & phân quyền
│   ├── functions.php        # Các hàm chính (CẬP NHẬT)
│   ├── db_connect.php       # Kết nối database
│   ├── header.php           # Header (CẬP NHẬT)
│   └── footer.php           # Footer
├── 📁 assets/               # Tài nguyên
│   ├── css/style.css        # CSS chính
│   ├── js/                  # JavaScript
│   └── images/              # Hình ảnh
├── index.php               # Trang chủ (CẬP NHẬT)
├── login.php              # Đăng nhập
├── register.php           # Đăng ký (CẬP NHẬT)
├── logout.php             # Đăng xuất
├── test_system.php        # Test hệ thống (MỚI)
└── README.md              # Hướng dẫn (MỚI)
```

## 🚀 Cài đặt và sử dụng

### 1. Chuẩn bị Database
```sql
-- Tạo tài khoản admin đầu tiên
INSERT INTO NguoiDung (MaND, TenDangNhap, MatKhau, HoTen, VaiTro) 
VALUES ('ADMIN001', 'admin', 'admin123', 'Quản trị viên', 'admin');
```

### 2. Truy cập hệ thống
1. Mở trình duyệt và truy cập: `http://localhost/abc/`
2. Đăng nhập với tài khoản admin vừa tạo
3. Bắt đầu cấu hình hệ thống

### 3. Cấu hình ban đầu (Admin)
1. **Tạo phòng chiếu**: Admin → Quản lý phòng & ghế
2. **Thêm ghế**: Trong từng phòng chiếu
3. **Thêm phim**: Admin → Quản lý phim
4. **Tạo suất chiếu**: Admin → Quản lý suất chiếu
5. **Tạo tài khoản nhân viên**: Admin → Quản lý nhân viên

### 4. Sử dụng hàng ngày
- **Khách hàng**: Tự đăng ký → Đặt vé
- **Nhân viên**: Kiểm tra vé tại quầy
- **Admin**: Theo dõi thống kê và quản lý

## 🔧 Các tính năng mới đã thêm

### ✨ Cho Admin:
- 🏢 **Quản lý phòng chiếu và ghế**
- 👨‍💼 **Tạo/xóa tài khoản nhân viên**
- 📊 **Dashboard với thống kê chi tiết**
- 🎨 **Giao diện đẹp với biểu tượng**

### ✨ Cho Nhân viên:
- 🎫 **Dashboard tối ưu cho công việc**
- 📊 **Thống kê suất chiếu hôm nay**
- 🚫 **Hạn chế quyền truy cập**

### ✨ Cho Khách hàng:
- 🎬 **Giao diện thân thiện**
- 📊 **Thống kê vé cá nhân**
- 🎨 **Hiển thị phim đẹp mắt**

## 🛡️ Bảo mật

- ✅ Phân quyền nghiêm ngặt theo vai trò
- ✅ Kiểm tra session trước mỗi trang
- ✅ Chuyển hướng tự động theo vai trò
- ✅ Ngăn chặn truy cập trái phép
- ✅ Mã hóa mật khẩu

## 🧪 Test hệ thống

Truy cập `test_system.php` để kiểm tra:
- Kết nối database
- Các hàm chính
- Phân quyền
- Cấu trúc file

## 📞 Hỗ trợ

Nếu gặp vấn đề:
1. Kiểm tra kết nối database Oracle
2. Đảm bảo các bảng đã được tạo
3. Chạy `test_system.php` để chẩn đoán
4. Kiểm tra file log lỗi của web server

## 🎯 Lưu ý quan trọng

- **Admin** là vai trò duy nhất có thể tạo nhân viên
- **Nhân viên** không thể tự tạo tài khoản
- **Khách hàng** chỉ có thể tự đăng ký
- Mỗi vai trò có giao diện và menu riêng biệt
- Hệ thống tự động chuyển hướng theo vai trò sau đăng nhập

---
*Hệ thống được thiết kế để đảm bảo bảo mật và phân quyền rõ ràng cho rạp chiếu phim.*
