<?php
// ==== 1. DỮ LIỆU MÔ PHỎNG ====

// Danh sách người dùng và mối quan hệ giới thiệu
$users = [
    1 => ['name' => 'Alice', 'referrer_id' => null],
    2 => ['name' => 'Bob', 'referrer_id' => 1],
    3 => ['name' => 'Charlie', 'referrer_id' => 2],
    4 => ['name' => 'David', 'referrer_id' => 3],
    5 => ['name' => 'Eva', 'referrer_id' => 1],
];

// Danh sách đơn hàng
$orders = [
    ['order_id' => 101, 'user_id' => 4, 'amount' => 200.0],  // David mua
    ['order_id' => 102, 'user_id' => 3, 'amount' => 150.0],  // Charlie mua
    ['order_id' => 103, 'user_id' => 5, 'amount' => 300.0],  // Eva mua
];

// Tỷ lệ hoa hồng theo từng cấp
$commissionRates = [
    1 => 0.10, // Cấp 1: 10%
    2 => 0.05, // Cấp 2: 5%
    3 => 0.02, // Cấp 3: 2%
];

// ==== 2. HÀM XỬ LÝ ====

// Hàm lấy chuỗi giới thiệu lên đến 3 cấp, dùng đệ quy
function getReferrers(int $userId, array $users, int $level = 1, int $maxLevel = 3): array {
    static $result = [];
    if ($level > $maxLevel || !isset($users[$userId]['referrer_id'])) {
        return $result;
    }
    $refId = $users[$userId]['referrer_id'];
    if ($refId !== null) {
        $result[$level] = $refId;
        getReferrers($refId, $users, $level + 1, $maxLevel);
    }
    return $result;
}

// Hàm chính tính hoa hồng từ danh sách đơn hàng
function calculateCommission(array $orders, array $users, array $commissionRates): array {
    $commissions = [];
    $details = [];

    foreach ($orders as $order) {
        $buyerId = $order['user_id'];
        $amount = $order['amount'];
        $referrers = getReferrers($buyerId, $users);

        foreach ($referrers as $level => $referrerId) {
            $rate = $commissionRates[$level] ?? 0;
            $commissionAmount = $amount * $rate;

            // Gộp tổng hoa hồng
            if (!isset($commissions[$referrerId])) {
                $commissions[$referrerId] = 0;
            }
            $commissions[$referrerId] += $commissionAmount;

            // Chi tiết từng khoản hoa hồng
            $details[] = [
                'receiver_id' => $referrerId,
                'receiver_name' => $users[$referrerId]['name'],
                'buyer_id' => $buyerId,
                'buyer_name' => $users[$buyerId]['name'],
                'order_id' => $order['order_id'],
                'level' => $level,
                'amount' => $commissionAmount,
            ];
        }

        // Reset static của hàm đệ quy để dùng lại
        $dummy = getReferrers(0, [], 1); // reset static $result
    }

    return ['totals' => $commissions, 'details' => $details];
}

// ==== 3. IN RA BÁO CÁO ====

// Gọi hàm xử lý
$result = calculateCommission($orders, $users, $commissionRates);

// In tổng hoa hồng từng người
echo "<h3>Tổng hoa hồng theo người dùng</h3>";
foreach ($result['totals'] as $userId => $total) {
    echo "Người dùng: " . $users[$userId]['name'] . " - Hoa hồng: $" . number_format($total, 2) . "<br>";
}

// In chi tiết hoa hồng
echo "<h3>Chi tiết hoa hồng</h3>";
foreach ($result['details'] as $detail) {
    echo "Người nhận: {$detail['receiver_name']} - Từ đơn hàng: {$detail['order_id']} - Người mua: {$detail['buyer_name']} - Cấp: {$detail['level']} - Số tiền: $" . number_format($detail['amount'], 2) . "<br>";
}

?>
