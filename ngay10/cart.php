<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    
    // Khởi tạo giỏ hàng nếu chưa có
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Thêm sản phẩm vào giỏ
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    
    // Đếm tổng số lượng
    $cartCount = array_sum($_SESSION['cart']);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'cartCount' => $cartCount]);
}
?>