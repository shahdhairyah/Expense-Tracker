<?php
// expense-tracker/manage_expense.php

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
                <li class="nav-item"><a class="nav-link active" href="manage_expense.php">Manage</a></li>
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

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    header("Location: manage_expense.php");
    exit;
}

// Fetch Categories
 $catStmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ?");
 $catStmt->execute([$_SESSION['user_id']]);
 $allCategories = $catStmt->fetchAll();

// Build Query
 $where = "WHERE e.user_id = ?";
 $params = [$_SESSION['user_id']];

if (!empty($_GET['category'])) {
    $where .= " AND e.category_id = ?";
    $params[] = $_GET['category'];
}
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $where .= " AND e.expense_date BETWEEN ? AND ?";
    $params[] = $_GET['from_date'];
    $params[] = $_GET['to_date'];
}

 $sql = "SELECT e.*, c.name as category_name 
        FROM expenses e 
        LEFT JOIN categories c ON e.category_id = c.id 
        $where 
        ORDER BY e.expense_date DESC";
 $stmt = $pdo->prepare($sql);
 $stmt->execute($params);
 $expenses = $stmt->fetchAll();
?>

<style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', Roboto, sans-serif; }
    .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
</style>

<div class="container mt-5 mb-5">
    <div class="card overflow-hidden">
        <div class="card-header bg-dark text-white py-4 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold mb-0"><i class="fas fa-list me-2"></i>Manage Expenses</h3>
            <a href="add_expense.php" class="btn btn-light btn-sm fw-bold">Add New</a>
        </div>
        <div class="card-body p-4 bg-light">
            
            <!-- Filters -->
            <form method="GET" class="row g-3 mb-4 p-3 bg-white rounded shadow-sm">
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach($allCategories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="from_date" class="form-control" value="<?php echo isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" name="to_date" class="form-control" value="<?php echo isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : ''; ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="manage_expense.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <!-- Table -->
            <div class="bg-white rounded shadow-sm overflow-hidden">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Notes</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($expenses) > 0): ?>
                            <?php foreach($expenses as $exp): ?>
                            <tr>
                                <td class="fw-bold"><?php echo date('M d, Y', strtotime($exp['expense_date'])); ?></td>
                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($exp['category_name'] ?? 'Uncategorized'); ?></span></td>
                                <td class="text-muted"><?php echo htmlspecialchars($exp['notes']); ?></td>
                                <td class="fw-bold text-danger">$<?php echo number_format($exp['amount'], 2); ?></td>
                                <td>
                                    <a href="manage_expense.php?delete=<?php echo $exp['id']; ?>" class="btn btn-sm btn-danger rounded-pill px-3" onclick="return confirm('Delete this expense?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-4">No expenses found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>