-- ================================================
-- 1. Truy vấn cơ bản: Liệt kê toàn bộ phim trong hệ thống
-- ================================================
SELECT * FROM Phim;

-- ================================================
-- 2. Truy vấn cơ bản có điều kiện: Phim đang chiếu
-- ================================================
SELECT MaPhim, TenPhim, TrangThai
FROM Phim
WHERE TrangThai = 'dang_chieu';

-- ================================================
-- 3. JOIN: Lấy vé của 1 người dùng (đầy đủ tên phim, giờ chiếu, ghế)
-- ================================================
SELECT Ve.MaVe, Phim.TenPhim, SuatChieu.ThoiGianBatDau, Ghe.SoGhe
FROM Ve
JOIN SuatChieu ON Ve.MaSuat = SuatChieu.MaSuat
JOIN Phim ON SuatChieu.MaPhim = Phim.MaPhim
JOIN Ghe ON Ve.MaGhe = Ghe.MaGhe
WHERE Ve.MaNguoiDung = 'ND03';

-- ================================================
-- 4. JOIN: Danh sách ghế đã đặt của 1 suất chiếu cụ thể
-- ================================================
SELECT Ghe.SoGhe, Ve.MaVe, Ve.TrangThai
FROM Ve
JOIN Ghe ON Ve.MaGhe = Ghe.MaGhe
WHERE Ve.MaSuat = 'SC01';

-- ================================================
-- 5. GROUP BY: Thống kê số vé đã bán theo từng phim
-- ================================================
SELECT Phim.TenPhim, COUNT(*) AS SoLuongVe
FROM Ve
JOIN SuatChieu ON Ve.MaSuat = SuatChieu.MaSuat
JOIN Phim ON SuatChieu.MaPhim = Phim.MaPhim
GROUP BY Phim.TenPhim;

-- ================================================
-- 6. GROUP BY + HAVING: Phim có hơn 1 suất chiếu trong hệ thống
-- ================================================
SELECT MaPhim, COUNT(*) AS SoLuongSuat
FROM SuatChieu
GROUP BY MaPhim
HAVING COUNT(*) > 1;

-- ================================================
-- 7. SUBQUERY: Tìm phim có doanh thu cao nhất
-- ================================================
SELECT TenPhim
FROM Phim
WHERE MaPhim = (
    SELECT MaPhim
    FROM (
        SELECT SuatChieu.MaPhim, SUM(SuatChieu.GiaVe) AS DoanhThu
        FROM Ve
        JOIN SuatChieu ON Ve.MaSuat = SuatChieu.MaSuat
        GROUP BY SuatChieu.MaPhim
        ORDER BY DoanhThu DESC
    )
    WHERE ROWNUM = 1
);

-- ================================================
-- 8. SUBQUERY: Khách hàng đặt nhiều vé nhất
-- ================================================
SELECT HoTen
FROM NguoiDung
WHERE MaND = (
    SELECT MaNguoiDung
    FROM (
        SELECT MaNguoiDung, COUNT(*) AS SoVe
        FROM Ve
        GROUP BY MaNguoiDung
        ORDER BY SoVe DESC
    )
    WHERE ROWNUM = 1
);

-- ================================================
-- 9. VIEW: Tạo view tổng hợp thông tin vé
-- ================================================
CREATE OR REPLACE VIEW VIEW_ThongTinVe AS
SELECT Ve.MaVe, NguoiDung.HoTen, Phim.TenPhim, SuatChieu.ThoiGianBatDau, Ghe.SoGhe
FROM Ve
JOIN NguoiDung ON Ve.MaNguoiDung = NguoiDung.MaND
JOIN SuatChieu ON Ve.MaSuat = SuatChieu.MaSuat
JOIN Phim ON SuatChieu.MaPhim = Phim.MaPhim
JOIN Ghe ON Ve.MaGhe = Ghe.MaGhe;

-- Xem dữ liệu từ view
-- SELECT * FROM VIEW_ThongTinVe;

-- ================================================
-- 10. FUNCTION: Tính tổng doanh thu của 1 suất chiếu
-- ================================================
CREATE OR REPLACE FUNCTION TinhDoanhThu_Suat (
    p_MaSuat VARCHAR2
) RETURN NUMBER IS
    v_tong NUMBER := 0;
BEGIN
    SELECT COUNT(*) * GiaVe
    INTO v_tong
    FROM Ve
    JOIN SuatChieu ON Ve.MaSuat = SuatChieu.MaSuat
    WHERE Ve.MaSuat = p_MaSuat AND Ve.TrangThai IN ('da_dat', 'da_kiem_tra');

    RETURN v_tong;
EXCEPTION
    WHEN OTHERS THEN
        RETURN -1;
END;
/
