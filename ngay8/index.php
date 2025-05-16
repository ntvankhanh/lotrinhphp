<?php
namespace XYZBank\Accounts;

use IteratorAggregate;
use ArrayIterator;

// Trait để ghi nhật ký giao dịch
trait GhiNhatKyGiaoDich {
    public function ghiGiaoDich(string $loai, float $soTien, float $soDuMoi): void {
        $ngay = date('Y-m-d H:i:s');
        echo "[{$ngay}] Giao dịch: {$loai} " . number_format($soTien, 0, ',', '.') . " VNĐ | Số dư mới: " . number_format($soDuMoi, 0, ',', '.') . " VNĐ<br>";
    }
}

// Lớp trừu tượng cơ bản cho tài khoản ngân hàng
abstract class TaiKhoanNganHang {
    protected string $soTaiKhoan;
    protected string $tenChuTaiKhoan;
    protected float $soDu;

    public function __construct(string $soTaiKhoan, string $tenChuTaiKhoan, float $soDu) {
        $this->soTaiKhoan = $soTaiKhoan;
        $this->tenChuTaiKhoan = $tenChuTaiKhoan;
        $this->soDu = $soDu;
        NganHang::tangSoTaiKhoan();
    }

    public function laySoDu(): float {
        return $this->soDu;
    }

    public function layTenChuTaiKhoan(): string {
        return $this->tenChuTaiKhoan;
    }

    public function laySoTaiKhoan(): string {
        return $this->soTaiKhoan;
    }

    abstract public function guiTien(float $soTien): void;
    abstract public function rutTien(float $soTien): void;
    abstract public function loaiTaiKhoan(): string;
}

// Giao diện cho tài khoản có lãi suất
interface CoLaiSuat {
    public function tinhLaiSuatHangNam(): float;
}

// Lớp Tài khoản tiết kiệm
class TaiKhoanTietKiem extends TaiKhoanNganHang implements CoLaiSuat {
    use GhiNhatKyGiaoDich;

    private const LAI_SUAT = 0.05; // 5% mỗi năm

    public function guiTien(float $soTien): void {
        $this->soDu += $soTien;
        $this->ghiGiaoDich('Gửi tiền', $soTien, $this->soDu);
    }

    public function rutTien(float $soTien): void {
        if ($this->soDu - $soTien < 1000000) {
            echo "Không thể rút tiền: số dư sau giao dịch phải lớn hơn hoặc bằng 1.000.000 VNĐ.<br>";
            return;
        }
        $this->soDu -= $soTien;
        $this->ghiGiaoDich('Rút tiền', $soTien, $this->soDu);
    }

    public function loaiTaiKhoan(): string {
        return 'Tiết kiệm';
    }

    public function tinhLaiSuatHangNam(): float {
        return $this->soDu * self::LAI_SUAT;
    }
}

// Lớp Tài khoản thanh toán
class TaiKhoanThanhToan extends TaiKhoanNganHang {
    use GhiNhatKyGiaoDich;

    public function guiTien(float $soTien): void {
        $this->soDu += $soTien;
        $this->ghiGiaoDich('Gửi tiền', $soTien, $this->soDu);
    }

    public function rutTien(float $soTien): void {
        if ($this->soDu - $soTien < 0) {
            echo "Không thể rút tiền: số dư không đủ.<br>";
            return;
        }
        $this->soDu -= $soTien;
        $this->ghiGiaoDich('Rút tiền', $soTien, $this->soDu);
    }

    public function loaiTaiKhoan(): string {
        return 'Thanh toán';
    }
}

// Lớp tiện ích ngân hàng
class NganHang {
    private static int $tongSoTaiKhoan = 0;

    public static function tangSoTaiKhoan(): void {
        self::$tongSoTaiKhoan++;
    }

    public static function layTongSoTaiKhoan(): int {
        return self::$tongSoTaiKhoan;
    }

    public static function layTenNganHang(): string {
        return 'Ngân hàng XYZ';
    }
}

// Lớp Bộ sưu tập tài khoản, triển khai IteratorAggregate
class BoSuuTapTaiKhoan implements IteratorAggregate {
    private array $danhSachTaiKhoan = [];

    public function themTaiKhoan(TaiKhoanNganHang $taiKhoan): void {
        $this->danhSachTaiKhoan[] = $taiKhoan;
    }

    public function getIterator(): \Traversable {
        return new ArrayIterator($this->danhSachTaiKhoan);
    }

    public function locTheoSoDu(float $soDuToiThieu): array {
        return array_filter($this->danhSachTaiKhoan, function(TaiKhoanNganHang $taiKhoan) use ($soDuToiThieu) {
            return $taiKhoan->laySoDu() >= $soDuToiThieu;
        });
    }
}

// Ví dụ sử dụng
/* require_once __FILE__; // Dùng cho trường hợp chạy độc lập */

echo "<h2>Hệ thống quản lý tài khoản ngân hàng số - Ngân hàng XYZ</h2>";

// Tạo tài khoản
$taiKhoanTietKiem = new TaiKhoanTietKiem('10201122', 'Nguyễn Thị A', 20000000);
$taiKhoanThanhToan1 = new TaiKhoanThanhToan('20301123', 'Lê Văn B', 8000000);
$taiKhoanThanhToan2 = new TaiKhoanThanhToan('20401124', 'Trần Minh C', 12000000);

// Tạo bộ sưu tập tài khoản và thêm tài khoản
$boSuuTapTaiKhoan = new BoSuuTapTaiKhoan();
$boSuuTapTaiKhoan->themTaiKhoan($taiKhoanTietKiem);
$boSuuTapTaiKhoan->themTaiKhoan($taiKhoanThanhToan1);
$boSuuTapTaiKhoan->themTaiKhoan($taiKhoanThanhToan2);

// Thực hiện giao dịch
$taiKhoanThanhToan1->guiTien(5000000);
$taiKhoanThanhToan2->rutTien(2000000);

// Tính và hiển thị lãi suất cho tài khoản tiết kiệm
$laiSuat = $taiKhoanTietKiem->tinhLaiSuatHangNam();
echo "<p>Lãi suất hàng năm cho {$taiKhoanTietKiem->layTenChuTaiKhoan()}: " . number_format($laiSuat) . " VNĐ</p>";

// Hiển thị thông tin tất cả tài khoản
echo "<h3>Danh sách tài khoản</h3>";
foreach ($boSuuTapTaiKhoan as $taiKhoan) {
    echo "Tài khoản: {$taiKhoan->laySoTaiKhoan()} | {$taiKhoan->layTenChuTaiKhoan()} | Loại: {$taiKhoan->loaiTaiKhoan()} | Số dư: " . number_format($taiKhoan->laySoDu()) . " VNĐ<br>";
}

// Hiển thị tổng số tài khoản và tên ngân hàng
echo "<p>Tổng số tài khoản đã tạo: " . NganHang::layTongSoTaiKhoan() . "</p>";
echo "<p>Tên ngân hàng: " . NganHang::layTenNganHang() . "</p>";
?>
