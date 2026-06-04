<?php
session_start();

if (!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ARCvhive &mdash; Register</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    html, body { display: flex; align-items: center; justify-content: center; }
    .register-wrapper { width: 900px; max-width: 95vw; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; overflow: hidden; box-shadow: 0 8px 40px rgba(0,0,0,0.08); }
    .register-grid { display: grid; grid-template-columns: 1fr 1fr; }
    .register-brand { padding: 52px 44px; background: var(--accent); color: #F5F2EC; }
    .register-content { padding: 52px 44px; }
    .register-content h1 { font-family: 'DM Serif Display', serif; font-size: 24px; margin-bottom: 6px; }
    .register-content .subtitle { font-size: 12px; color: var(--text-muted); margin-bottom: 24px; }
    @media (max-width: 780px) { .register-grid { grid-template-columns: 1fr; } .register-brand { display: none; } .register-content { padding: 36px 28px; } }
  </style>
</head>
<body>
  <div class="register-wrapper">
    <div class="register-grid">
      <div class="side-panel register-brand">
        <div>
          <div class="side-panel-logo">Blu<em>Archive</em></div>
          <div class="side-panel-tagline">Register a new researcher account</div>
        </div>
        <div class="side-panel-body">
          <h2>Create your student archive access.</h2>
          <p>Use the department selector and student number details to save your account into the archive list.</p>
        </div>
        <div class="side-panel-footer">ARCvhive</div>
      </div>
      <div class="register-content">
        <h1>Create account</h1>
        <p class="subtitle">Add your archive student credentials below.</p>
        <div class="inline-actions" style="margin-bottom: 16px;">
          <a class="btn-secondary" href="index.php">&larr; Back to Login</a>
        </div>
        <?php include 'pages/register.php'; ?>
      </div>
    </div>
  </div>
</body>
</html>
