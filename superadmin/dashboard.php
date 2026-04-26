<?php
require '../config/db.php';
require '../includes/auth.php';
check_auth(['superadmin']);

include '../includes/header.php';
?>

<h1>Super Admin Control Panel</h1>
<p>Welcome to the central management system.</p>

<div class="stats-grid">
    <a href="manage_requests.php" style="text-decoration: none;">
        <div class="stat-box bg-purple">
            <h3>Manage</h3>
            <p>Book Requests</p>
        </div>
    </a>
    <a href="manage_users.php" style="text-decoration: none;">
        <div class="stat-box bg-blue">
            <h3>Manage</h3>
            <p>User Accounts</p>
        </div>
    </a>
    <a href="manage_admins.php" style="text-decoration: none;">
        <div class="stat-box bg-secondary">
            <h3>Manage</h3>
            <p>Admin Staff</p>
        </div>
    </a>
</div>

<div class="card" style="margin-top: 2rem;">
    <h3>System Status</h3>
    <p>Logged in as: <strong><?= h($_SESSION['username']) ?></strong></p>
    <p>Last Activity: <?= date('Y-m-d H:i:s') ?></p>
</div>

<?php include '../includes/footer.php'; ?>
