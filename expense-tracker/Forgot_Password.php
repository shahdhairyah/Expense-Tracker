<?php
// expense-tracker/Forgot_Password.php

require_once 'config/database.php';
require_once 'includes/header.php';

 $message = "";
 $msgType = ""; // 'success', 'danger', 'warning'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Check if email exists in database
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // EMAIL EXISTS
            // In a real production app, you would generate a token and send an email here.
            // For this demo, we will just show the success message.
            
            $message = "Password reset link has been sent to your email. Please check your inbox.";
            $msgType = "success";
        } else {
            // EMAIL DOES NOT EXIST
            $message = "This email address is not registered in our system.";
            $msgType = "warning";
        }
    } else {
        $message = "Please enter your email address.";
        $msgType = "danger";
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
    .forgot-container {
        max-width: 500px;
        width: 100%;
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .forgot-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px;
        text-align: center;
        color: white;
    }
    .forgot-body {
        padding: 40px;
    }
    .form-control {
        padding: 12px 15px;
        border-radius: 10px;
        border: 1px solid #e0e0e0;
    }
    .form-control:focus {
        box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.1);
        border-color: #764ba2;
    }
    .btn-reset {
        background: linear-gradient(to right, #667eea, #764ba2);
        border: none;
        padding: 12px;
        font-weight: 600;
        border-radius: 10px;
        width: 100%;
        color: white;
        transition: opacity 0.3s;
    }
    .btn-reset:hover {
        opacity: 0.9;
        color: white;
    }
</style>

<div class="container">
    <div class="forgot-container mx-auto">
        <!-- Header Section -->
        <div class="forgot-header">
            <div class="mb-3">
                <i class="fas fa-lock fa-3x"></i>
            </div>
            <h2 class="fw-bold">Forgot Password?</h2>
            <p class="opacity-75">Enter your email to recover your account.</p>
        </div>

        <!-- Form Section -->
        <div class="forgot-body">
            
            <!-- Alert Messages -->
            <?php if($message): ?>
                <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show text-center" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-envelope text-muted"></i>
                        </span>
                        <input type="email" name="email" class="form-control border-start-0 ps-0" placeholder="name@example.com" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-reset shadow-sm">
                    <i class="fas fa-paper-plane me-2"></i> Send Reset Link
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="login.php" class="text-decoration-none fw-bold text-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>