<?php
require '../config/db.php';
require '../includes/auth.php';
check_auth(['superadmin']);

$message = "";

// Handle Update Status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $req_id = $_POST['request_id'];
    $new_status = $_POST['status'];
    try {
        // Update status and reset notification flag
        $stmt = $pdo->prepare("UPDATE book_requests SET status = ?, is_notified = 0 WHERE id = ?");
        $stmt->execute([$new_status, $req_id]);
        $message = "Status updated successfully!";
    } catch (PDOException $e) {
        $message = "Error updating status.";
    }
}

// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_request'])) {
    $req_id = $_POST['request_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM book_requests WHERE id = ?");
        $stmt->execute([$req_id]);
        $message = "Request deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error deleting request.";
    }
}

// Fetch all requests with user names
try {
    $stmt = $pdo->query("SELECT br.*, u.username FROM book_requests br JOIN users u ON br.user_id = u.id ORDER BY br.created_at DESC");
    $requests = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching requests.");
}

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Manage Book Requests</h1>
    <a href="dashboard.php" style="color: var(--primary);">← Back to Hub</a>
</div>

<?php if($message): ?>
    <div class="alert alert-success"><?= h($message) ?></div>
<?php endif; ?>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Book Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($requests as $req): ?>
                <tr>
                    <td><?= h($req['username']) ?></td>
                    <td><?= h($req['book_title']) ?></td>
                    <td><?= h($req['category']) ?></td>
                    <td>
                        <form method="POST" style="display: flex; gap: 0.5rem; align-items: center; margin: 0;">
                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                            <select name="status" style="margin: 0; padding: 0.25rem; font-size: 0.8rem; width: auto;">
                                <option value="pending" <?= $req['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="in_progress" <?= $req['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="completed" <?= $req['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                            <button type="submit" name="update_status" style="width: auto; margin: 0; padding: 0.25rem 0.5rem; font-size: 0.8rem;">Update</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this request?');" style="margin: 0;">
                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                            <button type="submit" name="delete_request" class="btn-cancel" style="width: auto; margin: 0; padding: 0.25rem 0.5rem; font-size: 0.8rem;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
