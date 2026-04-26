<?php
require '../config/db.php';
require '../includes/auth.php';
check_auth(['user']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $req_id = $_POST['request_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Ensure the request belongs to the user and is still pending
        $stmt = $pdo->prepare("DELETE FROM book_requests WHERE id = ? AND user_id = ? AND status = 'pending'");
        $stmt->execute([$req_id, $user_id]);
    } catch (PDOException $e) {
        // Log error
    }
}

header("Location: dashboard.php");
exit();
?>
