
<?php
session_start();

// Lớp ngoại lệ tùy chỉnh
class GioHangException extends Exception {}

// Danh sách sách có sẵn
$sach = [
    ['ten' => 'Clean Code', 'gia' => 150000],
    ['ten' => 'Design Patterns', 'gia' => 200000],
    ['ten' => 'Refactoring', 'gia' => 180000],
];

// Biến khởi tạo
$loi = [];
$thanhCong = false;
$duLieuDonHang = null;

// Xử lý xóa giỏ hàng
if (isset($_POST['xoa_gio_hang'])) {
    try {
        $_SESSION['gio_hang'] = [];
        if (file_exists('cart_data.json')) {
            unlink('1.json');
        }
        setcookie('email_khach_hang', '', time() - 3600); // Xóa cookie
        $thanhCong = true;
    } catch (Exception $e) {
        $loi[] = "Lỗi khi xóa giỏ hàng: " . $e->getMessage();
    }
}

// Xử lý gửi đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['xoa_gio_hang'])) {
    // Lấy và kiểm tra dữ liệu đầu vào
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $soDienThoai = filter_input(INPUT_POST, 'so_dien_thoai', FILTER_SANITIZE_NUMBER_INT) ?? '';
    $diaChi = filter_input(INPUT_POST, 'dia_chi', FILTER_DEFAULT) ?? '';
    $diaChi = strip_tags($diaChi);

    if (!$email) {
        $loi[] = "Email không hợp lệ.";
    }
    if (!preg_match('/^\d+$/', $soDienThoai)) {
        $loi[] = "Số điện thoại không hợp lệ.";
    }
    if (empty($diaChi)) {
        $loi[] = "Địa chỉ không được để trống.";
    }

    // Xử lý giỏ hàng
    $gioHang = $_SESSION['gio_hang'] ?? [];
    foreach ($sach as $chiSo => $sachItem) {
        $keySoLuong = 'so_luong_' . $chiSo;
        $soLuong = filter_input(INPUT_POST, $keySoLuong, FILTER_SANITIZE_NUMBER_INT);
        $soLuong = intval($soLuong);
        if ($soLuong > 0) {
            $tenSach = strip_tags($sachItem['ten']);
            if (isset($gioHang[$tenSach])) {
                $gioHang[$tenSach]['so_luong'] += $soLuong;
            } else {
                $gioHang[$tenSach] = [
                    'gia' => $sachItem['gia'],
                    'so_luong' => $soLuong,
                ];
            }
        }
    }

    if (empty($gioHang)) {
        $loi[] = "Giỏ hàng trống. Vui lòng chọn sách.";
    }

    if (empty($loi)) {
        try {
            $_SESSION['gio_hang'] = $gioHang;

            // Tạo dữ liệu đơn hàng
            $sanPham = [];
            $tongTien = 0;
            foreach ($gioHang as $tenSach => $item) {
                $sanPham[] = [
                    'ten' => $tenSach,
                    'so_luong' => $item['so_luong'],
                    'gia' => $item['gia'],
                ];
                $tongTien += $item['gia'] * $item['so_luong'];
            }

            $duLieuDonHang = [
                'email_khach_hang' => $email,
                'san_pham' => $sanPham,
                'tong_tien' => $tongTien,
                'thoi_gian_tao' => date('Y-m-d H:i:s'),
                'so_dien_thoai_khach_hang' => $soDienThoai,
                'dia_chi_khach_hang' => $diaChi,
            ];

            // Lưu vào file JSON
            $duLieuJson = json_encode($duLieuDonHang, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (file_put_contents('1.json', $duLieuJson) === false) {
                throw new GioHangException("Không thể ghi dữ liệu vào file JSON.");
            }

            $thanhCong = true;
            setcookie('email_khach_hang', $email, time() + 7 * 24 * 3600); // Lưu cookie 7 ngày
        } catch (Exception $e) {
            $loi[] = "Lỗi khi xử lý đơn hàng: " . $e->getMessage();
        }
    }
} else {
    $email = $_COOKIE['email_khach_hang'] ?? '';
    $soDienThoai = '';
    $diaChi = '';
    $gioHang = $_SESSION['gio_hang'] ?? [];
}

// Tính tổng tiền hiển thị
$tongHienThi = 0;
if (!empty($gioHang)) {
    foreach ($gioHang as $item) {
        $tongHienThi += $item['gia'] * $item['so_luong'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ Hàng Đơn Giản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4">Ứng dụng Giỏ Hàng Đơn Giản</h1>

    <?php if ($loi): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($loi as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($thanhCong && $duLieuDonHang): ?>
        <div class="alert alert-success">
            <h4>Đơn hàng đã được lưu thành công!</h4>
        </div>
        <h3>Thông tin đơn hàng</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tên sách</th>
                    <th>Đơn giá (VNĐ)</th>
                    <th>Số lượng</th>
                    <th>Thành tiền (VNĐ)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($duLieuDonHang['san_pham'] as $sanPham): ?>
                    <tr>
                        <td><?= htmlspecialchars($sanPham['ten']) ?></td>
                        <td><?= number_format($sanPham['gia']) ?></td>
                        <td><?= $sanPham['so_luong'] ?></td>
                        <td><?= number_format($sanPham['gia'] * $sanPham['so_luong']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="3" class="text-end">Tổng tiền</th>
                    <th><?= number_format($duLieuDonHang['tong_tien']) ?></th>
                </tr>
            </tbody>
        </table>
        <h4>Thông tin khách hàng</h4>
        <ul>
            <li>Email: <?= htmlspecialchars($duLieuDonHang['email_khach_hang']) ?></li>
            <li>Số điện thoại: <?= htmlspecialchars($duLieuDonHang['so_dien_thoai_khach_hang']) ?></li>
            <li>Địa chỉ: <?= htmlspecialchars($duLieuDonHang['dia_chi_khach_hang']) ?></li>
            <li>Thời gian đặt hàng: <?= htmlspecialchars($duLieuDonHang['thoi_gian_tao']) ?></li>
        </ul>
        <form method="post">
            <button type="submit" name="xoa_gio_hang" class="btn btn-danger">Xóa giỏ hàng</button>
        </form>
    <?php endif; ?>

    <form method="post" class="bg-white p-4 rounded shadow-sm">
        <h3>Chọn sách</h3>
        <?php foreach ($sach as $chiSo => $sachItem): ?>
            <div class="mb-3 row">
                <label class="col-sm-4 col-form-label"><?= htmlspecialchars($sachItem['ten']) ?> (<?= number_format($sachItem['gia']) ?> VNĐ)</label>
                <div class="col-sm-2">
                    <input type="number" min="0" class="form-control" name="so_luong_<?= $chiSo ?>" value="0">
                </div>
            </div>
        <?php endforeach; ?>

        <h3>Thông tin khách hàng</h3>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        <div class="mb-3">
            <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
            <input type="text" id="so_dien_thoai" name="so_dien_thoai" class="form-control" value="<?= htmlspecialchars($soDienThoai) ?>" required>
        </div>
        <div class="mb-3">
            <label for="dia_chi" class="form-label">Địa chỉ</label>
            <textarea id="dia_chi" name="dia_chi" class="form-control" rows="3" required><?= htmlspecialchars($diaChi) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Xác nhận đặt hàng</button>
    </form>
</div>
</body>
</html>