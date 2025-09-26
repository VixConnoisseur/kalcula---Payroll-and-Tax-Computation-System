<?php
require_once '../config/kalcula_db.php';

$database = new KalculaDB();
$conn = $database->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT id, first_name, last_name, email, position, salary, date_hired FROM employees";

if (!empty($search)) {
    $sql .= " WHERE first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR position LIKE :search";
}
$sql .= " ORDER BY last_name, first_name";

try {
    $stmt = $conn->prepare($sql);
    if (!empty($search)) {
        $searchTerm = "%{$search}%";
        $stmt->bindParam(':search', $searchTerm);
    }
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr class='bg-white border-b hover:bg-gray-50'>";
            echo "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap'>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</td>";
            echo "<td class='px-6 py-4'>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td class='px-6 py-4'>" . htmlspecialchars($row['position']) . "</td>";
            echo "<td class='px-6 py-4'>₱" . number_format($row['salary'], 2) . "</td>";
            echo "<td class='px-6 py-4'>" . date("Y-m-d", strtotime($row['date_hired'])) . "</td>";
            echo "<td class='px-6 py-4 text-center'>
                    <button class='editBtn font-medium text-blue-600 hover:underline mr-3' data-id='" . $row['id'] . "'>Edit</button>
                    <button class='deleteBtn font-medium text-red-600 hover:underline' data-id='" . $row['id'] . "'>Delete</button>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6' class='text-center p-4'>No employees found.</td></tr>";
    }

} catch(PDOException $e) {
    echo "<tr><td colspan='6' class='text-center p-4 text-red-500'>Error: " . $e->getMessage() . "</td></tr>";
}
?>