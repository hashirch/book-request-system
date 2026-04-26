<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Request System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #1e293b;
            --accent: #f59e0b;
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #334155;
            --success: #10b981;
            --danger: #ef4444;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            line-height: 1.6;
        }
        nav {
            background-color: var(--secondary);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
        }
        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .card {
            background: var(--card);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        input, select, button {
            width: 100%;
            padding: 0.75rem;
            margin-top: 0.5rem;
            margin-bottom: 1.25rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            box-sizing: border-box;
        }
        button {
            background-color: var(--primary);
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        button:hover {
            opacity: 0.9;
        }
        .btn-cancel { background-color: var(--danger); }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .stat-box {
            padding: 1.5rem;
            text-align: center;
            border-radius: 8px;
            color: white;
        }
        .bg-blue { background-color: #3b82f6; }
        .bg-green { background-color: #10b981; }
        .bg-yellow { background-color: #f59e0b; }
        .bg-purple { background-color: #8b5cf6; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            text-align: left;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-info { background: #e0f2fe; color: #075985; }
    </style>
</head>
<body>
    <nav>
        <div><strong>BookRequest</strong></div>
        <div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <span>Welcome, <?= h($_SESSION['username']) ?></span>
                <a href="../logout.php">Logout</a>
            <?php else: ?>
                <a href="../user/login.php">Login</a>
                <a href="../user/register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container">
