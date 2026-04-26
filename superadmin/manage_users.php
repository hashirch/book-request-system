<?php
require '../config/db.php';
require '../includes/auth.php';
check_auth(['superadmin']);

$message = "";

// Handle Password Reset
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $user_id = $_POST['user_id'];
    $new_pass = "reset123"; // In reality, generate random or prompt
    $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $user_id]);
        $message = "Password for user ID $user_id reset to 'reset123'.";
    } catch (PDOException $e) {
        $message = "Error resetting password.";
    }
}

// Handle Delete User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        $stmt->execute([$user_id]);
        $message = "User deleted successfully.";
    } catch (PDOException $e) {
        $message = "Error deleting user.";
    }
}

// Fetch all regular users
try {
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY username ASC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching users.");
}

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Manage Users</h1>
    <a href="dashboard.php" style="color: var(--primary);">← Back to Hub</a>
</div>

<?php if($message): ?>
    <div class="alert alert-info"><?= h($message) ?></div>
<?php endif; ?>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $u): ?>
                <tr>
                    <td><?= h($u['username']) ?></td>
                    <td><?= h($u['email']) ?></td>
                    <td><?= date('Y-m-d', strtotime($u['created_at'])) ?></td>
                    <td style="display: flex; gap: 0.5rem;">
                        <form method="POST" onsubmit="return confirm('Reset password to reset123?');">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button type="submit" name="reset_password" style="width: auto; margin: 0; padding: 0.25rem 0.5rem; font-size: 0.8rem; background: var(--secondary);">Reset Pass</button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Delete this user account?');">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button type="submit" name="delete_user" class="btn-cancel" style="width: auto; margin: 0; padding: 0.25rem 0.5rem; font-size: 0.8rem;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
