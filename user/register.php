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
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        try {
            // Check if username or email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = "Username or Email already exists.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                $stmt->execute([$username, $email, $hashed]);
                header("Location: login.php?registered=1");
                exit();
            }
        } catch (PDOException $e) {
            $error = "An error occurred. Please try again.";
        }
    }
}

include '../includes/header.php';
?>

<div class="card" style="max-width: 400px; margin: auto;">
    <h2>Create an Account</h2>
    <?php if($error): ?>
        <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem;">
            <?= h($error) ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>
        
        <label>Email Address</label>
        <input type="email" name="email" required>
        
        <label>Password</label>
        <input type="password" name="password" required>
        
        <button type="submit">Register</button>
    </form>
    <p style="text-align: center;">Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include '../includes/footer.php'; ?>
