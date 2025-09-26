<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch initial settings from backend API (for pre-populating forms)
$settings = [];
try {
    $apiUrl = '../../backend/api/settings.php';  // Adjust path if needed
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        $settings = $data['data'] ?? [];
    }
} catch (Exception $e) {
    // Fallback to defaults
    $settings = [
        'pto_accrual' => 8,
        'vacation_accrual' => 5,
        'sick_accrual' => 5,
        'pto_cap' => 120,
        'accrual_schedule' => 'monthly',
        'holidays' => json_encode(['New Year\'s Day - Jan 1', 'Araw ng Kagitingan - Apr 9']),  // Sample
        'company_name' => 'Your Company Inc.',
        'trade_name' => '',
        'address_physical' => '',
        'address_mailing' => '',
        'phone' => '',
        'email' => '',
        'fiscal_start' => '2024-01-01',
        'fiscal_end' => '2024-12-31',
        // ... more defaults
    ];
}
$holidaysJson = $settings['holidays'] ?? '[]';
$holidaysArray = json_decode($holidaysJson, true) ?? [];
?>
<style>
    /* Embedded styles for settings (matches dashboard glassmorphism) */
    :root {
        --slate: #64748b;
        --terracotta: #e2725b;
        --cream: #fdf6ec;
    }
    .glass {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .tab-btn.active {
        background-color: var(--terracotta);
        color: #fff !important;
        border-bottom-color: var(--terracotta);
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: var(--slate);
    }
    input:checked + .slider:before {
        transform: translateX(26px);
    }
    /* Dark mode overrides (global from dashboard) */
    .dark .glass {
        background: rgba(31, 41, 55, 0.8);
        border-color: rgba(75, 85, 79, 0.3);
    }
    .dark .text-slate-700 { color: #f9fafb; }
    .dark .text-slate-500 { color: #d1d5db; }
    .dark .border-slate-300 { border-color: #4b5563; }
    .dark .bg-slate-200 { background-color: #374151; }
    .dark input, .dark select, .dark textarea {
        background-color: #374151;
        color: #f9fafb;
        border-color: #4b5563;
    }
    /* Screen reader only */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
</style>

<!-- Settings Content (loads into #content) -->
<main class="flex-1 p-6 overflow-y-auto flex">
    <!-- Left: Main Settings Forms -->
    <div class="flex-1 space-y-6">
        <!-- Welcome/Title Card -->
        <div class="glass p-6 shadow-lg">
            <h1 class="text-3xl font-bold text-slate-700 dark:text-gray-300">Settings</h1>
            <p class="mt-2 text-slate-500 dark:text-gray-400">Configure Time-Off policies, company details, and theme preferences.</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="glass p-4 shadow-lg">
            <div class="flex border-b border-slate-200 dark:border-gray-600 mb-4">
                <button onclick="switchTab('leave-policies')" class="tab-btn px-4 py-2 -mb-px text-sm font-medium border-b-2 border-transparent focus:outline-none focus:ring-2 focus:ring-[var(--terracotta)] focus:ring-offset-2" aria-label="Switch to Time-Off & Leave Policies tab" aria-selected="true">Time-Off & Leave Policies</button>
                <button onclick="switchTab('company-info')" class="tab-btn px-4 py-2 -mb-px text-sm font-medium border-b-2 border-transparent focus:outline-none focus:ring-2 focus:ring-[var(--terracotta)] focus:ring-offset-2" aria-label="Switch to Company Information tab">Company Information</button>
                <button onclick="switchTab('theme')" class="tab-btn px-4 py-2 -mb-px text-sm font-medium border-b-2 border-transparent focus:outline-none focus:ring-2 focus:ring-[var(--terracotta)] focus:ring-offset-2" aria-label="Switch to Theme Settings tab">Theme Settings</button>
            </div>

            <!-- Tab 1: Time-Off & Leave Policies -->
            <div id="leave-policies" class="tab-content active space-y-6">
                <!-- Accrual Policies -->
                <div class="glass p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-slate-700 dark:text-gray-300 mb-4">Accrual Policies (PTO/Vacation/Sick Time)</h3>
                    <form id="accrualForm" class="space-y-4">
                        <div>
                            <label for="ptoAccrual" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">PTO Accrual Rate (hours per month)</label>
                            <input type="number" id="ptoAccrual" name="pto_accrual" min="0" step="0.5" required aria-required="true" placeholder="e.g., 8" title="Annual PTO accrual rate in hours per month (Philippine labor law minimum: 5 days/year vacation)" value="<?php echo htmlspecialchars($settings['pto_accrual'] ?? ''); ?>" class="w-full px-3 py-2 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--terracotta)] focus:border-transparent focus:outline-none bg-white dark:bg-gray-700 text-slate-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="vacationAccrual" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Vacation Accrual Rate (days per year)</label>
                            <input type="number" id="vacationAccrual" name="vacation_accrual" min="0" step="0.5" required aria-required="true" placeholder="e.g., 5" title="Vacation days accrued per year (Article 87, Labor Code)" value="<?php echo htmlspecialchars($settings['vacation_accrual'] ?? ''); ?>" class="w-full px-3 py-2 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--terracotta)] focus:border-transparent focus:outline-none bg-white dark:bg-gray-700 text-slate-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="sickAccrual" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Sick Leave Accrual Rate (days per year)</label>
                            <input type="number" id="sickAccrual" name="sick_accrual" min="0" step="0.5" required aria-required="true" placeholder="e.g., 5" title="Sick leave days accrued per year (Article 87, Labor Code)" value="<?php echo htmlspecialchars($settings['sick_accrual'] ?? ''); ?>" class="w-full px-3 py-2 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--terracotta)] focus:border-transparent focus:outline-none bg-white dark:bg-gray-700 text-slate-700 dark:text-gray-100">
                        </div>
                    </form>
                </div>

                <!-- Accrual Rates & Caps -->
                <div class="glass p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-slate-700 dark:text-gray-300 mb-4">Accrual Rates & Caps</h3>
                    <form id="capsForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="ptoCap" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">PTO Carryover Cap (hours)</label>
                            <input type="number" id="ptoCap" name="pto_cap" min="0" required aria-required="true" placeholder="e.g., 120" title="Maximum PTO hours that can carry over to next year" value="<?php echo htmlspecialchars($settings['pto_cap'] ?? ''); ?>" class="w-full px-3 py-2 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--terracotta)] focus:border-transparent focus:outline-none bg-white dark:bg-gray-700 text-slate-700 dark:text-gray-100">
                        </div>
                        <div>
                            <label for="accrualSchedule" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Accrual Schedule</label>
                            <select id="accrualSchedule" name="accrual_schedule" required aria-required="true" aria-label="Select accrual schedule" title="How often leave time accrues" class="w-full px-3 py-2 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--terracotta)] focus:border-transparent focus:outline-none bg-white dark:bg-gray-700 text-slate-700 dark:text-gray-100">
                                <option value="">Select Schedule</option>
                                <option value="monthly" <?php echo ($settings['accrual_schedule'] ?? '') === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                <option value="quarterly" <?php echo ($settings['accrual_schedule'] ?? '') === 'quarterly' ? 'selected' : ''; ?>>Quarterly</option>
                                <option value="annually" <?php echo ($settings['accrual_schedule'] ?? '') === 'annually' ? 'selected' : ''; ?>>Annually</option>
                            </select>
                        </div>
                    </form>
                </div>

                <!-- Holiday Calendar -->
                <div class="glass p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-slate-700 dark:text-gray-300 mb-4">Holiday Calendar (Philippine Holidays 2024)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto space-y-2">
                        <?php
                        $phHolidays = [
                            ['id' => 'holiday1', 'name' => 'New Year\'s Day - Jan 1', 'type' => 'Regular Holiday'],
                            ['id' => 'holiday2', 'name' => 'Chinese New Year - Jan 22 (Monday)', 'type' => 'Special Non-Working'],
                            ['id' => 'holiday3', 'name' => 'Araw ng Kagitingan - Apr 9', 'type' => 'Regular Holiday'],
                            ['id' => 'holiday4', 'name' => 'Maundy Thursday - Mar 28', 'type' => 'Special Non-Working'],
                            ['id' => 'holiday5', 'name' => 'Good Friday - Mar 29', 'type' => 'Special Non-Working'],
                            ['id' => 'holiday6', 'name' => 'Labor Day - May 1', 'type' => 'Regular Holiday'],
                            ['id' => 'holiday7', 'name' => 'Independence Day - Jun 12', 'type' => 'Regular Holiday'],
                            ['id' => 'holiday8', 'name' => 'Eid\'l Adha - Jun 17 (Monday)', 'type' => 'Regular Holiday'],
                            ['id' => 'holiday9', 'name' => 'National Heroes Day - Last Mon of Aug (Aug 26)', 'type' => 'Regular Holiday'],
                            ['id' => 'holiday10', 'name' => 'All Saints\' Day - Nov 1', 'type' => 'Special Non-Working'],
                            ['id' => 'holiday11', 'name' => 'All Souls\' Day - Nov 2', 'type' => 'Special Working'],
                            ['id' => 'holiday12', 'name' => 'Bonifacio Day - Nov 30', 'type' => 'Regular Holiday'],
                            ['id' => 'holiday13', 'name' => 'Christmas Day - Dec 25', 'type' => 'Regular Holiday'],
                            ['id' => 'holiday14', 'name' => 'Rizal Day - Dec 30', 'type' => 'Regular Holiday'],
                            ['id' => 'holiday15', 'name' => 'Last Day of Year - Dec 31', 'type' => 'Special Non-Working'],
                            ['id' => 'holiday16', 'name' => 'Chinese New Year Eve - Jan 21 (Sunday)', 'type' => 'Special Working'],
                            ['id' => 'holiday17', 'name' => 'Eid\'l Fitr - Apr 10 (Wednesday)', 'type' => 'Regular Holiday'],
                            ['id' => 'holiday18', 'name' => 'National Heroes Day - Aug 26', 'type' => 'Regular Holiday']
                        ];
                        foreach ($phHolidays as $holiday) {
                            $checked = in_array($holiday['name'], $holidaysArray) ? 'checked' : '';
                            echo "<label class='flex items-center p-2 border border-slate-200 dark:border-gray-600 rounded hover:bg-slate-50 dark:hover:bg-gray-700'>
                                <input type='checkbox' id='{$holiday['id']}' name='holidays[]' value='{$holiday['name']}' {$checked} class='mr-2 focus:ring-2 focus:ring-[var(--terracotta)] focus:outline-none' aria-label='Toggle {$holiday['name']} ({$holiday['type']})'>
                                <span class='text-sm text-slate-700 dark:text-gray-300'>{$holiday['name']} ({$holiday['type']})</span>
                            </label>";
                        }
                        ?>
                    </div>
                    <div class="mt-4">
                        <label for="customHoliday" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Add Custom Holiday</label>
                        <input type="text" id="customHoliday" name="custom_holiday" placeholder="e.g., Company Anniversary - Mar 15" class="w-full px-3 py-2 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[var(--terracotta)] focus:border-transparent focus:outline-none bg-white dark:bg-gray-700 text-slate-700 dark:text-gray-100">
                    </div>
                </div>
            </div>

            <!-- Tab 2: Company Information -->
            <div id="company-info" class="tab-content space-y-6">
                <div class="glass p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-slate-700 dark:text-gray-300 mb-4">Company Details (Philippine-based)</h3>
                    <form id="companyForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="companyName" class="block text-sm font-medium text-slate-700 dark:text-gray-300 mb-2">Legal Company Name</label>
                                <input type="text" id="companyName" name="company_name" required aria-required="true" placeholder="e.g., ABC Corporation" title="Full legal name as registered with SEC/DT