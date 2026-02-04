<?php
// expense-tracker/dashboard.php

require_once 'includes/auth.php';
require_once 'config/database.php';


// 1. Get Current Month Budget
 $currentMonth = date('Y-m');
 $stmt = $pdo->prepare("SELECT amount FROM budgets WHERE user_id = ? AND month = ?");
 $stmt->execute([$_SESSION['user_id'], $currentMonth]);
 $budget = $stmt->fetchColumn();

// 2. Calculate Total Expense (Current Month)
 $stmt = $pdo->prepare("SELECT SUM(amount) FROM expenses WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?");
 $stmt->execute([$_SESSION['user_id'], $currentMonth]);
 $totalExpense = $stmt->fetchColumn() ?? 0;

// 3. Calculate Remaining
 $remaining = $budget - $totalExpense;

// 4. Recent Transactions
 $stmt = $pdo->prepare("SELECT e.*, c.name as category_name FROM expenses e LEFT JOIN categories c ON e.category_id = c.id WHERE e.user_id = ? ORDER BY e.expense_date DESC LIMIT 5");
 $stmt->execute([$_SESSION['user_id']]);
 $recentTxns = $stmt->fetchAll();

// ---------------------------------------------------------
// 5. CHART DATA GENERATION (Real Data)
// ---------------------------------------------------------

// A. Bar Chart Data: Last 6 Months
 $barLabels = [];
 $barData = [];
for ($i = 5; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $barLabels[] = date('M Y', strtotime($date)); // Format: Jan 2024
    
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM expenses WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?");
    $stmt->execute([$_SESSION['user_id'], $date]);
    $barData[] = $stmt->fetchColumn() ?? 0;
}

// B. Pie Chart Data: Categories (Current Month)
 $stmt = $pdo->prepare("SELECT c.name, SUM(e.amount) as total FROM expenses e JOIN categories c ON e.category_id = c.id WHERE e.user_id = ? AND DATE_FORMAT(e.expense_date, '%Y-%m') = ? GROUP BY c.name");
 $stmt->execute([$_SESSION['user_id'], $currentMonth]);
 $categoryStats = $stmt->fetchAll();

 $pieLabels = [];
 $pieData = [];
foreach ($categoryStats as $cat) {
    $pieLabels[] = $cat['name'];
    $pieData[] = $cat['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Expense Tracker</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        /* --- Navbar Styling --- */
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: #2c3e50 !important;
        }
        .nav-link {
            font-weight: 500;
            color: #6c757d !important;
            margin: 0 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .nav-link:hover {
            color: #0d6efd !important;
            background-color: #f0f7ff;
        }
        .nav-link.active {
            color: #ffffff !important;
            background-color: #0d6efd;
            box-shadow: 0 4px 6px rgba(13, 110, 253, 0.3);
        }
        /* Special style for Admin Link */
        .nav-link.admin-link {
            color: #dc3545 !important;
            font-weight: 600;
        }
        .nav-link.admin-link:hover {
            background-color: #fff5f5;
            color: #b02a37 !important;
        }

        /* --- Cards --- */
        .stats-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            background: #fff;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        /* --- Charts --- */
        .chart-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
            padding: 20px;
        }

        /* --- Table --- */
        .table-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .table thead th {
            border-top: none;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

<!-- CUSTOM NAVBAR -->
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
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_expense.php">Add Expense</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_expense.php">Manage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="budget.php">Budget</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reports.php">Reports</a>
                </li>
                
                <!-- ADMIN LINK CONDITIONAL -->
                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <li class="nav-item">
                    <a class="nav-link admin-link" href="admin/index.php">
                        <i class="fas fa-user-shield me-1"></i> Admin Dashboard
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Dashboard Overview</h2>
        <span class="text-muted small"><?php echo date('l, F j, Y'); ?></span>
    </div>

    <!-- 1. Summary Cards -->
    <div class="row g-4 mb-4">
        <!-- Budget -->
        <div class="col-xl-4 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-primary me-4">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Total Budget</h6>
                        <h3 class="fw-bold mb-0">$<?php echo number_format($budget, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense -->
        <div class="col-xl-4 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-danger me-4">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Total Expenses</h6>
                        <h3 class="fw-bold mb-0">$<?php echo number_format($totalExpense, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Remaining -->
        <div class="col-xl-4 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stats-icon bg-success me-4">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.8rem;">Remaining</h6>
                        <h3 class="fw-bold mb-0">$<?php echo number_format($remaining, 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Bar Chart -->
        <div class="col-lg-8">
            <div class="chart-card h-100">
                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-chart-bar text-primary me-2"></i>Expense Overview (6 Months)</h5>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="expenseBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-lg-4">
            <div class="chart-card h-100">
                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-chart-pie text-warning me-2"></i>Category Distribution</h5>
                <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center;">
                    <canvas id="categoryPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Recent Transactions -->
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-history text-secondary me-2"></i>Recent Transactions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Category</th>
                                    <th>Note</th>
                                    <th class="text-end pe-4">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($recentTxns) > 0): ?>
                                    <?php foreach($recentTxns as $txn): ?>
                                    <tr>
                                        <td class="ps-4"><?php echo date('M d, Y', strtotime($txn['expense_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?php echo htmlspecialchars($txn['category_name'] ?? 'Uncategorized'); ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small"><?php echo htmlspecialchars($txn['notes']); ?></td>
                                        <td class="text-end pe-4 fw-bold text-danger">
                                            -$<?php echo number_format($txn['amount'], 2); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No transactions found yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-center border-0">
                    <a href="manage_expense.php" class="btn btn-sm btn-outline-primary">View All Expenses</a>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Bar Chart Logic
    const ctxBar = document.getElementById('expenseBarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($barLabels); ?>,
            datasets: [{
                label: 'Monthly Expenses',
                data: <?php echo json_encode($barData); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // 2. Pie Chart Logic
    const ctxPie = document.getElementById('categoryPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($pieLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($pieData); ?>,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>

</body>
</html>