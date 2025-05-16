<?php
// Tạo cơ sở dữ liệu và các bảng cần thiết cho website thương mại điện tử theo nội dung của createdatabase.php

$host = '127.0.0.1';
$dbname = 'Ngay10PHP';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Tạo database nếu chưa tồn tại
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $dbname");

    // Tạo bảng products
    $sqlProducts = "CREATE TABLE IF NOT EXISTS products (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255),
        description TEXT,
        price DECIMAL(10,2),
        stock INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sqlProducts);

    // Thêm dữ liệu mẫu vào bảng products
    $pdo->exec("INSERT INTO products (name, description, price, stock) VALUES
        ('Trà sữa trân châu', 'Trà sữa thơm ngon với trân châu dai', 30000, 100),
        ('Trà đào', 'Trà đào tươi mát, vị ngọt thanh', 35000, 50),
        ('Sữa tươi đường đen', 'Sữa tươi kết hợp đường đen béo ngậy', 40000, 20)
    ");

    // Tạo bảng reviews
    $sqlReviews = "CREATE TABLE IF NOT EXISTS reviews (
        id INT PRIMARY KEY AUTO_INCREMENT,
        product_id INT,
        user_name VARCHAR(255),
        comment TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sqlReviews);

    // Thêm dữ liệu mẫu vào bảng reviews
    $pdo->exec("INSERT INTO reviews (product_id, user_name, comment) VALUES
        (1, 'Nam', 'Trà sữa ngon, trân châu dai!'),
        (1, 'Lan', 'Rất đáng tiền!'),
        (2, 'Hùng', 'Trà đào tươi mát, thích lắm.')
    ");

    echo "Tạo cơ sở dữ liệu và các bảng thành công cùng với sản phẩm mẫu.";
} catch (PDOException $e) {
    die("Lỗi khi tạo cơ sở dữ liệu hoặc bảng: " . $e->getMessage());
}
?>
