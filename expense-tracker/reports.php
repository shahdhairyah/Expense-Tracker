<?php
// expense-tracker/reports.php

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
                <li class="nav-item"><a class="nav-link" href="budget.php">Budget</a></li>
                <li class="nav-item"><a class="nav-link active" href="reports.php">Reports</a></li>
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

// Filter Logic
 $where = "WHERE e.user_id = ?"; // FIXED: Ambiguity error solved
 $params = [$_SESSION['user_id']];
 $filterMonth = date('Y-m');

if (!empty($_GET['month'])) {
    $filterMonth = $_GET['month'];
    $where .= " AND DATE_FORMAT(e.expense_date, '%Y-%m') = ?";
    $params[] = $filterMonth;
}

// FIXED Query with table aliases
 $sql = "SELECT e.*, c.name as category_name 
        FROM expenses e 
        LEFT JOIN categories c ON e.category_id = c.id 
        $where 
        ORDER BY e.expense_date DESC";
        
 $stmt = $pdo->prepare($sql);
 $stmt->execute($params);
 $expenses = $stmt->fetchAll();

// CSV Export
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="report_'.$filterMonth.'.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Date', 'Category', 'Amount', 'Notes']);
    foreach ($expenses as $row) {
        fputcsv($output, [$row['id'], $row['expense_date'], $row['category_name'], $row['amount'], $row['notes']]);
    }
    fclose($output);
    exit;
}
?>

<style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', Roboto, sans-serif; }
    .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
</style>

<div class="container mt-5 mb-5">
    <div class="card overflow-hidden">
        <div class="card-header bg-dark text-white py-4 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold mb-0"><i class="fas fa-file-invoice me-2"></i>Expense Reports</h3>
            <a href="reports.php?export=csv&month=<?php echo $filterMonth; ?>" class="btn btn-success btn-sm"><i class="fas fa-file-csv"></i> Export CSV</a>
        </div>
        <div class="card-body p-4 bg-light">
            
            <form method="GET" class="row g-3 mb-4 p-3 bg-white rounded shadow-sm align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Select Month</label>
                    <input type="month" name="month" class="form-control" value="<?php echo $filterMonth; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <div class="bg-white rounded shadow-sm overflow-hidden">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Notes</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($expenses) > 0): ?>
                            <?php foreach($expenses as $exp): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($exp['expense_date'])); ?></td>
                                <td><?php echo htmlspecialchars($exp['category_name']); ?></td>
                                <td class="text-muted"><?php echo htmlspecialchars($exp['notes']); ?></td>
                                <td class="text-end fw-bold">$<?php echo number_format($exp['amount'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-4">No records found for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>