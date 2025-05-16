<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quán Trà Sữa Online</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .section-title {
            @apply text-2xl font-bold mb-4 text-gray-800;
        }
    </style>
</head>
<body class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-900">Quán Trà Sữa Online</h1>

        <!-- Tính năng 1: Lấy chi tiết sản phẩm -->
        <div class="mb-12">
            <h2 class="section-title">Sản phẩm</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><a href="#" class="text-blue-600 hover:underline" onclick="getProductDetails(1)">Trà sữa trân châu</a></li>
                                <li class="list-group-item"><a href="#" class="text-blue-600 hover:underline" onclick="getProductDetails(2)">Trà đào</a></li>
                                <li class="list-group-item"><a href="#" class="text-blue-600 hover:underline" onclick="getProductDetails(3)">Sữa tươi đường đen</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body" id="product-details">
                            <p class="text-gray-500">Chọn một sản phẩm để xem chi tiết</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tính năng 2: Thêm vào giỏ hàng -->
        <div class="mb-12">
            <h2 class="section-title">Giỏ hàng</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="mb-4">Số lượng: <span id="cart-count" class="badge bg-primary">0</span></p>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary" onclick="addToCart(1)">Thêm Trà sữa trân châu</button>
                        <button class="btn btn-outline-primary" onclick="addToCart(2)">Thêm Trà đào</button>
                        <button class="btn btn-outline-primary" onclick="addToCart(3)">Thêm Sữa tươi đường đen</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tính năng 3: Hiển thị đánh giá -->
        <div class="mb-12">
            <h2 class="section-title">Đánh giá sản phẩm</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <select class="form-select mb-3" onchange="getReviews(this.value)">
                        <option value="">Chọn sản phẩm</option>
                        <option value="1">Trà sữa trân châu</option>
                        <option value="2">Trà đào</option>
                        <option value="3">Sữa tươi đường đen</option>
                    </select>
                    <div id="reviews" class="text-gray-500">Chọn sản phẩm để xem đánh giá</div>
                </div>
            </div>
        </div>

        <!-- Tính năng 4: Lấy thương hiệu từ XML -->
        <div class="mb-12">
            <h2 class="section-title">Thương hiệu</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <select class="form-select mb-3" onchange="getBrands(this.value)">
                        <option value="">Chọn ngành hàng</option>
                        <option value="Drink">Đồ uống</option>
                        <option value="Fashion">Thời trang</option>
                    </select>
                    <div id="brands" class="text-gray-500">Chọn ngành hàng để xem thương hiệu</div>
                </div>
            </div>
        </div>

        <!-- Tính năng 5: Tìm kiếm thời gian thực -->
        <div class="mb-12">
            <h2 class="section-title">Tìm kiếm</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <input type="text" id="search" class="form-control mb-3" oninput="searchProducts(this.value)" placeholder="Tìm kiếm sản phẩm...">
                    <div id="search-results" class="text-gray-500">Nhập từ khóa để tìm kiếm</div>
                </div>
            </div>
        </div>

        <!-- Tính năng 6: Bình chọn -->
        <div class="mb-12">
            <h2 class="section-title">Bình chọn</h2>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="poll-form" class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vote" value="interface" id="vote1">
                            <label class="form-check-label" for="vote1">Giao diện</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vote" value="speed" id="vote2">
                            <label class="form-check-label" for="vote2">Tốc độ</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vote" value="service" id="vote3">
                            <label class="form-check-label" for="vote3">Dịch vụ khách hàng</label>
                        </div>
                        <button type="button" class="btn btn-primary mt-3" onclick="submitPoll()">Gửi</button>
                    </div>
                    <div id="poll-results"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS (Optional, for interactive components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function getProductDetails(id) {
            let xhr = new XMLHttpRequest();
            xhr.open('GET', `menu.php?id=${id}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('product-details').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function addToCart(productId) {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById('cart-count').textContent = response.cartCount;
                    }
                }
            };
            xhr.send(`product_id=${productId}`);
        }

        function getReviews(productId) {
            if (!productId) {
                document.getElementById('reviews').innerHTML = 'Chọn sản phẩm để xem đánh giá';
                return;
            }
            let xhr = new XMLHttpRequest();
            xhr.open('GET', `reviews.php?product_id=${productId}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('reviews').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function getBrands(category) {
            if (!category) {
                document.getElementById('brands').innerHTML = 'Chọn ngành hàng để xem thương hiệu';
                return;
            }
            let xhr = new XMLHttpRequest();
            xhr.open('GET', `brands.php?category=${category}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('brands').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function searchProducts(keyword) {
            if (!keyword) {
                document.getElementById('search-results').innerHTML = 'Nhập từ khóa để tìm kiếm';
                return;
            }
            let xhr = new XMLHttpRequest();
            xhr.open('GET', `search.php?keyword=${encodeURIComponent(keyword)}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('search-results').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function submitPoll() {
            let vote = document.querySelector('input[name="vote"]:checked');
            if (!vote) {
                alert('Vui lòng chọn một tùy chọn!');
                return;
            }
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'poll.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    let results = JSON.parse(xhr.responseText);
                    let output = '<h3 class="text-lg font-semibold mb-2">Kết quả bình chọn:</h3>';
                    for (let key in results) {
                        output += `<p>${key}: ${results[key]}%</p>`;
                    }
                    document.getElementById('poll-results').innerHTML = output;
                }
            };
            xhr.send(`vote=${vote.value}`);
        }
    </script>
</body>
</html>