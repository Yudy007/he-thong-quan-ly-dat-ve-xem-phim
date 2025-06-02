-- Tạo bảng người dùng
CREATE TABLE NguoiDung (
    MaND VARCHAR2(10) PRIMARY KEY,
    TenDangNhap VARCHAR2(30) UNIQUE NOT NULL,
    MatKhau VARCHAR2(100) NOT NULL,
    HoTen VARCHAR2(50) NOT NULL,
    VaiTro VARCHAR2(20) CHECK (VaiTro IN ('admin', 'nhanvien', 'khachhang'))
);

-- Bảng phim
CREATE TABLE Phim (
    MaPhim VARCHAR2(10) PRIMARY KEY,
    TenPhim VARCHAR2(100) NOT NULL,
    TheLoai VARCHAR2(50),
    ThoiLuong NUMBER(3) CHECK (ThoiLuong > 0),
    MoTa CLOB,
    TrangThai VARCHAR2(20) CHECK (TrangThai IN ('dang_chieu', 'sap_chieu', 'ngung_chieu'))
);

-- Bảng phòng chiếu
CREATE TABLE PhongChieu (
    MaPhong VARCHAR2(10) PRIMARY KEY,
    TenPhong VARCHAR2(30) UNIQUE NOT NULL,
    SoLuongGhe NUMBER(3) CHECK (SoLuongGhe > 0)
);

-- Bảng ghế
CREATE TABLE Ghe (
    MaGhe VARCHAR2(10) PRIMARY KEY,
    MaPhong VARCHAR2(10),
    SoGhe VARCHAR2(5) NOT NULL,
    LoaiGhe VARCHAR2(20) CHECK (LoaiGhe IN ('thuong', 'vip')),
    FOREIGN KEY (MaPhong) REFERENCES PhongChieu(MaPhong)
);

-- Bảng suất chiếu
CREATE TABLE SuatChieu (
    MaSuat VARCHAR2(10) PRIMARY KEY,
    MaPhim VARCHAR2(10),
    MaPhong VARCHAR2(10),
    ThoiGianBatDau DATE NOT NULL,
    ThoiGianKetThuc DATE NOT NULL,
    GiaVe NUMBER(8,0) CHECK (GiaVe > 0),
    FOREIGN KEY (MaPhim) REFERENCES Phim(MaPhim),
    FOREIGN KEY (MaPhong) REFERENCES PhongChieu(MaPhong)
);

-- Bảng vé
CREATE TABLE Ve (
    MaVe VARCHAR2(10) PRIMARY KEY,
    MaSuat VARCHAR2(10),
    MaGhe VARCHAR2(10),
    MaNguoiDung VARCHAR2(10),
    ThoiGianDat DATE DEFAULT SYSDATE,
    TrangThai VARCHAR2(20) DEFAULT 'da_dat' CHECK (TrangThai IN ('da_dat', 'da_kiem_tra', 'huy')),
    FOREIGN KEY (MaSuat) REFERENCES SuatChieu(MaSuat),
    FOREIGN KEY (MaGhe) REFERENCES Ghe(MaGhe),
    FOREIGN KEY (MaNguoiDung) REFERENCES NguoiDung(MaND)
);