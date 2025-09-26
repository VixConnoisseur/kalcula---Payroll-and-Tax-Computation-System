<?php
// kalcula_dashboard_content.php
// This is the dynamic content for the dashboard view.
session_start();
?>
<div class="flex-1 space-y-6">
    <div class="glass p-6 shadow-lg">
        <h1 class="text-3xl font-bold text-slate-700">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h1>
        <p class="mt-2 text-slate-500">Here’s what’s happening today:</p>
    </div>

    <!-- Info Cards will be populated by JavaScript after an API call -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Employee Headcount Card -->
        <div class="glass p-6 shadow-lg">
            <h2 class="text-xl font-bold text-slate-700">Employee Headcount</h2>
            <p id="totalEmployees" class="text-3xl font-bold mt-2">Loading...</p>
            <div class="mt-4 h-2 bg-slate-200 rounded-full">
                <div class="h-2 bg-[var(--terracotta)] rounded-full" style="width: 70%;"></div>
            </div>
            <p class="mt-2 text-sm text-slate-500">70% active, 30% inactive</p>
        </div>

        <!-- Total Payroll Card -->
        <div class="glass p-6 shadow-lg">
            <h2 class="text-xl font-bold text-slate-700">Total Payroll</h2>
            <p id="totalNetPay" class="text-3xl font-bold mt-2">Loading...</p>
            <p class="mt-2 text-slate-500">Last payroll run</p>
        </div>

        <!-- Total Payrolls Card -->
        <div class="glass p-6 shadow-lg">
            <h2 class="text-xl font-bold text-slate-700">Payroll Records</h2>
            <p id="totalPayrolls" class="text-3xl font-bold mt-2">Loading...</p>
            <p class="mt-2 text-slate-500">Number of processed payrolls</p>
        </div>
    </div>
</div>

<!-- Right: Notifications -->
<aside class="w-80 ml-6 glass p-6 shadow-lg">
    <h2 class="text-xl font-bold text-slate-700 mb-4">Notifications</h2>
    <ul class="space-y-3">
        <li class="p-3 rounded-lg bg-white/50 border border-slate-200">
            <p class="text-slate-700">New employee registered</p>
        </li>
        <li class="p-3 rounded-lg bg-white/50 border border-slate-200">
            <p class="text-slate-700">Payroll processed successfully</p>
        </li>
        <li class="p-3 rounded-lg bg-white/50 border border-slate-200">
            <p class="text-slate-700">System update scheduled</p>
        </li>
    </ul>
</aside>
