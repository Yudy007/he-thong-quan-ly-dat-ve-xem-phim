-- Dữ liệu cho NguoiDung
INSERT INTO NguoiDung VALUES ('ND01', 'admin', 'admin123', 'Nguyễn Văn A', 'admin');
INSERT INTO NguoiDung VALUES ('ND02', 'staff1', 'staff123', 'Trần Văn B', 'nhanvien');
INSERT INTO NguoiDung VALUES ('ND03', 'user1', 'user123', 'Lê Thị C', 'khachhang');

-- Dữ liệu cho Phim
INSERT INTO Phim VALUES ('P01', 'Avengers: Endgame', 'Hành động', 181, 'Phim siêu anh hùng', 'dang_chieu');
INSERT INTO Phim VALUES ('P02', 'Frozen II', 'Hoạt hình', 103, 'Câu chuyện băng giá tiếp tục', 'sap_chieu');

-- Dữ liệu cho PhongChieu
INSERT INTO PhongChieu VALUES ('PC01', 'Phòng 1', 100);
INSERT INTO PhongChieu VALUES ('PC02', 'Phòng 2', 80);

-- Dữ liệu cho Ghe (vài ghế mẫu)
INSERT INTO Ghe VALUES ('G01', 'PC01', 'A1', 'thuong');
INSERT INTO Ghe VALUES ('G02', 'PC01', 'A2', 'vip');
INSERT INTO Ghe VALUES ('G03', 'PC02', 'B1', 'thuong');

-- Dữ liệu cho SuatChieu
INSERT INTO SuatChieu VALUES ('SC01', 'P01', 'PC01', TO_DATE('2025-06-03 18:00', 'YYYY-MM-DD HH24:MI'), TO_DATE('2025-06-03 21:00', 'YYYY-MM-DD HH24:MI'), 80000);
INSERT INTO SuatChieu VALUES ('SC02', 'P02', 'PC02', TO_DATE('2025-06-04 14:00', 'YYYY-MM-DD HH24:MI'), TO_DATE('2025-06-04 16:00', 'YYYY-MM-DD HH24:MI'), 70000);

-- Dữ liệu cho Ve
INSERT INTO Ve VALUES ('V01', 'SC01', 'G01', 'ND03', SYSDATE, 'da_dat');
INSERT INTO Ve VALUES ('V02', 'SC01', 'G02', 'ND03', SYSDATE, 'da_dat');
