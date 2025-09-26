<?php
// backend/api/employee_api.php
require_once __DIR__ . "/../../backend/config/kalcula_db.php";

header('Content-Type: application/json');

try {
    $db = new KalculaDB();
    $pdo = $db->getConnection();

    // Check if an employee ID was provided in the URL
    if (!isset($_GET['id'])) {
        echo json_encode(["error" => "Employee ID not provided."]);
        exit;
    }

    $employee_id = $_GET['id'];

    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, salary, position FROM employees WHERE id = ?");
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        echo json_encode($employee);
    } else {
        echo json_encode(["error" => "Employee not found."]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
