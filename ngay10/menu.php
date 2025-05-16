<?php
require 'db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT name, description, price, stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        echo "<h3>{$row['name']}</h3>";
        echo "<p>Mô tả: {$row['description']}</p>";
        echo "<p>Giá: {$row['price']} VNĐ</p>";
        echo "<p>Tồn kho: {$row['stock']}</p>";
    } else {
        echo "<p>Không tìm thấy sản phẩm!</p>";
    }
    $stmt->close();
}
$conn->close();
?>