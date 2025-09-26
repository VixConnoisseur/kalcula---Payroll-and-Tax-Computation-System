<?php
require_once '../kalcula_db.php';

$database = new KalculaDB();
$conn = $database->getConnection();

$sql = "DELETE FROM employees WHERE id = :id";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: Could not execute the deletion.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>