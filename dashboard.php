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
    min-height: 620px;
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

/* ── Logout button (outside .wrap, bottom-left) ── */
.logout-area {
    width: 1260px;
    max-width: 98vw;
    margin: 0 auto 40px;
    display: flex;
    justify-content: flex-start;
}

.btn-logout {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 18px;
    background: #c0392b;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-family: 'DM Mono', monospace;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    letter-spacing: 0.04em;
    transition: background 0.15s;
}

.btn-logout:hover {
    background: #a93226;
}

/* ── Confirmation modal ── */
.logout-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.35);
    z-index: 999;
    align-items: center;
    justify-content: center;
}

.logout-overlay.active {
    display: flex;
}

.logout-modal {
    background: #fff;
    border-radius: 10px;
    padding: 32px 28px 24px;
    width: 320px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    text-align: center;
}

.logout-modal h3 {
    font-family: 'DM Serif Display', serif;
    font-size: 18px;
    margin: 0 0 8px;
    color: #1a1a1a;
}

.logout-modal p {
    font-family: 'DM Mono', monospace;
    font-size: 12px;
    color: #666;
    margin: 0 0 24px;
}

.modal-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.btn-cancel {
    padding: 8px 20px;
    background: transparent;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-family: 'DM Mono', monospace;
    font-size: 12px;
    cursor: pointer;
    color: #444;
}

.btn-cancel:hover {
    background: #f5f5f5;
}

.btn-confirm-logout {
    padding: 8px 20px;
    background: #c0392b;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-family: 'DM Mono', monospace;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
}

.btn-confirm-logout:hover {
    background: #a93226;
}
</style>

</head>

<body>

<!-- Confirmation modal (outside everything) -->
<div class="logout-overlay" id="logoutOverlay">
    <div class="logout-modal">
        <h3>Log out?</h3>
        <p>You'll be returned to the login page.</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeLogout()">Cancel</button>
            <a href="logout.php" class="btn-confirm-logout">Yes, log out</a>
        </div>
    </div>
</div>

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
            </div>
        </div>

        <div class="content card">
            <?php include __DIR__ . '/pages/' . $page . '.php'; ?>
        </div>

    </div>

    <div id="bananaOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; flex-direction:column;">
    <div class="tenor-gif-embed" data-postid="16699030" data-share-method="host" data-aspect-ratio="1" data-width="300px">
        <a href="https://tenor.com/view/banana-meme-ligma-when-the-gif-16699030">Banana Meme GIF</a>
    </div>
    <button onclick="document.getElementById('bananaOverlay').style.display='none'" 
            style="margin-top:16px; padding:8px 20px; background:#fff; border:none; border-radius:6px; font-family:'DM Mono',monospace; font-size:12px; cursor:pointer;">
        close
    </button>
</div>
<script async src="https://tenor.com/embed.js"></script>

</div>

<!-- changed the logout button placementc -->
<div class="logout-area">
    <button class="btn-logout" onclick="openLogout()">&#x2192; Log out</button>
</div>

<script>
function openLogout() {
    document.getElementById('logoutOverlay').classList.add('active');
}
function closeLogout() {
    document.getElementById('logoutOverlay').classList.remove('active');
}
document.getElementById('logoutOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeLogout();
});

let logoClicks = 0;
document.querySelector('.side-panel-logo').addEventListener('click', function() {
    logoClicks++;
    if (logoClicks >= 5) {
        logoClicks = 0;
        const overlay = document.getElementById('bananaOverlay');
        overlay.style.display = 'flex';
    }
});

document.getElementById('bananaOverlay').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>

</body>
</html>