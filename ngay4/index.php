<?php
session_start();

// ==== 1. CẤU HÌNH BAN ĐẦU ====
if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
    $GLOBALS['total_income'] = 0;
    $GLOBALS['total_expense'] = 0;
} else {
    $GLOBALS['total_income'] = 0;
    $GLOBALS['total_expense'] = 0;
    foreach ($_SESSION['transactions'] as $t) {
        if ($t['type'] === 'income') $GLOBALS['total_income'] += $t['amount'];
        else $GLOBALS['total_expense'] += $t['amount'];
    }
}

$errors = [];
$warnings = [];

// ==== 2. XỬ LÝ DỮ LIỆU TỪ FORM ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['transaction_name'] ?? '');
    $amount = $_POST['amount'] ?? '';
    $type = $_POST['type'] ?? '';
    $note = $_POST['note'] ?? '';
    $date = $_POST['date'] ?? '';

    if (!preg_match('/^[\p{L}\d\s]+$/u', $name)) {
        $errors[] = "Tên giao dịch không hợp lệ (không chứa ký tự đặc biệt).";
    }
    if (!preg_match('/^\d+(\.\d+)?$/', $amount) || (float)$amount <= 0) {
        $errors[] = "Số tiền phải là số dương.";
    }
    if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
        $errors[] = "Ngày thực hiện phải theo định dạng dd/mm/yyyy.";
    }

    $sensitive = ['nợ xấu', 'vay nóng'];
    foreach ($sensitive as $word) {
        if (stripos($note, $word) !== false) {
            $warnings[] = "⚠️ Ghi chú chứa từ khóa nhạy cảm: \"$word\".";
        }
    }

    if (empty($errors)) {
        $transaction = [
            'name' => $name,
            'amount' => (float)$amount,
            'type' => $type,
            'note' => $note,
            'date' => $date,
            'ip' => $_SERVER['REMOTE_ADDR'],
        ];
        $_SESSION['transactions'][] = $transaction;

        if ($type === 'income') $GLOBALS['total_income'] += $transaction['amount'];
        else $GLOBALS['total_expense'] += $transaction['amount'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tài chính cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --income-color: #2ecc71;
            --expense-color: #e74c3c;
            --light-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            padding-top: 20px;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .income-text {
            color: var(--income-color);
            font-weight: bold;
        }
        
        .expense-text {
            color: var(--expense-color);
            font-weight: bold;
        }
        
        .balance-text {
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .transaction-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .transaction-item:hover {
            transform: translateX(5px);
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .income-item {
            border-left-color: var(--income-color);
        }
        
        .expense-item {
            border-left-color: var(--expense-color);
        }
        
        .alert-warning {
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="fas fa-wallet text-primary me-2"></i>Quản Lý Tài Chính
                    </h1>
                    <p class="lead text-muted">Theo dõi thu chi cá nhân một cách dễ dàng</p>
                </div>
                
                <!-- Form thêm giao dịch -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Thêm giao dịch mới</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($errors as $e): ?>
                            <div class="alert alert-danger"><?= $e ?></div>
                        <?php endforeach; ?>
                        <?php foreach ($warnings as $w): ?>
                            <div class="alert alert-warning"><?= $w ?></div>
                        <?php endforeach; ?>
                        
                        <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                            <div class="mb-3">
                                <label for="transaction_name" class="form-label">Tên giao dịch</label>
                                <input type="text" class="form-control" id="transaction_name" name="transaction_name" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label">Số tiền (VND)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₫</span>
                                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Loại giao dịch</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" id="income" value="income" required>
                                            <label class="form-check-label text-success" for="income">
                                                <i class="fas fa-money-bill-wave me-1"></i> Thu nhập
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type" id="expense" value="expense" required>
                                            <label class="form-check-label text-danger" for="expense">
                                                <i class="fas fa-shopping-cart me-1"></i> Chi tiêu
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="note" class="form-label">Ghi chú</label>
                                <textarea class="form-control" id="note" name="note" rows="2"></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="date" class="form-label">Ngày thực hiện</label>
                                <input type="text" class="form-control" id="date" name="date" placeholder="dd/mm/yyyy" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-save me-2"></i>Lưu giao dịch
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Thống kê -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success bg-opacity-10 border-success">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted">Tổng thu</h6>
                                <p class="card-text income-text fs-4 mb-0"><?= number_format($GLOBALS['total_income'], 2) ?> ₫</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-danger bg-opacity-10 border-danger">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted">Tổng chi</h6>
                                <p class="card-text expense-text fs-4 mb-0"><?= number_format($GLOBALS['total_expense'], 2) ?> ₫</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-primary bg-opacity-10 border-primary">
                            <div class="card-body text-center">
                                <h6 class="card-title text-muted">Số dư</h6>
                                <p class="card-text balance-text <?= ($GLOBALS['total_income'] - $GLOBALS['total_expense'] >= 0 ? 'text-success' : 'text-danger' ) ?>">
                                    <?= number_format($GLOBALS['total_income'] - $GLOBALS['total_expense'], 2) ?> ₫
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Danh sách giao dịch -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lịch sử giao dịch</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($_SESSION['transactions'])): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tên giao dịch</th>
                                            <th>Số tiền</th>
                                            <th>Loại</th>
                                            <th>Ngày</th>
                                            <th>Chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($_SESSION['transactions'] as $t): ?>
                                            <tr class="transaction-item <?= $t['type'] === 'income' ? 'income-item' : 'expense-item' ?>">
                                                <td><?= htmlspecialchars($t['name']) ?></td>
                                                <td class="<?= $t['type'] === 'income' ? 'income-text' : 'expense-text' ?>">
                                                    <?= number_format($t['amount'], 2) ?> ₫
                                                </td>
                                                <td>
                                                    <?php if ($t['type'] === 'income'): ?>
                                                        <span class="badge bg-success">Thu nhập</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Chi tiêu</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $t['date'] ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" 
                                                            title="<?= htmlspecialchars($t['note']) ?>">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Chưa có giao dịch nào được ghi nhận</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Kích hoạt tooltip Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>
</html>