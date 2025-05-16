<?php
require 'db_connect.php';

if (isset($_GET['keyword'])) {
    $keyword = "%{$_GET['keyword']}%";
    $stmt = $conn->prepare("SELECT name, price FROM products WHERE name LIKE ?");
    $stmt->bind_param("s", $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['name']} - {$row['price']} VNĐ</li>";
    }
    if ($result->num_rows === 0) {
        echo "<li>Không tìm thấy sản phẩm.</li>";
    }
    echo "</ul>";
    
    $stmt->close();
}
$conn->close();
?>