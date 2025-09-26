<?php
require_once __DIR__ . "/../../backend/config/kalcula_db.php";

header('Content-Type: application/json');

try {
    $db = new KalculaDB();
    $pdo = $db->getConnection();

    // Employees count
    $employees = $pdo->query("SELECT COUNT(*) AS count FROM employees")->fetch()['count'];

    // Payroll records count
    $payrolls = $pdo->query("SELECT COUNT(*) AS count FROM payroll")->fetch()['count'];

    // Total net pay
    $total_net_pay_query = $pdo->query("SELECT IFNULL(SUM(net_pay),0) AS total FROM payroll");
    $total_net_pay = $total_net_pay_query->fetch(PDO::FETCH_ASSOC)['total'];

    // Return the data as a JSON object
    echo json_encode([
        "employees" => $employees,
        "payrolls" => $payrolls,
        "total_net_pay" => number_format($total_net_pay, 2, '.', '')
    ]);
} catch (Exception $e) {
    // Return an error message as JSON
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
