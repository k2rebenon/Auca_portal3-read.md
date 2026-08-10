<?php
// Shared auth helpers: role checks + redirects.
require_once __DIR__ . '/../config.php';

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        header("Location: /auca-portal/auth/login.php");
        exit;
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['user']['role'] !== $role) {
        header("Location: /auca-portal/auth/login.php");
        exit;
    }
}
s
