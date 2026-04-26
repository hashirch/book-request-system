<?php
require '../config/db.php';
require '../includes/auth.php';
check_auth(['user']);

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_request'])) {
    $title = trim($_POST['book_title']);
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    if (!empty($title) && !empty($category)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO book_requests (user_id, book_title, category, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$user_id, $title, $category]);
            $message = "Request submitted successfully!";
        } catch (PDOException $e) {
            $message = "Error submitting request.";
        }
    }
}

include '../includes/header.php';
?>

<div class="card">
    <h2>Request a Book</h2>
    <?php if($message): ?>
        <div class="alert alert-success"><?= h($message) ?></div>
    <?php endif; ?>

    <form method="POST" id="requestForm">
        <label>Username</label>
        <input type="text" value="<?= h($_SESSION['username']) ?>" readonly>

        <label>Email Address</label>
        <input type="text" value="<?= h($_SESSION['email']) ?>" readonly>

        <label>Category</label>
        <select name="category" id="categorySelect" required>
            <option value="">-- Select Category --</option>
            <option value="App Development">App Development</option>
            <option value="Mobile Development">Mobile Development</option>
            <option value="AI">AI</option>
        </select>

        <label>Book Title</label>
        <select name="book_title" id="bookSelect" required disabled>
            <option value="">-- Select a Book --</option>
        </select>

        <button type="submit" name="submit_request" id="submitBtn" disabled>Submit Request</button>
    </form>
</div>

<script>
document.getElementById('categorySelect').addEventListener('change', function() {
    const category = this.value;
    const bookSelect = document.getElementById('bookSelect');
    const submitBtn = document.getElementById('submitBtn');

    if (!category) {
        bookSelect.disabled = true;
        submitBtn.disabled = true;
        return;
    }

    // Show loading
    bookSelect.innerHTML = '<option value="">Loading books...</option>';
    bookSelect.disabled = true;

    const formData = new FormData();
    formData.append('category', category);

    fetch('../api/fetch_books.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            bookSelect.innerHTML = '<option value="">' + data.error + '</option>';
        } else {
            bookSelect.innerHTML = '<option value="">-- Select a Book --</option>';
            data.books.forEach(book => {
                const option = document.createElement('option');
                option.value = book.title;
                option.textContent = book.title + ' (by ' + book.author + ')';
                bookSelect.appendChild(option);
            });
            bookSelect.disabled = false;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to fetch books.');
    });
});
</script>

<?php include '../includes/footer.php'; ?>
