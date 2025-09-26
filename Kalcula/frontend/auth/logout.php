<?php
session_start();
require __DIR__ . "/../../config/kalcula_db.php";  // Adjust path to your DB config

// Check if user is logged in (optional security)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../frontend/auth/login.php");  // Redirect if already logged out
    exit;
}

// Optional: Log logout event to DB (uncomment if you have a user_logs table)
/* try {
    $userId = $_SESSION['user_id'];
    $logoutTime = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("INSERT INTO user_logs (user_id, action, timestamp) VALUES (?, 'logout', ?)");
    $stmt->execute([$userId, $logoutTime]);
} catch (PDOException $e) {
    // Log error silently (don't break logout)
    error_log("Logout log error: " . $e->getMessage());
} */

// Destroy session and clear data
$_SESSION = array();  // Unset all session variables
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Optional: Regenerate session ID for security (prevents session fixation)
session_start();
session_regenerate_id(true);
session_destroy();

// Redirect to login page
header("Location: ../../frontend/auth/login.php");  // Adjust path to your login page
exit;
?>
