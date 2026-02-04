<?php
// expense-tracker/index.php
// This is the Landing Page (Home) for visitors.
session_start();

// Optional: If user is ALREADY logged in, send them straight to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker - Smart Budget Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom Styles for the Landing Page */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Navbar Styles */
        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            color: white;
            padding: 100px 0;
            border-bottom-right-radius: 50px;
            border-bottom-left-radius: 50px;
            margin-bottom: 50px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .btn-custom-light {
            background-color: white;
            color: #0d6efd;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .btn-custom-light:hover {
            background-color: #f0f0f0;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            color: #0d6efd;
        }

        /* Feature Cards */
        .feature-card {
            border: none;
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: #e7f1ff;
            color: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px auto;
        }

        /* Footer */
        footer {
            background-color: #212529;
            color: #adb5bd;
            padding: 40px 0;
            margin-top: 80px;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="index.php">
                <i class="fas fa-wallet me-2"></i>ExpenseTracker
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                        <a href="register.php" class="btn btn-primary">Get Started</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="hero-title">Take Control of Your Finances</h1>
                    <p class="hero-subtitle">Track expenses, set budgets, and visualize your spending habits with our simple, powerful, and secure budget management system.</p>
                    <a href="register.php" class="btn btn-custom-light btn-lg shadow">
                        Create Free Account <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                    <div class="mt-4 text-white-50 small">
                        <i class="fas fa-check-circle me-1"></i> No Credit Card Required &nbsp;
                        <i class="fas fa-check-circle me-1"></i> Secure & Private
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="container mb-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose Us?</h2>
            <p class="text-muted">Everything you need to manage your money in one place.</p>
        </div>
        
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon-circle">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Visual Analytics</h4>
                    <p class="text-muted">Interactive charts help you understand exactly where your money goes every month.</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon-circle">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <h4>Budget Goals</h4>
                    <p class="text-muted">Set monthly limits for different categories and get alerts before you overspend.</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon-circle">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <h4>Easy Reports</h4>
                    <p class="text-muted">Filter transactions by date or category and export your data to CSV for accounting.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="container my-5 py-5">
        <div class="card bg-primary text-white border-0 shadow-lg overflow-hidden">
            <div class="card-body p-5 text-center">
                <h2 class="fw-bold">Ready to start saving?</h2>
                <p class="lead mb-4">Join thousands of users who are managing their finances better today.</p>
                <a href="register.php" class="btn btn-light btn-lg text-primary fw-bold">Register Now</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <div class="mb-3">
                <i class="fas fa-wallet fa-2x text-white"></i>
            </div>
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Expense Tracker. All rights reserved.</p>
            <small>Built with Bootstrap 5, PHP & MySQL</small>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>