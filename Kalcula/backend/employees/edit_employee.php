<?php
require_once '../kalcula_db.php';

$database = new KalculaDB();
$conn = $database->getConnection();

$sql = "UPDATE employees SET 
            first_name = :first_name, 
            last_name = :last_name, 
            email = :email, 
            position = :position, 
            salary = :salary, 
            date_hired = :date_hired 
        WHERE id = :id";
        
try {
    $stmt = $conn->prepare($sql);

    // Bind parameters from the POST request
    $stmt->bindParam(':first_name', $_POST['first_name']);
    $stmt->bindParam(':last_name', $_POST['last_name']);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':position', $_POST['position']);
    $stmt->bindParam(':salary', $_POST['salary']);
    $stmt->bindParam(':date_hired', $_POST['date_hired']);
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: Could not execute the update.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>