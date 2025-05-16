<?php
// Lớp AffiliatePartner: đại diện cho một cộng tác viên cơ bản
class CongTacVien {
    private string $ten;
    private string $email;
    private float $tyLeHoaHong;
    private bool $hoatDong;
    public const TEN_NEN_TANG = "VietLink Affiliate";

    public function __construct(string $ten, string $email, float $tyLeHoaHong, bool $hoatDong = true) {
        $this->ten = $ten;
        $this->email = $email;
        $this->tyLeHoaHong = $tyLeHoaHong;
        $this->hoatDong = $hoatDong;
    }

    public function __destruct() {
        echo "Đối tượng cộng tác viên '{$this->ten}' đang được hủy.<br>";
    }

    public function tinhHoaHong(float $giaTriDonHang): float {
        return $giaTriDonHang * ($this->tyLeHoaHong / 100);
    }

    public function layThongTin(): string {
        return sprintf(
            "Cộng tác viên: %s, Email: %s, Tỷ lệ hoa hồng: %.2f%%, Hoạt động: %s, Nền tảng: %s",
            $this->ten,
            $this->email,
            $this->tyLeHoaHong,
            $this->hoatDong ? 'Có' : 'Không',
            self::TEN_NEN_TANG
        );
    }

    public function dangHoatDong(): bool {
        return $this->hoatDong;
    }

    public function layTen(): string {
        return $this->ten;
    }
}

// Lớp CongTacVienCaoCap: mở rộng từ CongTacVien với thêm tiền thưởng mỗi đơn hàng
class CongTacVienCaoCap extends CongTacVien {
    private float $thuongMoiDonHang;

    public function __construct(string $ten, string $email, float $tyLeHoaHong, float $thuongMoiDonHang, bool $hoatDong = true) {
        parent::__construct($ten, $email, $tyLeHoaHong, $hoatDong);
        $this->thuongMoiDonHang = $thuongMoiDonHang;
    }

    public function tinhHoaHong(float $giaTriDonHang): float {
        $hoaHongCoBan = parent::tinhHoaHong($giaTriDonHang);
        return $hoaHongCoBan + $this->thuongMoiDonHang;
    }
}

// Lớp QuanLyCongTacVien: quản lý danh sách cộng tác viên
class QuanLyCongTacVien {
    private array $danhSachCongTacVien = [];

    public function themCongTacVien(CongTacVien $congTacVien): void {
        $this->danhSachCongTacVien[] = $congTacVien;
    }

    public function hienThiDanhSach(): void {
        echo "<h2>Danh sách cộng tác viên</h2>";
        echo "<ul>";
        foreach ($this->danhSachCongTacVien as $congTacVien) {
            echo "<li>" . htmlspecialchars($congTacVien->layThongTin()) . "</li>";
        }
        echo "</ul>";
    }

    public function tongHoaHong(float $giaTriDonHang): float {
        $tong = 0;
        foreach ($this->danhSachCongTacVien as $congTacVien) {
            if ($congTacVien->dangHoatDong()) {
                $tong += $congTacVien->tinhHoaHong($giaTriDonHang);
            }
        }
        return $tong;
    }

    public function hienThiHoaHong(float $giaTriDonHang): void {
        echo "<h2>Hoa hồng từng cộng tác viên cho đơn hàng trị giá " . number_format($giaTriDonHang) . " VNĐ</h2>";
        echo "<ul>";
        foreach ($this->danhSachCongTacVien as $congTacVien) {
            if ($congTacVien->dangHoatDong()) {
                $hoaHong = $congTacVien->tinhHoaHong($giaTriDonHang);
                echo "<li>" . htmlspecialchars($congTacVien->layTen()) . ": " . number_format($hoaHong) . " VNĐ</li>";
            }
        }
        echo "</ul>";
    }
}

// Ví dụ sử dụng
$giaTriDonHang = 2000000; // 2,000,000 VNĐ

$quanLy = new QuanLyCongTacVien();

// Tạo hai cộng tác viên cơ bản
$congTacVien1 = new CongTacVien("Nguyen Van A", "a@example.com", 5.0);
$congTacVien2 = new CongTacVien("Tran Thi B", "b@example.com", 7.5);

// Tạo một cộng tác viên cao cấp
$congTacVienCaoCap = new CongTacVienCaoCap("Le Van C", "c@example.com", 6.0, 50000);

// Thêm cộng tác viên vào danh sách quản lý
$quanLy->themCongTacVien($congTacVien1);
$quanLy->themCongTacVien($congTacVien2);
$quanLy->themCongTacVien($congTacVienCaoCap);

// Hiển thị danh sách cộng tác viên
$quanLy->hienThiDanhSach();

// Hiển thị hoa hồng từng cộng tác viên
$quanLy->hienThiHoaHong($giaTriDonHang);

// Hiển thị tổng hoa hồng
echo "<h2>Tổng hoa hồng hệ thống cần chi trả: " . number_format($quanLy->tongHoaHong($giaTriDonHang)) . " VNĐ</h2>";

// Khi kết thúc script, các destructor sẽ hiển thị thông báo hủy đối tượng
?>
