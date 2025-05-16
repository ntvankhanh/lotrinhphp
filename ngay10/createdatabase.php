CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    description TEXT,
    price DECIMAL(10,2),
    stock INT
);
INSERT INTO products (name, description, price, stock) VALUES
    ('Trà sữa trân châu', 'Trà sữa thơm ngon với trân châu dai', 30000, 100),
    ('Trà đào', 'Trà đào tươi mát, vị ngọt thanh', 35000, 50),
    ('Sữa tươi đường đen', 'Sữa tươi kết hợp đường đen béo ngậy', 40000, 20);

    CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    user_name VARCHAR(255),
    comment TEXT
);
INSERT INTO reviews (product_id, user_name, comment) VALUES
    (1, 'Nam', 'Trà sữa ngon, trân châu dai!'),
    (1, 'Lan', 'Rất đáng tiền!'),
    (2, 'Hùng', 'Trà đào tươi mát, thích lắm.');

    DB name: Ngay10PHPNgay10PHP