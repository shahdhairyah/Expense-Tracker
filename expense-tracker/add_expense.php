<?php
// expense-tracker/add_expense.php

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
                <li class="nav-item"><a class="nav-link active" href="add_expense.php">Add Expense</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_expense.php">Manage</a></li>
                <li class="nav-item"><a class="nav-link" href="budget.php">Budget</a></li>
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
 $msgType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_category') {
    $newCatName = trim($_POST['new_category']);
    if (!empty($newCatName)) {
        $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE user_id = ? AND name = ?");
        $checkStmt->execute([$_SESSION['user_id'], $newCatName]);
        if ($checkStmt->rowCount() > 0) {
            $message = "Category already exists.";
            $msgType = "warning";
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (user_id, name) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $newCatName]);
            $message = "Category added!";
            $msgType = "success";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_expense') {
    $amount = $_POST['amount'];
    $category_id = $_POST['category_id'];
    $date = $_POST['date'];
    $notes = trim($_POST['notes']);

    if ($amount > 0 && !empty($date)) {
        $stmt = $pdo->prepare("INSERT INTO expenses (user_id, category_id, amount, expense_date, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $category_id, $amount, $date, $notes]);
        $message = "Expense added successfully!";
        $msgType = "success";
    }
}

 $stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY name ASC");
 $stmt->execute([$_SESSION['user_id']]);
 $categories = $stmt->fetchAll();
?>

<style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', Roboto, sans-serif; }
    .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
</style>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card overflow-hidden">
                <div class="card-header bg-primary text-white py-4">
                    <h3 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Expense</h3>
                </div>
                <div class="card-body p-5 bg-white">
                    
                    <?php if($message): ?>
                        <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="action" value="add_expense">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Amount ($)</label>
                                <input type="number" step="0.01" name="amount" class="form-control form-control-lg" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date</label>
                                <input type="date" name="date" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category_id" class="form-select form-select-lg" required>
                                <option value="" selected disabled>-- Select Category --</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Add details..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">
                            <i class="fas fa-save me-2"></i> Save Expense
                        </button>
                    </form>

                    <hr class="my-5">
                    
                    <h5 class="fw-bold text-secondary">Add New Category</h5>
                    <form method="POST" class="d-flex gap-2 mt-3">
                        <input type="hidden" name="action" value="add_category">
                        <input type="text" name="new_category" class="form-control" placeholder="Category Name" required>
                        <button type="submit" class="btn btn-outline-success px-4"><i class="fas fa-plus"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>