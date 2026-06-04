<?php
session_start();
// if (!isset($_SESSION['user'])) { header('Location: index.php'); exit; }
// $page=$_GET['page'] ?? 'home';
// $allowed=['home','register','submit','departments','search'];
// if(!in_array($page,$allowed)) $page='home';
?>

<!DOCTYPE html><html><head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ARCvhive Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
    /* dashboard.php — page-specific styles only */
    .wrap {
        width: 1100px;
        max-width: 95vw;
        margin: 40px auto;
        display: grid;
        grid-template-columns: 320px 1fr;
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 8px 40px rgba(0,0,0,.08);
        position: relative;
        z-index: 1;
    }

    .left {
        padding: 40px;
        justify-content: space-between;
    }

    .right {
        padding: 32px;
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

    .user {
        font-size: 12px;
        color: var(--text-muted);
    }

    .side-panel-footer {
        color: #ffffff;
        opacity: 1;
        font-size: 12px;
        line-height: 1.6;
        text-transform: none;
        letter-spacing: normal;
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
            <div class="side-panel-footer">Logged in as <?=htmlspecialchars($_SESSION['name'] ?: $_SESSION['user'])?><br>Student #: <?=htmlspecialchars($_SESSION['student_number'] ?? '')?><br>Dept: <?=htmlspecialchars($_SESSION['department'] ?? '')?></div>
        </div>
        <div class="right">
            <div class="topcard card">
                <div class="user">Dashboard Navigation</div>
                <div class="nav">
                    <a href="?page=home">Home</a>
                    <a href="?page=submit">Submit</a>
                    <a href="?page=departments">Departments</a>
                    <a href="?page=search">Search</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
            <div class="content card"><?php include __DIR__.'/pages/'.$page.'.php'; ?></div>
        </div>
    </div>
</body>

</html>