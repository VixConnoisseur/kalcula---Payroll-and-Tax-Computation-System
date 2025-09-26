<?php
session_start();
require __DIR__ . "/../../backend/config/kalcula_db.php";

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Example values (replace with actual DB queries later)
$totalEmployees = 42;
$upcomingPayday = date("F d, Y", strtotime("+5 days"));
$daysToPayday = 5;
$payrollProcessed = "September 20, 2025";
$notifications = [
    "New employee registered",
    "Payroll processed successfully",
    "System update scheduled",
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kalcula Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',  // Enable dark mode
    }
  </script>
  <script>
    // Load dark mode on app start
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.documentElement.classList.add('dark');
    }
  </script>
  <style>
    :root {
      --slate: #64748b;
      --terracotta: #e2725b;
      --cream: #fdf6ec;
    }
    body {
      background-color: var(--cream);
      font-family: "Inter", sans-serif;
    }
    .glass {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 16px;
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .active {
      background-color: var(--terracotta);
      color: #fff !important;
    }
    /* Dark mode overrides for glass and colors */
    .dark .glass {
      background: rgba(31, 41, 55, 0.8);
      border-color: rgba(75, 85, 79, 0.3);
    }
    .dark .bg-white { background-color: #1f2937; }
    .dark .text-slate-700 { color: #f9fafb; }
    .dark .text-slate-500 { color: #d1d5db; }
    .dark .border-slate-300 { border-color: #4b5563; }
    .dark .bg-slate-200 { background-color: #374151; }
    .dark .bg-white\/50 { background-color: rgba(255, 255, 255, 0.1); }
    .dark .border-slate-200 { border-color: #4b5563; }
    /* Dropdown enhancements */
    #profileMenu {
      z-index: 50;
    }
  </style>
</head>
<body class="flex h-screen overflow-hidden dark:bg-gray-900 transition-colors duration-300">
  <!-- Sidebar -->
  <aside class="w-64 bg-gradient-to-b from-slate-700 to-slate-900 text-white flex flex-col p-6 dark:from-gray-800 dark:to-gray-900">
    <h2 class="text-2xl font-bold mb-10">Kalcula</h2>
    <nav class="flex-1 space-y-3">
      <button data-page="kalcula_dashboard_content.php" class="tab-btn active w-full text-left px-4 py-2 rounded-lg hover:bg-[var(--terracotta)]">Dashboard</button>
      <button data-page="employees.php" class="tab-btn w-full text-left px-4 py-2 rounded-lg hover:bg-[var(--terracotta)]">Employees</button>
      <button data-page="payroll.php" class="tab-btn w-full text-left px-4 py-2 rounded-lg hover:bg-[var(--terracotta)]">Payroll</button>
      <button data-page="settings.php" class="tab-btn w-full text-left px-4 py-2 rounded-lg hover:bg-[var(--terracotta)]">Settings</button>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 flex flex-col">
    <!-- Topbar -->
    <header class="flex items-center justify-between p-4 bg-white shadow-md dark:bg-gray-800 dark:border-b dark:border-gray-700">
      <div class="flex-1 max-w-lg">
        <input type="text" placeholder="Search..." class="w-full px-4 py-2 rounded-lg border border-slate-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-[var(--terracotta)] bg-white dark:bg-gray-700 text-slate-700 dark:text-gray-300">
      </div>

      <!-- Profile Dropdown (Enhanced: Logout Tab) -->
      <div class="relative ml-4">
        <button 
          id="profileBtn" 
          class="w-10 h-10 rounded-full bg-slate-600 dark:bg-gray-600 text-white flex items-center justify-center font-bold focus:outline-none focus:ring-2 focus:ring-[var(--terracotta)] focus:ring-offset-2"
          aria-label="Open user menu" 
          title="Open profile and logout menu"
          aria-haspopup="true"
          aria-expanded="false">
          <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
        </button>
        <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 glass p-2 dark:bg-gray-800" role="menu" aria-labelledby="profileBtn">
          <a href="profile.php" class="block px-4 py-2 rounded hover:bg-[var(--terracotta)] hover:text-white focus:outline-none focus:ring-2 focus:ring-[var(--terracotta)] focus:ring-offset-2" role="menuitem">My Profile</a>
          <button 
            onclick="logoutWithConfirm()" 
            class="block w-full text-left px-4 py-2 rounded hover:bg-[var(--terracotta)] hover:text-white focus:outline-none focus:ring-2 focus:ring-[var(--terracotta)] focus:ring-offset-2" 
            role="menuitem" 
            aria-label="Log out of Kalcula" 
            title="Log out and return to login page">
            🚪 Logout
          </button>
        </div>
      </div>
    </header>

    <!-- Page Content -->
    <main id="content" class="flex-1 p-6 overflow-y-auto flex">
      <!-- Left: Info Cards -->
      <div class="flex-1 space-y-6">
        <div class="glass p-6 shadow-lg dark:bg-gray-800">
          <h1 class="text-3xl font-bold text-slate-700 dark:text-gray-300">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h1>
          <p class="mt-2 text-slate-500 dark:text-gray-400">Here’s what’s happening today:</p>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Employee Headcount -->
          <div class="glass p-6 shadow-lg dark:bg-gray-800">
            <h2 class="text-xl font-bold text-slate-700 dark:text-gray-300">Employee Headcount</h2>
            <p class="text-3xl font-bold mt-2 text-slate-700 dark:text-gray-300"><?php echo $totalEmployees; ?></p>
            <div class="mt-4 h-2 bg-slate-200 dark:bg-gray-600 rounded-full">
              <div class="h-2 bg-[var(--terracotta)] rounded-full" style="width: 70%;"></div>
            </div>
            <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">70% active, 30% inactive</p>
          </div>

          <!-- Upcoming Payday -->
          <div class="glass p-6 shadow-lg dark:bg-gray-800">
            <h2 class="text-xl font-bold text-slate-700 dark:text-gray-300">Upcoming Payday</h2>
            <p class="text-3xl font-bold mt-2 text-slate-700 dark:text-gray-300"><?php echo $upcomingPayday; ?></p>
            <p class="mt-2 text-slate-500 dark:text-gray-400">In <?php echo $daysToPayday; ?> days</p>
          </div>

          <!-- Payroll Processed -->
          <div class="glass p-6 shadow-lg dark:bg-gray-800">
            <h2 class="text-xl font-bold text-slate-700 dark:text-gray-300">Last Payroll</h2>
            <p class="text-3xl font-bold mt-2 text-green-600">✔</p>
            <p class="mt-2 text-slate-500 dark:text-gray-400">Processed on <?php echo $payrollProcessed; ?></p>
          </div>
        </div>
      </div>

      <!-- Right: Notifications -->
      <aside class="w-80 ml-6 glass p-6 shadow-lg dark:bg-gray-800">
        <h2 class="text-xl font-bold text-slate-700 dark:text-gray-300 mb-4">Notifications</h2>
        <ul class="space-y-3">
          <?php foreach ($notifications as $note): ?>
            <li class="p-3 rounded-lg bg-white/50 dark:bg-gray-700 border border-slate-200 dark:border-gray-600">
              <p class="text-slate-700 dark:text-gray-300"><?php echo $note; ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      </aside>
    </main>
  </div>

  <script>
    // Sidebar tab loader (unchanged)
    document.querySelectorAll(".tab-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        const page = btn.getAttribute("data-page");
        fetch(page)
          .then(res => res.text())
          .then(html => {
            document.getElementById("content").innerHTML = html;
          })
          .catch(() => {
            document.getElementById("content").innerHTML =
              `<p class="text-red-600 dark:text-red-400">Error loading ${page}</p>`;
          });
      });
    });

    // Profile menu toggle (enhanced with aria-expanded)
    const profileBtn = document.getElementById("profileBtn");
    const profileMenu = document.getElementById("profileMenu");
    profileBtn.addEventListener("click", () => {
      const isHidden = profileMenu.classList.toggle("hidden");
      profileBtn.setAttribute("aria-expanded", !isHidden);
      profileBtn.setAttribute("aria-label", isHidden ? "Open user menu" : "Close user menu");
    });

    // New: Logout with confirmation (for dropdown button)
    window.logoutWithConfirm = async function() {
      if (confirm('Are you sure you want to log out? All unsaved changes will be lost.')) {
        try {
          // Fetch backend to destroy session securely
          await fetch('../../backend/auth/logout.php');
        } catch (error) {
          console.error('Logout request error:', error);
          // Fallback: Redirect anyway (session may need manual clear)
        }
        // Redirect to login
        window.location.href = '../auth/login.php';  // Adjust path if needed
      }
    };

    // Close dropdown on outside click
    document.addEventListener('click', (e) => {
      if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
        profileMenu.classList.add('hidden');
        profileBtn.setAttribute("aria-expanded", "false");
      }
    });

    // Close dropdown on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !profileMenu.classList.contains('hidden')) {
        profileMenu.classList.add('hidden');
        profileBtn.setAttribute("aria-expanded", "false");
        profileBtn.focus();
      }
    });
  </script>
</body>
</html>
