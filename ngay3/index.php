<?php
// ==== DỮ LIỆU ĐẦU VÀO ====

// Danh sách nhân viên
$employees = [
    ['id' => 101, 'name' => 'Nguyễn Văn A', 'base_salary' => 5000000],
    ['id' => 102, 'name' => 'Trần Thị B', 'base_salary' => 6000000],
    ['id' => 103, 'name' => 'Lê Văn C', 'base_salary' => 5500000],
];

// Dữ liệu chấm công
$timesheet = [
    101 => ['2025-03-01', '2025-03-02', '2025-03-04', '2025-03-05'],
    102 => ['2025-03-01', '2025-03-03', '2025-03-04'],
    103 => ['2025-03-02', '2025-03-03', '2025-03-04', '2025-03-05', '2025-03-06'],
];

// Phụ cấp và khấu trừ
$adjustments = [
    101 => ['allowance' => 500000, 'deduction' => 200000],
    102 => ['allowance' => 300000, 'deduction' => 100000],
    103 => ['allowance' => 400000, 'deduction' => 150000],
];

define('STANDARD_WORKING_DAYS', 22);

// ==== XỬ LÝ ====

// Cập nhật nhân viên mới
$employees[] = ['id' => 104, 'name' => 'Phạm Thị D', 'base_salary' => 5800000];

// Cập nhật lại ngày công cho nhân viên 101
if (isset($timesheet[101])) {
    $days = &$timesheet[101];
    if (!in_array('2025-03-06', $days)) $days[] = '2025-03-06';
    if (!in_array('2025-03-07', $days)) array_unshift($days, '2025-03-07');
    if (($key = array_search('2025-03-01', $days)) !== false) unset($days[$key]);
    if (($key = array_search('2025-03-05', $days)) !== false) unset($days[$key]);
    sort($days);
}

// Làm sạch ngày công (xóa trùng)
foreach ($timesheet as $id => $days) {
    $timesheet[$id] = array_unique($days);
}

// Tính số ngày công
function calculateWorkingDays($timesheet) {
    $working_days = [];
    foreach ($timesheet as $id => $days) {
        $working_days[$id] = count($days);
    }
    return $working_days;
}

// Tính lương thực lĩnh
function calculateNetSalary($employee, $working_days, $adjustments) {
    $id = $employee['id'];
    $base_salary = $employee['base_salary'];
    $days = $working_days[$id] ?? 0;
    $allowance = $adjustments[$id]['allowance'] ?? 0;
    $deduction = $adjustments[$id]['deduction'] ?? 0;

    $salary = ($base_salary / STANDARD_WORKING_DAYS) * $days;
    return round($salary + $allowance - $deduction);
}

// Tạo bảng lương tổng hợp
function generatePayrollTable($employees, $working_days, $adjustments) {
    $payroll = [];
    foreach ($employees as $emp) {
        $id = $emp['id'];
        $payroll[] = [
            'id' => $id,
            'name' => $emp['name'],
            'days' => $working_days[$id] ?? 0,
            'base_salary' => $emp['base_salary'],
            'allowance' => $adjustments[$id]['allowance'] ?? 0,
            'deduction' => $adjustments[$id]['deduction'] ?? 0,
            'net_salary' => calculateNetSalary($emp, $working_days, $adjustments)
        ];
    }
    return $payroll;
}

$working_days = calculateWorkingDays($timesheet);
$payroll = generatePayrollTable($employees, $working_days, $adjustments);

// Tìm người làm nhiều nhất / ít nhất
$max_days = max($working_days);
$min_days = min($working_days);
$max_worker = $min_worker = null;
foreach ($employees as $emp) {
    if (($working_days[$emp['id']] ?? 0) == $max_days) $max_worker = $emp;
    if (($working_days[$emp['id']] ?? 0) == $min_days) $min_worker = $emp;
}

// Nhân viên làm đủ 4 ngày trở lên
$eligible_for_bonus = array_filter($employees, function ($emp) use ($working_days) {
    return ($working_days[$emp['id']] ?? 0) >= 4;
});

// Kiểm tra logic
$check_work = in_array('2025-03-03', $timesheet[102]) ? 'Có' : 'Không';
$check_adjustment = array_key_exists(101, $adjustments) ? 'Có' : 'Không';
$total_salary = array_sum(array_column($payroll, 'net_salary'));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bảng Lương Tháng 3/2025</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { margin-top: 30px; }
    </style>
</head>
<body>

<h1>BẢNG LƯƠNG THÁNG 03/2025</h1>
<table>
    <thead>
        <tr>
            <th>Mã NV</th><th>Họ tên</th><th>Ngày công</th>
            <th>Lương cơ bản</th><th>Phụ cấp</th><th>Khấu trừ</th><th>Lương thực lĩnh</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($payroll as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['days'] ?></td>
            <td><?= number_format($row['base_salary'], 0, ',', '.') ?> VND</td>
            <td><?= number_format($row['allowance'], 0, ',', '.') ?> VND</td>
            <td><?= number_format($row['deduction'], 0, ',', '.') ?> VND</td>
            <td><strong><?= number_format($row['net_salary'], 0, ',', '.') ?> VND</strong></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p><strong>Tổng quỹ lương:</strong> <?= number_format($total_salary, 0, ',', '.') ?> VND</p>
<p><strong>Nhân viên làm nhiều nhất:</strong> <?= $max_worker['name'] ?> (<?= $max_days ?> ngày công)</p>
<p><strong>Nhân viên làm ít nhất:</strong> <?= $min_worker['name'] ?> (<?= $min_days ?> ngày công)</p>

<h2>Nhân viên đủ điều kiện xét thưởng (>= 4 ngày công):</h2>
<ul>
    <?php foreach ($eligible_for_bonus as $emp): ?>
        <li><?= $emp['name'] ?> (<?= $working_days[$emp['id']] ?> ngày công)</li>
    <?php endforeach; ?>
</ul>

<h2>Kiểm tra logic</h2>
<p>Trần Thị B có đi làm ngày 2025-03-03: <strong><?= $check_work ?></strong></p>
<p>Thông tin phụ cấp của nhân viên 101 tồn tại: <strong><?= $check_adjustment ?></strong></p>

</body>
</html>
