<?php
require_once __DIR__ . '/../auth/check_auth.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf($_POST['csrf'] ?? '')) {
		die('Invalid CSRF token');
	}
	$id = (int)($_POST['id'] ?? 0);
	$db = getDB();
	$del = $db->prepare('DELETE FROM posts WHERE id = ?');
	$del->execute([$id]);
}
header('Location: dashboard.php');
exit;
