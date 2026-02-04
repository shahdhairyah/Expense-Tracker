<?php
// expense-tracker/TermsConditions.php

require_once 'includes/header.php';
// Note: We are not including the auth check here, so anyone can read the terms.
// We are using the Inline Navbar to match the rest of the site design.
?>

<!-- INLINE NAVBAR (Matches Dashboard Design) -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-wallet me-2 text-primary"></i>ExpenseTracker
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="add_expense.php">Add Expense</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_expense.php">Manage</a></li>
                <li class="nav-item"><a class="nav-link" href="budget.php">Budget</a></li>
                <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                <li class="nav-item ms-lg-2">
                    <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<style>
    .navbar-custom { background-color: #ffffff; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 15px 0; font-family: 'Segoe UI', Roboto, sans-serif; }
    .navbar-brand { font-weight: 800; font-size: 1.5rem; color: #2c3e50 !important; }
    .nav-link { font-weight: 500; color: #6c757d !important; margin: 0 10px; border-radius: 8px; transition: all 0.3s; padding: 8px 16px; }
    .nav-link:hover { color: #0d6efd !important; background-color: #f0f7ff; transform: translateY(-1px); }
</style>

<style>
    body {
        background-color: #f4f6f9;
        font-family: 'Segoe UI', Roboto, sans-serif;
        color: #444;
    }
    .terms-container {
        max-width: 900px;
        margin: 50px auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 60px;
    }
    .terms-header {
        text-align: center;
        margin-bottom: 50px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    .section-title {
        font-size: 1.25rem;
        color: #2c3e50;
        margin-top: 40px;
        margin-bottom: 15px;
        font-weight: 700;
    }
    .section-content {
        line-height: 1.8;
        color: #555;
        margin-bottom: 15px;
        text-align: justify;
    }
    .highlight-box {
        background-color: #e7f1ff;
        padding: 20px;
        border-left: 5px solid #0d6efd;
        border-radius: 5px;
        margin: 30px 0;
    }
</style>

<div class="container">
    <div class="terms-container">
        <!-- Header -->
        <div class="terms-header">
            <h1 class="fw-bold text-dark">Terms & Conditions</h1>
            <p class="text-muted">Last Updated: <?php echo date('F j, Y'); ?></p>
        </div>

        <!-- Introduction -->
        <div class="section-content">
            Welcome to <strong>ExpenseTracker</strong>. By accessing or using our service, you agree to be bound by these Terms & Conditions. Please read them carefully as they govern your use of our budget management and expense tracking platform.
        </div>

        <!-- Section 1 -->
        <h3 class="section-title">1. Acceptance of Terms</h3>
        <p class="section-content">
            By accessing and using this Application, you accept and agree to be bound by the terms and provisions of this agreement. If you do not agree to abide by these terms, please do not use this service. Furthermore, if you are using the service on behalf of a company or entity, you represent that you have the authority to bind such entity.
        </p>

        <!-- Section 2 -->
        <h3 class="section-title">2. User Responsibilities</h3>
        <p class="section-content">
            You are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer, and you agree to accept responsibility for all activities that occur under your account or password. 
        </p>
        <div class="highlight-box">
            <strong>Security Notice:</strong> While we take reasonable measures to protect your data, no method of transmission over the Internet is 100% secure. You provide your information at your own risk.
        </div>

        <!-- Section 3 -->
        <h3 class="section-title">3. Privacy Policy</h3>
        <p class="section-content">
            Your use of our Application is also subject to our Privacy Policy. Please review our Privacy Policy, which also governs the Application and informs users of our data collection practices. By using the Application, you agree to the collection and use of your information in accordance with our Privacy Policy.
        </p>

        <!-- Section 4 -->
        <h3 class="section-title">4. Accuracy of Data</h3>
        <p class="section-content">
            ExpenseTracker is a tool designed to assist you in managing your finances. While we strive to provide accurate calculations and reporting, we make no warranties about the accuracy, reliability, or completeness of the data provided. You are solely responsible for verifying the accuracy of your financial inputs.
        </p>

        <!-- Section 5 -->
        <h3 class="section-title">5. Modification of Terms</h3>
        <p class="section-content">
            We reserve the right to modify these terms at any time. All changes are effective immediately when we post them. Your continued use of the Application following the posting of revised terms means that you accept and agree to the changes.
        </p>

        <!-- Section 6 -->
        <h3 class="section-title">6. Contact Us</h3>
        <p class="section-content">
            If you have any questions about these Terms & Conditions, please contact our support team through the profile section or via the administrator email provided in the dashboard.
        </p>
        
        <hr class="my-5">
        
        <div class="text-center">
            <button onclick="window.history.back()" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Go Back
            </button>
        </div>
    </div>
</div>

<br><br>

<?php require_once 'includes/footer.php'; ?>