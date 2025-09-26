<?php
session_start();
require '../../backend/config/kalcula_db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $db = new KalculaDB();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: /bsit3a_guasis/Kalcula/frontend/dashboard/kalcula_dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kalcula Payroll System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --slate: #64748b;
            --terracotta: #e07b67;
            --cream: #f5f5dc;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--slate), var(--terracotta));
            min-height: 100vh;
        }
        .glass {
            background: rgba(245, 245, 220, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .shake {
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .input-focus { 
            transition: all 0.3s ease; 
        }
        .input-focus:focus { 
            box-shadow: 0 0 0 3px rgba(224, 123, 103, 0.3); 
            transform: scale(1.02); 
            border-color: var(--terracotta);
        }
        /* Button Styles */
        .btn-primary {
            background: var(--terracotta);
            color: white;
            font-weight: 600;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            box-shadow: 0 2px 8px rgba(224, 123, 103, 0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #d56b5c, var(--terracotta));
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(224, 123, 103, 0.4);
        }
        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(224, 123, 103, 0.5);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .btn-primary:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-secondary {
            background: transparent;
            color: white;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            color: var(--terracotta);
        }
        .btn-toggle {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            border-radius: 0.5rem;
            padding: 0.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .btn-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
    </style>
</head>
<body class="flex flex-col lg:flex-row items-center justify-center min-h-screen p-4">
    <!-- Introduction Section (Left on Desktop, Top on Mobile) - Centered and Concise -->
    <div>
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Welcome to Kalcula</h1>
        <p class="text-white text-lg mb-6 leading-relaxed max-w-md mx-auto">
            Streamline payroll with Kalcula – automated calculations, secure data management, and real-time insights for effortless compliance.
        </p>
        <ul class="space-y-2 text-white max-w-md mx-auto text-left">
            <li class="flex items-center justify-center lg:justify-start">
                <svg class="w-5 h-5 text-terracotta mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Automated salary calculations and deductions
            </li>
            <li class="flex items-center justify-center lg:justify-start">
                <svg class="w-5 h-5 text-terracotta mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Secure employee data management
            </li>
            <li class="flex items-center justify-center lg:justify-start">
                <svg class="w-5 h-5 text-terracotta mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </li>
        </ul>
    </div>

    <!-- Login Card (Right on Desktop, Bottom on Mobile) -->
    <div class="glass rounded-2xl shadow-2xl p-6 md:p-8 w-full lg:w-1/2 max-w-md animate-fade-in <?php if ($error): ?>shake<?php endif; ?>">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6 text-center">Sign In to Your Account</h2>

        <?php if ($error): ?>
            <div class="bg-red-600/20 border border-red-500/50 text-red-100 p-4 rounded-lg mb-6 text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6" id="loginForm">
            <div>
                <label for="username" class="block text-white mb-2 font-medium">Username or Email</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    class="w-full p-4 rounded-lg bg-white/10 border border-white/30 text-white placeholder-slate-200 focus:outline-none input-focus"
                    placeholder="Enter your username or email"
                    aria-describedby="username-error"
                >
            </div>
            <div class="relative">
                <label for="password" class="block text-white mb-2 font-medium">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    class="w-full p-4 rounded-lg bg-white/10 border border-white/30 text-white placeholder-slate-200 focus:outline-none input-focus pr-12"
                    placeholder="Enter your password"
                    aria-describedby="password-error"
                >
                <button 
                    type="button" 
                    id="togglePassword" 
                    class="btn-toggle absolute right-3 top-10"
                    aria-label="Toggle password visibility"
                >
                    <!-- Eye Icon (Closed by default) -->
                    <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Eye Slash Icon (for hidden state) -->
                    <svg id="eyeSlashIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                </button>
            </div>
            <button 
                type="submit" 
                class="btn-primary"
            >
                Proceed to My Account
            </button>
        </form>
        <p class="text-center text-slate-200 mt-4 text-sm">
            Forgot password? <a href="#" class="btn-secondary ml-2">Reset here</a>
        </p>
    </div>

    <script>
        // Show/Hide Password Toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icons
            eyeIcon.classList.toggle('hidden');
            eyeSlashIcon.classList.toggle('hidden');
            
            // Accessibility
            this.setAttribute('aria-label', type === 'password' ? 'Show password' : 'Hide password');
        });

        // Form Validation Feedback
        const form = document.getElementById('loginForm');
        const inputs = form.querySelectorAll('input[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('border-red-500');
                    this.classList.remove('border-green-500');
                } else {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-green-500');
                }
            });
            
            input.addEventListener('focus', function() {
                this.classList.remove('border-red-500', 'border-green-500');
            });
        });
    </script>
</body>
</html>
