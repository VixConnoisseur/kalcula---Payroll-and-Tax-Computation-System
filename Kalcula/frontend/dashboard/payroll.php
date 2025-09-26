<?php
// payroll.php
session_start();
// Require the database connection
require __DIR__ . "/../../backend/config/kalcula_db.php";
?>

<div class="w-full flex flex-col p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-700">Payroll and Tax Computation</h1>
    </div>

    <!-- Main Content Area -->
    <div class="w-full bg-white rounded-lg shadow-lg p-6">
        <!-- Step 1: Employee ID Input -->
        <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4 mb-6">
            <label for="employeeId" class="text-sm font-medium text-gray-900 whitespace-nowrap">Enter Employee ID:</label>
            <input type="text" id="employeeId" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[var(--terracotta)] focus:border-[var(--terracotta)] block w-full md:w-auto p-2.5" placeholder="e.g., 1">
            <button id="fetchEmployeeBtn" class="w-full md:w-auto bg-[var(--terracotta)] text-white font-bold py-2 px-4 rounded-lg hover:opacity-90 transition-opacity">Fetch Details</button>
        </div>

        <!-- Employee Details Section -->
        <div id="employeeDetails" class="mt-6">
            <h2 class="text-xl font-bold text-slate-700 mb-4">Employee Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-gray-700">Name:</span>
                    <span id="employeeName" class="text-gray-900">N/A</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-gray-700">Position:</span>
                    <span id="employeePosition" class="text-gray-900">N/A</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-gray-700">Daily Rate:</span>
                    <span id="employeeDailyRate" class="text-gray-900">N/A</span>
                </div>
            </div>

            <!-- Input Days Worked/Absent -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <div>
                    <label for="daysWorked" class="block mb-2 text-sm font-medium text-gray-900">Days Worked</label>
                    <input type="number" id="daysWorked" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[var(--terracotta)] focus:border-[var(--terracotta)] block w-full p-2.5" required min="0" value="0">
                </div>
                <div>
                    <label for="daysAbsent" class="block mb-2 text-sm font-medium text-gray-900">Days Absent</label>
                    <input type="number" id="daysAbsent" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[var(--terracotta)] focus:border-[var(--terracotta)] block w-full p-2.5" required min="0" value="0">
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button id="computePayrollBtn" class="bg-green-600 text-white font-bold py-2 px-4 rounded-lg hover:opacity-90 transition-opacity">
                    Compute Payroll
                </button>
            </div>
        </div>

        <!-- Computation Summary Section -->
        <div id="payrollSummary" class="mt-6 border-t pt-6">
            <h2 class="text-xl font-bold text-slate-700 mb-4">Computation Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-gray-700">Gross Pay:</span>
                    <span id="grossPay" class="text-gray-900">N/A</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-gray-700">SSS Contribution:</span>
                    <span id="sss" class="text-gray-900">N/A</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-gray-700">PhilHealth Contribution:</span>
                    <span id="philhealth" class="text-gray-900">N/A</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-gray-700">Pag-Ibig Contribution:</span>
                    <span id="pagibig" class="text-gray-900">N/A</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-gray-700">Withholding Tax:</span>
                    <span id="tax" class="text-gray-900">N/A</span>
                </div>
            </div>
            <div class="mt-6">
                <div class="font-bold text-2xl flex justify-between">
                    <span>Net Pay:</span>
                    <span id="netPay" class="text-[var(--terracotta)]">N/A</span>
                </div>
            </div>

            <!-- Print/Export Button -->
            <div class="mt-6 flex justify-end">
                <button id="printPayslipBtn" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:opacity-90 transition-opacity">
                    Print Payslip
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to handle logic -->
<script src="../js/payroll.js"></script>
