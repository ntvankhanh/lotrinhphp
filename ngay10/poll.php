<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $vote = $_POST['vote'];
    
    // Khởi tạo poll nếu chưa có
    if (!isset($_SESSION['poll'])) {
        $_SESSION['poll'] = [
            'interface' => 0,
            'speed' => 0,
            'service' => 0
        ];
    }
    
    // Tăng số phiếu
    $_SESSION['poll'][$vote]++;
    
    // Tính phần trăm
    $total = array_sum($_SESSION['poll']);
    $results = [];
    foreach ($_SESSION['poll'] as $key => $value) {
        $results[$key] = $total > 0 ? round(($value / $total) * 100, 2) : 0;
    }
    
    header('Content-Type: application/json');
    echo json_encode($results);
}
?>