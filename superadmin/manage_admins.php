<?php
require '../config/db.php';
require '../includes/auth.php';
check_auth(['superadmin']);

$message = "";

// Handle Add Admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$username, $email, $hashed]);
            $message = "Admin added successfully.";
        } catch (PDOException $e) {
            $message = "Error adding admin (Username/Email might exist).";
        }
    }
}

// Handle Delete Admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_admin'])) {
    $admin_id = $_POST['admin_id'];
    try {
        // Double check it's an admin and not the super admin themselves
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
        $stmt->execute([$admin_id]);
        $message = "Admin removed.";
    } catch (PDOException $e) {
        $message = "Error removing admin.";
    }
}

// Fetch all admins
try {
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'admin' ORDER BY username ASC");
    $admins = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching admins.");
}

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Manage Admin Staff</h1>
    <a href="dashboard.php" style="color: var(--primary);">← Back to Hub</a>
</div>

<?php if($message): ?>
    <div class="alert alert-info"><?= h($message) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem;">
    <div class="card">
        <h3>Add New Admin</h3>
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <button type="submit" name="add_admin">Create Admin Account</button>
        </form>
    </div>

    <div class="card">
        <h3>Current Admins</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($admins as $a): ?>
                    <tr>
                        <td><?= h($a['username']) ?></td>
                        <td><?= h($a['email']) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Remove admin access for this user?');">
                                <input type="hidden" name="admin_id" value="<?= $a['id'] ?>">
                                <button type="submit" name="delete_admin" class="btn-cancel" style="width: auto; margin: 0; padding: 0.25rem 0.5rem; font-size: 0.8rem;">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
