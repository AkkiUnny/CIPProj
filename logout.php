<?php
session_start();

if (isset($_COOKIE['login_cookie'])) {
    setcookie('login_cookie', '', time() - 3600, '/');
}

unset($_COOKIE['login_cookie']);
session_unset();
session_destroy();

header('Location: index.php');
exit;
