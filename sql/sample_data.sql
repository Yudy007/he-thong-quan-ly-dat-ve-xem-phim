-- Dữ liệu cho NguoiDung
INSERT INTO NguoiDung (MaND, TenDangNhap, MatKhau, HoTen, VaiTro, Email, SDT)
VALUES ('ND01', 'admin', '123', 'admin', 'admin', 'admin@cinema.com', '0909123456');

INSERT INTO NguoiDung (MaND, TenDangNhap, MatKhau, HoTen, VaiTro, Email, SDT)
VALUES ('ND02', 'staff1', '123', 'staff', 'nhanvien', 'staff1@cinema.com', '0909876543');

INSERT INTO NguoiDung (MaND, TenDangNhap, MatKhau, HoTen, VaiTro, Email, SDT)
VALUES ('ND03', 'user1', '123', 'Khach hang', 'khachhang', 'user1@example.com', '0912345678');

-- Dữ liệu cho Phim
INSERT INTO Phim (MaPhim, TenPhim, TheLoai, ThoiLuong, MoTa, TrangThai)
VALUES ('P01', 'Avengers: Endgame', 'Hành động', 181, 'Phim siêu anh hùng hoành tráng từ Marvel Studios', 'dang_chieu');

INSERT INTO Phim (MaPhim, TenPhim, TheLoai, ThoiLuong, MoTa, TrangThai)
VALUES ('P02', 'Frozen II', 'Hoạt hình', 103, 'Tiếp tục câu chuyện băng giá cùng Elsa và Anna', 'sap_chieu');

-- Dữ liệu cho PhongChieu
INSERT INTO PhongChieu (MaPhong, TenPhong, SoLuongGhe)
VALUES ('PC01', 'Phòng 1', 100);

INSERT INTO PhongChieu (MaPhong, TenPhong, SoLuongGhe)
VALUES ('PC02', 'Phòng 2', 80);

-- Dữ liệu cho Ghe (vài ghế mẫu)
INSERT INTO Ghe (MaGhe, MaPhong, SoGhe, LoaiGhe)
VALUES ('G01', 'PC01', 'A1', 'thuong');

INSERT INTO Ghe (MaGhe, MaPhong, SoGhe, LoaiGhe)
VALUES ('G02', 'PC01', 'A2', 'vip');

INSERT INTO Ghe (MaGhe, MaPhong, SoGhe, LoaiGhe)
VALUES ('G03', 'PC02', 'B1', 'thuong');

-- Dữ liệu cho SuatChieu
INSERT INTO SuatChieu (MaSuat, MaPhim, MaPhong, ThoiGianBatDau, ThoiGianKetThuc, GiaVe)
VALUES ('SC01', 'P01', 'PC01', TO_DATE('2025-06-03 18:00', 'YYYY-MM-DD HH24:MI'), TO_DATE('2025-06-03 21:00', 'YYYY-MM-DD HH24:MI'), 80000);

INSERT INTO SuatChieu (MaSuat, MaPhim, MaPhong, ThoiGianBatDau, ThoiGianKetThuc, GiaVe)
VALUES ('SC02', 'P02', 'PC02', TO_DATE('2025-06-04 14:00', 'YYYY-MM-DD HH24:MI'), TO_DATE('2025-06-04 16:00', 'YYYY-MM-DD HH24:MI'), 70000);

-- Dữ liệu cho Ve
INSERT INTO Ve (MaVe, MaSuat, MaGhe, MaNguoiDung, ThoiGianDat, TrangThai)
VALUES ('V01', 'SC01', 'G01', 'ND03', SYSDATE, 'da_dat');

INSERT INTO Ve (MaVe, MaSuat, MaGhe, MaNguoiDung, ThoiGianDat, TrangThai)
VALUES ('V02', 'SC01', 'G02', 'ND03', SYSDATE, 'da_dat');
