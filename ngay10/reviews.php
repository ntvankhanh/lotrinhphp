<?php
require 'db_connect.php';

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $stmt = $conn->prepare("SELECT user_name, comment FROM reviews WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><strong>{$row['user_name']}</strong>: {$row['comment']}</li>";
    }
    if ($result->num_rows === 0) {
        echo "<li>Chưa có đánh giá.</li>";
    }
    echo "</ul>";
    
    $stmt->close();
}
$conn->close();
?>