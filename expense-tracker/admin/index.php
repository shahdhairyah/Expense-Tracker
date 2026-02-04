<?php
// expense-tracker/admin/index.php

// 1. Start Session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Include Database Connection
require_once '../config/database.php';

// 3. Manual Auth Check (Redirect to login if not logged in)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// 4. Verify Admin Privileges
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // If not admin, kick them out
    header("Location: ../dashboard.php");
    exit;
}

// 5. Include Header
require_once '../includes/header.php';
?>

<!-- ADMIN ONLY NAVBAR -->
<!-- As requested: Only Logo and Logout -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5 shadow">
    <div class="container">
        <!-- Website Name / Logo -->
        <a class="navbar-brand fw-bold" href="../index.php">
            <i class="fas fa-user-shield me-2"></i>Admin Panel
        </a>
        
        <!-- Logout Button (Aligned to Right) -->
        <div class="ms-auto">
            <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
        </div>
    </div>
</nav>
<!-- END ADMIN ONLY NAVBAR -->

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6 fw-bold text-dark">Dashboard Overview</h1>
        <span class="badge bg-danger fs-6">Restricted Access</span>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card text-white bg-secondary shadow h-100 border-0">
                <div class="card-body text-center">
                    <h5 class="card-title text-white-50">Total Users</h5>
                    <h2 class="fw-bold display-4">
                        <?php 
                        echo $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(); 
                        ?>
                    </h2>
                    <i class="fas fa-users fa-2x mb-2 text-white-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info shadow h-100 border-0">
                <div class="card-body text-center">
                    <h5 class="card-title text-white-50">Total Transactions</h5>
                    <h2 class="fw-bold display-4">
                        <?php echo $pdo->query("SELECT COUNT(*) FROM expenses")->fetchColumn(); ?>
                    </h2>
                    <i class="fas fa-receipt fa-2x mb-2 text-white-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-dark shadow h-100 border-0">
                <div class="card-body text-center">
                    <h5 class="card-title text-white-50">System Volume</h5>
                    <h2 class="fw-bold display-4">
                        $<?php echo number_format($pdo->query("SELECT SUM(amount) FROM expenses")->fetchColumn() ?? 0, 2); ?>
                    </h2>
                    <i class="fas fa-chart-pie fa-2x mb-2 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- All Users Table -->
    <div class="card shadow border-0">
        <div class="card-header bg-white py-4 border-bottom-0">
            <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-list me-2"></i>Registered Users List</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
                        while($row = $stmt->fetch()): 
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <?php if($row['is_admin'] == 1): ?>
                                    <span class="badge bg-danger">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-success">User</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<br><br>

<?php require_once '../includes/footer.php'; ?>