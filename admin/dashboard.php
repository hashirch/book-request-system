<?php
require '../config/db.php';
require '../includes/auth.php';
check_auth(['admin']);

try {
    // Total Unique Users
    $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as total FROM book_requests");
    $total_users = $stmt->fetch()['total'];

    // Total Book Requests
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM book_requests");
    $total_requests = $stmt->fetch()['total'];

    // Requests In Progress
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM book_requests WHERE status = 'in_progress'");
    $in_progress = $stmt->fetch()['total'];

    // Completed Requests
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM book_requests WHERE status = 'completed'");
    $completed = $stmt->fetch()['total'];
} catch (PDOException $e) {
    die("Error calculating statistics.");
}

include '../includes/header.php';
?>

<h1>Admin Dashboard</h1>
<p>System Overview and Statistics</p>

<div class="stats-grid">
    <div class="stat-box bg-blue">
        <h3><?= $total_users ?></h3>
        <p>Unique Users</p>
    </div>
    <div class="stat-box bg-purple">
        <h3><?= $total_requests ?></h3>
        <p>Total Requests</p>
    </div>
    <div class="stat-box bg-yellow">
        <h3><?= $in_progress ?></h3>
        <p>In Progress</p>
    </div>
    <div class="stat-box bg-green">
        <h3><?= $completed ?></h3>
        <p>Completed</p>
    </div>
</div>

<div class="card" style="margin-top: 2rem;">
    <h3>Recent Activity</h3>
    <p>View-only access enabled for admins. For management, please contact Super Admin.</p>
    <!-- We could list requests here if needed, but the manual says "view dashboard statistics" -->
</div>

<?php include '../includes/footer.php'; ?>
