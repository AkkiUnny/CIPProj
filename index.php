<?php
session_start();

// start the login session for this page

function getUsers() {

    // read saved users from the text file
    $users = [];

    if (file_exists("userlist.txt")) {

        $lines = file("userlist.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        for ($i = 0; $i < count($lines); $i += 5) {

            $users[] = [
                "username" => $lines[$i],
                "password" => $lines[$i + 1],
                "name" => $lines[$i + 2],
                "student_number" => $lines[$i + 3],
                "department" => $lines[$i + 4]
            ];
        }
    }

    return $users;
}

function loginUser($user) {

    // store the logged-in user details in the session
    $_SESSION['user'] = $user['username'];
    $_SESSION['password'] = $user['password'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['student_number'] = $user['student_number'];
    $_SESSION['department'] = $user['department'];
}

// if the user is already signed in, go to the dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

// try to use the remember-me cookie to sign in again
if (isset($_COOKIE['login_cookie'])) {

    foreach (getUsers() as $user) {

        if ($user['student_number'] == $_COOKIE['login_cookie']) {

            loginUser($user);

            header("Location: dashboard.php");
            exit();
        }
    }
}

$error = "";

// handle the login form when the user submits it
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    foreach (getUsers() as $user) {

        if ($user['username'] == $username &&
            $user['password'] == $password) {

            loginUser($user);

            if (isset($_POST['remember'])) {

                setcookie(
                    "login_cookie",
                    $user['student_number'],
                    time() + 999999,
                    "/"
                );

            } else {

                setcookie(
                    "login_cookie",
                    "",
                    time() - 99999,
                    "/"
                );
            }

            header("Location: dashboard.php");
            exit();
        }
    }

    $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ARCvhive &mdash; Sign In</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    /* index.php — page-specific styles only */
    html, body {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-wrapper {
      display: grid;
      grid-template-columns: 1fr 1fr;
      width: 820px;
      max-width: 95vw;
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 8px 40px rgba(0,0,0,0.08);
      position: relative;
      z-index: 1;
    }

    /* Left panel — overrides shared side-panel padding for login layout */
    .login-brand {
      padding: 52px 44px;
      justify-content: space-between;
    }

    /* Right panel */
    .login-form-panel {
      padding: 52px 44px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .login-form-panel h1 {
      font-family: 'DM Serif Display', serif;
      font-size: 24px;
      color: var(--text);
      letter-spacing: -0.02em;
      margin-bottom: 6px;
    }

    .login-form-panel .subtitle {
      font-size: 12px;
      color: var(--text-muted);
      margin-bottom: 36px;
    }

    .demo-hint {
      margin-top: 24px;
      padding-top: 24px;
      border-top: 1px solid var(--border);
      font-size: 11px;
      color: var(--text-muted);
      line-height: 1.7;
    }

    .demo-hint strong { color: var(--text); }

    @media (max-width: 640px) {
      .login-wrapper { grid-template-columns: 1fr; }
      .login-brand { display: none; }
      .login-form-panel { padding: 36px 28px; }
    }
  </style>
</head>

<body>
  <div class="login-wrapper">
    <div class="side-panel login-brand">
      <div>
        <div class="side-panel-logo">Blu<em>Archive</em></div>
        <div class="side-panel-tagline">Research Paper Archive</div>
      </div>
      <div class="side-panel-body">
        <h2>Research made for students, by students.</h2>
        <p>A centralized repository for academic research papers, organized by department and searchable by author or title.</p>
      </div>
      <div class="side-panel-footer">Yigglety doo</div>
    </div>

    <div class="login-form-panel">
      <h1>Welcome back.</h1>
      <p class="subtitle">Sign in to access the archive.</p>

      <?php if (!empty($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="index.php">
        <div class="form-row">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Username" autocomplete="username"
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required />
        </div>
        <div class="form-row">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Password" autocomplete="current-password" required />
        </div>
        <div class="checkbox-row">
          <input type="checkbox" id="remember" name="remember" value="1" />
          <label for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn-primary">Sign In &rarr;</button>
      </form>

      <div class="inline-actions">
        <a class="btn-secondary" href="register.php">Register</a>
      </div>

      <div class="demo-hint">
        <strong>Registered accounts:</strong><br>
        Use your saved username and password from the register page.
      </div>
    </div>
  </div>
</body>

</html>