<?php
require '../config/db.php';
require '../includes/auth.php';
check_auth(['user']);

$user_id = $_SESSION['user_id'];

// 1. Fetch notifications (status changes)
try {
    $stmt = $pdo->prepare("SELECT book_title, status FROM book_requests WHERE user_id = ? AND is_notified = 0");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll();

    // Mark as notified
    $stmt = $pdo->prepare("UPDATE book_requests SET is_notified = 1 WHERE user_id = ? AND is_notified = 0");
    $stmt->execute([$user_id]);
} catch (PDOException $e) {
    error_log($e->getMessage());
}

// 2. Fetch all requests
try {
    $stmt = $pdo->prepare("SELECT * FROM book_requests WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $requests = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error loading dashboard.");
}

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>User Dashboard</h1>
    <a href="request_book.php"><button style="width: auto;">Request New Book</button></a>
</div>

<?php foreach($notifications as $notif): ?>
    <div class="alert alert-info">
        🎉 Your request for <strong><?= h($notif['book_title']) ?></strong> is now <strong><?= h(str_replace('_', ' ', $notif['status'])) ?></strong>.
    </div>
<?php endforeach; ?>

<div class="card">
    <h3>My Requests</h3>
    <table>
        <thead>
            <tr>
                <th>Book Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($requests)): ?>
                <tr><td colspan="4" style="text-align: center;">No requests found.</td></tr>
            <?php endif; ?>
            <?php foreach($requests as $req): ?>
                <tr>
                    <td><?= h($req['book_title']) ?></td>
                    <td><?= h($req['category']) ?></td>
                    <td>
                        <span style="padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; background: <?= $req['status'] == 'completed' ? '#dcfce7' : ($req['status'] == 'in_progress' ? '#fef3c7' : '#f1f5f9') ?>; color: <?= $req['status'] == 'completed' ? '#166534' : ($req['status'] == 'in_progress' ? '#92400e' : '#475569') ?>;">
                            <?= h(ucfirst(str_replace('_', ' ', $req['status']))) ?>
                        </span>
                    </td>
                    <td>
                        <?php if($req['status'] == 'pending'): ?>
                            <form action="cancel_request.php" method="POST" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                <button type="submit" class="btn-cancel" style="padding: 0.5rem; margin: 0; width: auto; font-size: 0.8rem;">Cancel</button>
                            </form>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
