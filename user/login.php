<?php
require '../config/db.php';
require '../includes/auth.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['role'] !== 'user') {
                $error = "Please use the admin login panel for staff accounts.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit();
            }
        } else {
            $error = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $error = "An error occurred. Please try again.";
    }
}

include '../includes/header.php';
?>

<div class="card" style="max-width: 400px; margin: auto;">
    <h2>Login</h2>
    <?php if(isset($_GET['registered'])): ?>
        <div class="alert alert-success">Registration successful! Please login.</div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
            <?= h($error) ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>
        
        <label>Password</label>
        <input type="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
    <p style="text-align: center;">Don't have an account? <a href="register.php">Register here</a></p>
    <hr>
    <p style="text-align: center; font-size: 0.8rem;"><a href="../admin/login.php">Admin Login</a> | <a href="../superadmin/login.php">Super Admin Login</a></p>
</div>

<?php include '../includes/footer.php'; ?>
