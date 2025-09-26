<?php 
// employees.php
session_start(); 

// CRITICAL FIX: Explicitly require the database class file
// The path must be relative from the current file (employees.php) 
// to the kalcula_db.php file.
require __DIR__ . "/../../backend/config/kalcula_db.php"; 

// 1. Database Connection and Check
// Create a new database instance and get the connection
$db = new KalculaDB(); // This line (line 11) now correctly finds the class!
$conn = $db->getConnection();

// 2. Data Fetching Logic
// ... (rest of your PHP code follows)

$employees = []; // Initialize empty array

try {
    // Prepare SQL statement to select all data from the employees table
    $stmt = $conn->prepare("SELECT id, first_name, last_name, email, position, salary, date_hired FROM employees ORDER BY last_name");
    $stmt->execute();
    
    // Fetch all results as an associative array
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // In a real application, log this error instead of displaying it
    $error_message = "Error fetching employees: " . $e->getMessage();
}

// Check if there was an error and display it if so
if (isset($error_message)) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert"><strong class="font-bold">Error!</strong><span class="block sm:inline"> ' . htmlspecialchars($error_message) . '</span></div>';
    $employees = []; // Ensure the loop doesn't run if there's an error
}
?>

<div class="w-full flex flex-col p-6"> 
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-700">Employees Management</h1>
        <button id="addEmployeeBtn" class="bg-[var(--terracotta)] text-white font-bold py-2 px-4 rounded-lg hover:opacity-90 transition-opacity">
            Add New Employee
        </button>
    </div>

    <div class="w-full bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Position</th>
                        <th scope="col" class="px-6 py-3">Salary</th>
                        <th scope="col" class="px-6 py-3">Date Hired</th>
                        <th scope="col" class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="employeeTable">
                    <?php if (empty($employees)): ?>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No employee records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($employees as $employee): ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    <?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?>
                                </th>
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($employee['email']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($employee['position']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo '₱' . number_format($employee['salary'], 2); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo date('M d, Y', strtotime($employee['date_hired'])); ?>
                                </td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <button 
                                        data-id="<?php echo $employee['id']; ?>" 
                                        class="edit-btn font-medium text-blue-600 hover:text-blue-900"
                                        title="Edit Employee"
                                    >Edit</button>
                                    <button 
                                        data-id="<?php echo $employee['id']; ?>" 
                                        class="delete-btn font-medium text-red-600 hover:text-red-900"
                                        title="Delete Employee"
                                    >Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="employeeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4">
    </div>

<script src="../js/employees.js"></script>