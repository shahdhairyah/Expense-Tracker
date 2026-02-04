<?php
// expense-tracker/register.php
require_once 'config/database.php';
require_once 'includes/header.php';

 $message = "";
 $msgType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Simple Validation
    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $msgType = "danger";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $msgType = "warning";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $message = "Email is already registered.";
            $msgType = "danger";
        } else {
            // Register User
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $message = "Registration successful! Please login.";
                $msgType = "success";
                // Optional: redirect automatically
                // header("Location: login.php"); exit;
            } else {
                $message = "Something went wrong. Try again.";
                $msgType = "danger";
            }
        }
    }
}
?>

<style>
    body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', Roboto, sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
    }
    .register-container {
        max-width: 900px;
        width: 100%;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        background: white;
    }
    .register-image {
        background: url('https://picsum.photos/seed/register/600/900') no-repeat center center/cover;
        position: relative;
    }
    .register-image::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.8), rgba(13, 110, 253, 0.8));
    }
    .register-form-side {
        padding: 50px;
    }
    .form-floating input:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.1);
    }
    .btn-register {
        background: linear-gradient(to right, #198754, #20c997);
        border: none;
        padding: 12px;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }
</style>

<div class="container">
    <div class="row register-container mx-auto">
        <!-- Left Side: Branding -->
        <div class="col-md-6 register-image d-none d-md-flex flex-column justify-content-center align-items-center text-white p-5">
            <h1 class="fw-bold mb-3">Create Account</h1>
            <p class="lead text-center">Join us today to start tracking your expenses and achieve your financial goals.</p>
            <div class="mt-5">
                <i class="fas fa-user-plus fa-4x mb-3"></i>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="col-md-6 register-form-side">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">Sign Up</h3>
                <p class="text-muted">It's free and takes less than a minute.</p>
            </div>

            <?php if($message): ?>
                <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-floating mb-3">
                    <input type="text" name="name" class="form-control" id="name" placeholder="Full Name" required>
                    <label for="name">Full Name</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" required>
                    <label for="email">Email Address</label>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control" id="password" placeholder="Password" minlength="6" required>
                            <label for="password">Password</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm" minlength="6" required>
                            <label for="confirm_password">Confirm</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="terms" required>
                    <label class="form-check-label small" for="terms">
                        I agree to the <a href="./TermsConditions.php" class="text-decoration-none">Terms & Conditions</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-success btn-register w-100 text-white fw-bold rounded-pill shadow-sm">
                    Create Account
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="small text-muted">Already have an account? <a href="login.php" class="fw-bold text-decoration-none">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>