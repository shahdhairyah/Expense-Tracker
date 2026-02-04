<?php
// expense-tracker/budget.php

require_once 'includes/auth.php';
require_once 'config/database.php';
require_once 'includes/header.php';
// NOTE: Navbar is now inline below to prevent file missing errors
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
                <li class="nav-item"><a class="nav-link active" href="budget.php">Budget</a></li>
                <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                
                <!-- ADMIN DASHBOARD LINK (Conditional) -->
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <li class="nav-item">
                    <a class="nav-link admin-link" href="admin/index.php">
                        <i class="fas fa-user-shield me-1"></i> Admin Dashboard
                    </a>
                </li>
                <?php endif; ?>

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
    .nav-link.active { color: #ffffff !important; background-color: #0d6efd; box-shadow: 0 4px 6px rgba(13, 110, 253, 0.3); }
    .nav-link.admin-link { color: #dc3545 !important; font-weight: 600; }
    .nav-link.admin-link:hover { background-color: #fff5f5; color: #b02a37 !important; }
</style>

<?php
// --- BACKEND LOGIC ---

 $message = "";
 $currentMonth = date('Y-m');
 $budgetAmount = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $month = $_POST['month'];
    $amount = $_POST['amount'];

    $stmt = $pdo->prepare("SELECT id FROM budgets WHERE user_id = ? AND month = ?");
    $stmt->execute([$_SESSION['user_id'], $month]);
    
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->prepare("UPDATE budgets SET amount = ? WHERE user_id = ? AND month = ?");
        $stmt->execute([$amount, $_SESSION['user_id'], $month]);
        $message = "Budget updated!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO budgets (user_id, month, amount) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $month, $amount]);
        $message = "Budget set!";
    }
}

// Get Data
 $stmt = $pdo->prepare("SELECT amount FROM budgets WHERE user_id = ? AND month = ?");
 $stmt->execute([$_SESSION['user_id'], $currentMonth]);
 $budgetAmount = $stmt->fetchColumn();

 $stmt = $pdo->prepare("SELECT SUM(amount) FROM expenses WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?");
 $stmt->execute([$_SESSION['user_id'], $currentMonth]);
 $spent = $stmt->fetchColumn() ?? 0;
 $remaining = $budgetAmount - $spent;
?>

<style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', Roboto, sans-serif; }
    .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
</style>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-lg-5">
            <div class="card overflow-hidden">
                <div class="card-header bg-primary text-white py-4">
                    <h4 class="fw-bold mb-0"><i class="fas fa-piggy-bank me-2"></i>Set Budget</h4>
                </div>
                <div class="card-body p-5 bg-white">
                    <?php if($message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Month</label>
                            <input type="month" name="month" class="form-control form-control-lg" value="<?php echo $currentMonth; ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Budget Amount ($)</label>
                            <input type="number" name="amount" class="form-control form-control-lg" value="<?php echo $budgetAmount; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">Save Budget</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm p-4 text-center h-100 bg-white">
                        <h6 class="text-muted">Total Spent</h6>
                        <h2 class="text-danger fw-bold">$<?php echo number_format($spent, 2); ?></h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm p-4 text-center h-100 bg-white">
                        <h6 class="text-muted">Remaining</h6>
                        <h2 class="text-success fw-bold">$<?php echo number_format($remaining, 2); ?></h2>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card shadow-sm p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Budget Status</span>
                            <?php if($remaining < 0): ?>
                                <span class="badge bg-danger rounded-pill px-3 py-2">Over Budget!</span>
                            <?php else: ?>
                                <span class="badge bg-success rounded-pill px-3 py-2">On Track</span>
                            <?php endif; ?>
                        </div>
                        <div class="progress mt-3" style="height: 10px;">
                            <?php 
                            $percent = $budgetAmount > 0 ? ($spent / $budgetAmount) * 100 : 0;
                            if($percent > 100) $percent = 100;
                            ?>
                            <div class="progress-bar bg-<?php echo $percent > 90 ? 'danger' : 'primary'; ?>" style="width: <?php echo $percent; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>