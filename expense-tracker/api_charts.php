<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
header('Content-Type: application/json');

 $user_id = $_SESSION['user_id'];

// 1. Monthly Expenses (Last 6 months)
 $months = [];
 $amounts = [];
for($i=5; $i>=0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $months[] = $date;
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM expenses WHERE user_id = ? AND DATE_FORMAT(expense_date, '%Y-%m') = ?");
    $stmt->execute([$user_id, $date]);
    $amounts[] = $stmt->fetchColumn() ?? 0;
}

// 2. Category Expenses (Current Month)
 $currentMonth = date('Y-m');
 $stmt = $pdo->prepare("SELECT c.name, SUM(e.amount) as total FROM expenses e JOIN categories c ON e.category_id = c.id WHERE e.user_id = ? AND DATE_FORMAT(e.expense_date, '%Y-%m') = ? GROUP BY c.name");
 $stmt->execute([$user_id, $currentMonth]);
 $categoryData = $stmt->fetchAll();

echo json_encode([
    'bar' => ['labels' => $months, 'data' => $amounts],
    'pie' => $categoryData
]);
?>