<?php
require '../config/db.php';
require '../includes/auth.php';

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'superadmin'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid Super Admin credentials.";
        }
    } catch (PDOException $e) {
        $error = "An error occurred.";
    }
}

include '../includes/header.php';
?>

<div class="card" style="max-width: 400px; margin: auto; border: 2px solid var(--accent);">
    <h2 style="color: var(--accent);">Super Admin Control</h2>
    <?php if($error): ?>
        <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
            <?= h($error) ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <label>Super Admin Username</label>
        <input type="text" name="username" required>
        
        <label>Password</label>
        <input type="password" name="password" required>
        
        <button type="submit" style="background: var(--accent);">Login to Console</button>
    </form>
    <p style="text-align: center; font-size: 0.8rem;"><a href="../user/login.php">Back to User Login</a></p>
</div>

<?php include '../includes/footer.php'; ?>
