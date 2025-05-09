<?php
define('COMMISSION_RATE', 0.2);
define('VAT_RATE', 0.1);

$ten = "Spring Sale 2025";
$so_luong = 150;
$gia = 99.99;
$danh_muc = "Thời trang";
$trang_thai = True;
// $thue=0;

$don_hang = [
    "ID001" => 99.99,
    "ID002" => 49.99,
    "ID003" => 19.99,
    "ID004" => 29.99,
    "ID005" => 39.99,
];
$doanh_thu = $gia * $so_luong;
$vat = $doanh_thu * VAT_RATE;
$chi_phi = $doanh_thu * COMMISSION_RATE;
$loi_nhuan = $doanh_thu - $chi_phi - $vat;

if ($loi_nhuan > 0) {
    echo "Chiến dịch thành công<br>";
} else if ($loi_nhuan == 0) {
    echo "Chiến dịch hòa vốn";
} else {
    echo "Chiến dịch thất bại";
}
$keys = array_keys($don_hang);
$tong_doanh_thu = 0;
for ($i = 0; $i < count($don_hang); $i++) {
    $tong_doanh_thu += $don_hang[$keys[$i]];
}
switch($danh_muc){
    case "Thời trang":
        echo "Sản phẩm thời trang có doanh thu ổn định<br>";
        break;
    case "Điện tử":
        echo "Danh mục điện tử";
        break;
    case "Đồ gia dụng":
        echo "Danh mục đồ gia dụng";
        break;
    default:
        echo "Danh mục không xác định";
        break;
}
// Tên chiến dịch và trạng thái.
// Tổng doanh thu, chi phí hoa hồng, lợi nhuận (sau khi trừ VAT).
// Đánh giá hiệu quả chiến dịch.
// Chi tiết từng đơn hàng.
// Thông báo mẫu: "Chiến dịch Spring Sale 2025 đã kết thúc với lợi nhuận: [số tiền] USD".

echo "Tên chiến dịch : $ten và trạng thái " .($trang_thai ? "đang diễn ra" : "Đã kết thúc") . "<br>";
echo "Tổng doanh thu : $doanh_thu <br>";
echo "Chi phí hoa hồng : $chi_phi <br>";
echo "Lợi nhuận : $loi_nhuan <br>";
echo "Chi tiết từng đơn hàng : <br>";
foreach($don_hang as $key => $value) {
    echo "Mã sản phẩm: $key, Giá: $value <br>";
}
echo "Chiến dịch $ten đã kết thúc với lợi nhuận: $loi_nhuan USD <br>";
// echo "Danh mục sản phẩm: $danh_muc <br>";
