<?php
require '../config/db.php';
require '../includes/auth.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category'])) {
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    // 1. Check Rate Limit (5 calls per 24 hours)
    try {
        $stmt = $pdo->prepare("SELECT call_count, last_call FROM api_usage WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $usage = $stmt->fetch();

        $now = new DateTime();
        if ($usage) {
            $last_call = new DateTime($usage['last_call']);
            $interval = $now->diff($last_call);
            
            // Reset if more than 24 hours
            if ($interval->h >= 24 || $interval->days >= 1) {
                $stmt = $pdo->prepare("UPDATE api_usage SET call_count = 1, last_call = CURRENT_TIMESTAMP WHERE user_id = ?");
                $stmt->execute([$user_id]);
            } else {
                if ($usage['call_count'] >= 5) {
                    echo json_encode(['error' => 'Rate limit exceeded. You can only fetch books 5 times per 24 hours.']);
                    exit();
                } else {
                    $stmt = $pdo->prepare("UPDATE api_usage SET call_count = call_count + 1, last_call = CURRENT_TIMESTAMP WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                }
            }
        } else {
            $stmt = $pdo->prepare("INSERT INTO api_usage (user_id, call_count, last_call) VALUES (?, 1, CURRENT_TIMESTAMP)");
            $stmt->execute([$user_id]);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error']);
        exit();
    }

    // 2. Fetch from Google Books API
    $queries = [
        'App Development' => 'web+development',
        'Mobile Development' => 'mobile+development',
        'AI' => 'artificial+intelligence'
    ];

    $query = $queries[$category] ?? 'technology';
    $apiUrl = "https://www.googleapis.com/books/v1/volumes?q=" . $query;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        echo json_encode(['error' => 'Failed to reach Google Books API']);
        exit();
    }

    $data = json_decode($response, true);
    $books = [];

    if (isset($data['items'])) {
        foreach ($data['items'] as $item) {
            $title = $item['volumeInfo']['title'] ?? 'Unknown Title';
            $authors = isset($item['volumeInfo']['authors']) ? implode(', ', $item['volumeInfo']['authors']) : 'Unknown Author';
            
            // 3. Silently insert into books table
            try {
                $stmt = $pdo->prepare("INSERT IGNORE INTO books (title, author, category) VALUES (?, ?, ?)");
                $stmt->execute([$title, $authors, $category]);
            } catch (PDOException $e) {
                // Silently ignore insert errors (unique key constraint)
            }
            
            $books[] = ['title' => $title, 'author' => $authors];
        }
    }

    echo json_encode(['books' => $books]);
    exit();
}
?>
