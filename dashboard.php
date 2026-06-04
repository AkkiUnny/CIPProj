<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$page = $_GET['page'] ?? 'home';

$allowed = ['home', 'register', 'submit'];

if (!in_array($page, $allowed)) {
    $page = 'home';
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>ARCvhive Dashboard</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<style>
.wrap {
    width: 1260px;
    max-width: 98vw;
    margin: 40px auto;
    display: grid;
    grid-template-columns: 320px 1fr;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,.08);
}

.left {
    padding: 40px;
}

.right {
    padding: 36px;
    background: var(--bg);
}

.topcard {
    padding: 16px;
    margin-bottom: 20px;
}

.content {
    padding: 28px;
    min-height: 420px;
}

.library-shell {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 24px;
    align-items: start;
}

.library-sidebar {
    border: 1px solid var(--border);
    border-radius: 8px;
    background: rgba(255,255,255,0.45);
    padding: 16px;
}

.library-sidebar h3 {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--text-muted);
    margin-bottom: 10px;
}
.filter-label {
    display: block;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 6px;
}
.library-chip {
    display: block;
    border: 1px solid var(--border);
    border-radius: 999px;
    padding: 8px 10px;
    margin-bottom: 8px;
    background: rgba(255,255,255,0.65);
    color: var(--text);
    text-decoration: none;
    font-size: 11px;
}

.library-chip:hover {
    background: #fff;
}

.library-search {
    width: 100%;
    margin-top: 8px;
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 9px 10px;
    font-family: 'DM Mono', monospace;
    font-size: 12px;
    background: rgba(255,255,255,0.8);
}

.side-panel-footer {
    color: #fff;
    font-size: 12px;
    line-height: 1.6;
}
</style>

</head>

<body>

<div class="wrap">

    <div class="side-panel left">
        <div>
            <div class="side-panel-logo">BLUArchive</div>
            <div class="side-panel-tagline">Academic Research Collection</div>
        </div>

        <div class="side-panel-body">
            <h2>Welcome to BluArchive!</h2>
            <p>Research Collection Web for students, by students.</p>
        </div>

        <div class="side-panel-footer">
            Logged in as <?=htmlspecialchars($_SESSION['name'] ?? $_SESSION['user'])?><br>
            Student #: <?=htmlspecialchars($_SESSION['student_number'] ?? '')?><br>
            Dept: <?=htmlspecialchars($_SESSION['department'] ?? '')?>
        </div>
    </div>

    <div class="right">

        <div class="topcard card">
            <div class="user">Dashboard Navigation</div>

            <div class="nav">
                <a href="?page=home">Library</a>
                <a href="?page=submit">Submit</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="content card">
            <?php include __DIR__ . '/pages/' . $page . '.php'; ?>
        </div>

    </div>

</div>

</body>
</html>