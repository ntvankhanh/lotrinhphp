<?php
// create_tables.php - Tạo cơ sở dữ liệu và bảng

require_once 'db.php';

try {
    // Tạo cơ sở dữ liệu nếu chưa tồn tại
    $pdo->exec("CREATE DATABASE IF NOT EXISTS tech_factory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    // Sử dụng cơ sở dữ liệu
    $pdo->exec("USE tech_factory");

    // Tạo bảng products
    $sqlProducts = "CREATE TABLE IF NOT EXISTS products (
        id INT PRIMARY KEY AUTO_INCREMENT,
        product_name VARCHAR(100) NOT NULL,
        unit_price DECIMAL(10,2) NOT NULL,
        stock_quantity INT NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sqlProducts);

    // Tạo bảng orders
    $sqlOrders = "CREATE TABLE IF NOT EXISTS orders (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_date DATE NOT NULL,
        customer_name VARCHAR(100) NOT NULL,
        note TEXT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sqlOrders);

    // Tạo bảng order_items
    $sqlOrderItems = "CREATE TABLE IF NOT EXISTS order_items (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price_at_order_time DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sqlOrderItems);

    echo "Tạo cơ sở dữ liệu và các bảng thành công.";
} catch (PDOException $e) {
    die("Lỗi khi tạo cơ sở dữ liệu hoặc bảng: " . $e->getMessage());
}
?>
