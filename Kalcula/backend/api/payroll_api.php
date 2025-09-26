<?php
// Set the content type to application/json
header('Content-Type: application/json');

// This is a simple, hardcoded data source to simulate a database.
$employees = [
    '1' => [
        'id' => '1',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'position' => 'Software Engineer',
        'salary' => 60000.00 // Example monthly salary
    ],
    '2' => [
        'id' => '2',
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'position' => 'Project Manager',
        'salary' => 75000.00
    ],
    '3' => [
        'id' => '3',
        'first_name' => 'Peter',
        'last_name' => 'Jones',
        'position' => 'UI/UX Designer',
        'salary' => 55000.00
    ]
];

// Get the employee ID from the URL query string
$employeeId = $_GET['id'] ?? '';

// Find the employee data
if (array_key_exists($employeeId, $employees)) {
    echo json_encode($employees[$employeeId]);
} else {
    // If no employee is found, return an error message
    http_response_code(404);
    echo json_encode(['error' => 'Employee not found.']);
}

?>
