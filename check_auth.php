<?php
// Simple auth check placeholder
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
// ensure helper functions are available
require_once __DIR__ . '/../includes/functions.php';

if (empty($_SESSION['user_id'])) {
	header('Location: ../auth/login.php');
	exit;
}
