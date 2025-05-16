<?php
// data_operations.php - Các thao tác dữ liệu sử dụng PDO cho tech_factory

require_once 'db.php';

try {
    // 4.1 Thêm 5 sản phẩm mẫu
    $products = [
        ['product_name' => 'Động cơ AC', 'unit_price' => 1500000.00, 'stock_quantity' => 100],
        ['product_name' => 'Cảm biến nhiệt độ', 'unit_price' => 800000.00, 'stock_quantity' => 200],
        ['product_name' => 'Bảng điều khiển', 'unit_price' => 2500000.00, 'stock_quantity' => 50],
        ['product_name' => 'Động cơ DC', 'unit_price' => 1800000.00, 'stock_quantity' => 80],
        ['product_name' => 'Cảm biến áp suất', 'unit_price' => 1200000.00, 'stock_quantity' => 150],
    ];

    $stmtInsertProduct = $pdo->prepare("INSERT INTO products (product_name, unit_price, stock_quantity, created_at) VALUES (:product_name, :unit_price, :stock_quantity, NOW())");

    $insertedProductIds = [];
    foreach ($products as $product) {
        $stmtInsertProduct->execute([
            ':product_name' => $product['product_name'],
            ':unit_price' => $product['unit_price'],
            ':stock_quantity' => $product['stock_quantity'],
        ]);
        // 4.2 In ra ID vừa thêm
        $lastId = $pdo->lastInsertId();
        $insertedProductIds[] = $lastId;
        echo "Đã thêm sản phẩm với ID: " . $lastId . "<br>";
    }

    // 4.3 Thêm 3 đơn hàng, mỗi đơn có 2-3 sản phẩm
    $orders = [
        [
            'order_date' => '2024-06-01',
            'customer_name' => 'Khách hàng A',
            'note' => 'Giao hàng nhanh',
            'items' => [
                ['product_id' => $insertedProductIds[0], 'quantity' => 2, 'price_at_order_time' => 1500000.00],
                ['product_id' => $insertedProductIds[1], 'quantity' => 1, 'price_at_order_time' => 800000.00],
            ],
        ],
        [
            'order_date' => '2024-06-02',
            'customer_name' => 'Khách hàng B',
            'note' => null,
            'items' => [
                ['product_id' => $insertedProductIds[2], 'quantity' => 1, 'price_at_order_time' => 2500000.00],
                ['product_id' => $insertedProductIds[3], 'quantity' => 3, 'price_at_order_time' => 1800000.00],
            ],
        ],
        [
            'order_date' => '2024-06-03',
            'customer_name' => 'Khách hàng C',
            'note' => 'Kiểm tra kỹ',
            'items' => [
                ['product_id' => $insertedProductIds[4], 'quantity' => 5, 'price_at_order_time' => 1200000.00],
                ['product_id' => $insertedProductIds[0], 'quantity' => 1, 'price_at_order_time' => 1500000.00],
                ['product_id' => $insertedProductIds[2], 'quantity' => 2, 'price_at_order_time' => 2500000.00],
            ],
        ],
    ];

    $stmtInsertOrder = $pdo->prepare("INSERT INTO orders (order_date, customer_name, note) VALUES (:order_date, :customer_name, :note)");
    $stmtInsertOrderItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_order_time) VALUES (:order_id, :product_id, :quantity, :price_at_order_time)");

    foreach ($orders as $order) {
        $stmtInsertOrder->execute([
            ':order_date' => $order['order_date'],
            ':customer_name' => $order['customer_name'],
            ':note' => $order['note'],
        ]);
        $orderId = $pdo->lastInsertId();
        echo "Đã thêm đơn hàng với ID: $orderId<br>";

        foreach ($order['items'] as $item) {
            $stmtInsertOrderItem->execute([
                ':order_id' => $orderId,
                ':product_id' => $item['product_id'],
                ':quantity' => $item['quantity'],
                ':price_at_order_time' => $item['price_at_order_time'],
            ]);
        }
    }

    // 4.4 Câu lệnh chuẩn bị để thêm sản phẩm mới (ví dụ)
    $stmtPreparedInsert = $pdo->prepare("INSERT INTO products (product_name, unit_price, stock_quantity, created_at) VALUES (:product_name, :unit_price, :stock_quantity, NOW())");
    // Ví dụ sử dụng:
    // $stmtPreparedInsert->execute([':product_name' => 'Sản phẩm mới', ':unit_price' => 1000000, ':stock_quantity' => 10]);

    // 4.5 Hiển thị toàn bộ danh sách sản phẩm
    echo "Danh sách tất cả sản phẩm:<br>";
    $stmtSelectAll = $pdo->query("SELECT * FROM products");
    $productsAll = $stmtSelectAll->fetchAll();
    foreach ($productsAll as $prod) {
        echo "{$prod['id']} - {$prod['product_name']} - {$prod['unit_price']} - {$prod['stock_quantity']} - {$prod['created_at']}<br>";
    }

    // 4.6 Lọc sản phẩm có giá > 1.000.000 VNĐ
    echo "Sản phẩm có giá > 1.000.000 VNĐ:<br>";
    $stmtPriceFilter = $pdo->prepare("SELECT * FROM products WHERE unit_price > :price");
    $stmtPriceFilter->execute([':price' => 1000000]);
    $filteredProducts = $stmtPriceFilter->fetchAll();
    foreach ($filteredProducts as $prod) {
        echo "{$prod['id']} - {$prod['product_name']} - {$prod['unit_price']}<br>";
    }

    // 4.7 Hiển thị sản phẩm theo giá giảm dần
    echo "Sản phẩm theo giá giảm dần:<br>";
    $stmtOrderBy = $pdo->query("SELECT * FROM products ORDER BY unit_price DESC");
    $orderedProducts = $stmtOrderBy->fetchAll();
    foreach ($orderedProducts as $prod) {
        echo "{$prod['id']} - {$prod['product_name']} - {$prod['unit_price']}<br>";
    }

    // 4.8 Xóa một sản phẩm theo ID (ví dụ: xóa sản phẩm có id=2)
    $deleteId = $insertedProductIds[1]; // xóa sản phẩm thứ hai đã thêm
    $stmtDelete = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmtDelete->execute([':id' => $deleteId]);
    echo "Đã xóa sản phẩm với ID: $deleteId<br>";

    // 4.9 Cập nhật giá và tồn kho của sản phẩm (ví dụ: cập nhật sản phẩm id=3)
    $updateId = $insertedProductIds[2]; // cập nhật sản phẩm thứ ba đã thêm
    $newPrice = 2600000.00;
    $newStock = 45;
    $stmtUpdate = $pdo->prepare("UPDATE products SET unit_price = :price, stock_quantity = :stock WHERE id = :id");
    $stmtUpdate->execute([':price' => $newPrice, ':stock' => $newStock, ':id' => $updateId]);
    echo "Đã cập nhật sản phẩm ID $updateId với giá mới $newPrice và tồn kho $newStock<br>";

    // 4.10 Lấy 5 sản phẩm mới nhất (theo created_at)
    echo "5 sản phẩm mới nhất:<br>";
    $stmtLimit = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
    $newestProducts = $stmtLimit->fetchAll();
    foreach ($newestProducts as $prod) {
        echo "{$prod['id']} - {$prod['product_name']} - {$prod['created_at']}<br>";
    }

} catch (PDOException $e) {
    die("Lỗi thao tác cơ sở dữ liệu: " . $e->getMessage());
}
?>
