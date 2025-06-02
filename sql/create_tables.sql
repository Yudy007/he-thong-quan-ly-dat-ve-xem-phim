-- Bảng Người Dùng (NguoiDung)
CREATE TABLE NguoiDung (
    MaND VARCHAR2(10) PRIMARY KEY,
    TenDangNhap VARCHAR2(50) UNIQUE NOT NULL,
    MatKhau VARCHAR2(255) NOT NULL,
    HoTen NVARCHAR2(100) NOT NULL,
    Email VARCHAR2(100),
    SoDienThoai VARCHAR2(20),
    VaiTro VARCHAR2(20) DEFAULT 'khachhang' CHECK (VaiTro IN ('admin', 'nhanvien', 'khachhang'))
);

-- Bảng Phim
CREATE TABLE Phim (
    MaPhim VARCHAR2(10) PRIMARY KEY,
    TenPhim NVARCHAR2(150) NOT NULL,
    TheLoai NVARCHAR2(50),
    ThoiLuong NUMBER NOT NULL, -- phút
    MoTa CLOB,
    Poster VARCHAR2(255),
    TrangThai VARCHAR2(20) DEFAULT 'dang_chieu' CHECK (TrangThai IN ('dang_chieu', 'sap_chieu', 'ngung_chieu'))
);

-- Bảng Phòng Chiếu
CREATE TABLE PhongChieu (
    MaPhong VARCHAR2(10) PRIMARY KEY,
    TenPhong NVARCHAR2(50) NOT NULL,
    SoLuongGhe NUMBER NOT NULL
);

-- Bảng Ghế
CREATE TABLE Ghe (
    MaGhe VARCHAR2(10) PRIMARY KEY,
    MaPhong VARCHAR2(10) NOT NULL REFERENCES PhongChieu(MaPhong),
    Hang VARCHAR2(5) NOT NULL,
    So NUMBER NOT NULL,
    LoaiGhe VARCHAR2(10) DEFAULT 'thuong' CHECK (LoaiGhe IN ('thuong', 'vip')),
    TrangThai VARCHAR2(10) DEFAULT 'tot' CHECK (TrangThai IN ('tot', 'hong'))
);

-- Bảng Suất Chiếu
CREATE TABLE SuatChieu (
    MaSuat VARCHAR2(10) PRIMARY KEY,
    MaPhim VARCHAR2(10) NOT NULL REFERENCES Phim(MaPhim),
    MaPhong VARCHAR2(10) NOT NULL REFERENCES PhongChieu(MaPhong),
    ThoiGianBatDau TIMESTAMP NOT NULL,
    ThoiGianKetThuc TIMESTAMP NOT NULL,
    GiaVe NUMBER NOT NULL
);

-- Bảng Vé
CREATE TABLE Ve (
    MaVe VARCHAR2(15) PRIMARY KEY,
    MaSuat VARCHAR2(10) NOT NULL REFERENCES SuatChieu(MaSuat),
    MaND VARCHAR2(10) NOT NULL REFERENCES NguoiDung(MaND),
    MaGhe VARCHAR2(10) NOT NULL REFERENCES Ghe(MaGhe),
    TrangThai VARCHAR2(20) DEFAULT 'da_dat' CHECK (TrangThai IN ('da_dat', 'da_kiem_tra', 'huy')),
    ThoiGianDat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);