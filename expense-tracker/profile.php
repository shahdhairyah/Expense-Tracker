<?php
// expense-tracker/profile.php

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
                <li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>
                <li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
                
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

 $user_id = $_SESSION['user_id'];
 $message = "";

 $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
 $stmt->execute([$user_id]);
 $user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $updateStmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
    $updateStmt->execute([$name, $user_id]);
    $_SESSION['user_name'] = $name;
    $message = "Profile updated!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    if (password_verify($current, $user['password'])) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $passStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $passStmt->execute([$hash, $user_id]);
        $message = "Password changed!";
    } else {
        $message = "Current password incorrect.";
    }
}
?>

<style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', Roboto, sans-serif; }
    .card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .profile-sidebar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
    }
</style>

<div class="container mt-5 mb-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card overflow-hidden">
                <div class="profile-sidebar p-4 text-center">
                    <img src="https://picsum.photos/seed/<?php echo $user['email']; ?>/150/150" class="rounded-circle border border-4 border-white shadow mb-3" alt="Profile">
                    <h4 class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></h4>
                    <p class="opacity-75"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold text-muted">User Role</h6>
                    <span class="badge bg-<?php echo $user['is_admin'] ? 'danger' : 'primary'; ?> fs-6 mb-3">
                        <?php echo $user['is_admin'] ? 'Administrator' : 'Standard User'; ?>
                    </span>
                    <hr>
                    <small class="text-muted d-block">Joined: <?php echo date('F Y', strtotime($user['created_at'])); ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <?php if($message): ?>
                <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Edit Profile</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email (Cannot Change)</label>
                            <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0 text-danger">Change Password</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <input type="hidden" name="change_password" value="1">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" minlength="6" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-danger">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>