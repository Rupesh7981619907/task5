<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function esc($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token)
{
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

?>
