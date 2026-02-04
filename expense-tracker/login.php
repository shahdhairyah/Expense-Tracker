<?php
// expense-tracker/login.php
require_once 'config/database.php';
require_once 'includes/header.php';

session_start();

// Initialize message variables
 $message = "";
 $msgType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login Success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Log login
            $logStmt = $pdo->prepare("INSERT INTO login_logs (user_id, ip_address) VALUES (?, ?)");
            $logStmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);

            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Invalid email or password.";
            $msgType = "danger";
        }
    } else {
        $message = "Please fill in all fields.";
        $msgType = "warning";
    }
}
?>

<style>
    body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', Roboto, sans-serif;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-container {
        max-width: 900px;
        width: 100%;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        background: white;
    }
    .login-image {
        background: url('https://picsum.photos/seed/finance/600/900') no-repeat center center/cover;
        position: relative;
    }
    .login-image::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.8), rgba(25, 135, 84, 0.8));
    }
    .login-form-side {
        padding: 50px;
    }
    .form-floating input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
    .btn-login {
        background: linear-gradient(to right, #0d6efd, #0dcaf0);
        border: none;
        padding: 12px;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }
</style>

<div class="container">
    <div class="row login-container mx-auto">
        <!-- Left Side: Branding -->
        <div class="col-md-6 login-image d-none d-md-flex flex-column justify-content-center align-items-center text-white p-5">
            <h1 class="fw-bold mb-3">Welcome Back!</h1>
            <p class="lead text-center">Manage your finances efficiently with our Expense Tracker.</p>
            <div class="mt-5">
                <i class="fas fa-wallet fa-4x mb-3"></i>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="col-md-6 login-form-side">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">Login</h3>
                <p class="text-muted">Please enter your details to sign in.</p>
            </div>

            <?php if($message): ?>
                <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" required>
                    <label for="email">Email Address</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label small" for="rememberMe">Remember me</label>
                    </div>
                    <a href="./Forgot_Password.php" class="text-decoration-none small">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100 text-white fw-bold rounded-pill shadow-sm">
                    Sign In
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="small text-muted">Don't have an account? <a href="register.php" class="fw-bold text-decoration-none">Register here</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>