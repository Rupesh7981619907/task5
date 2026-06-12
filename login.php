<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

// Process login before outputting headers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf($_POST['csrf'] ?? '')) {
		die('Invalid CSRF token');
	}
	$username = trim($_POST['username'] ?? '');
	$password = $_POST['password'] ?? '';
	$db = getDB();
	$stmt = $db->prepare('SELECT id, password FROM users WHERE username = ?');
	$stmt->execute([$username]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($user && password_verify($password, $user['password'])) {
		session_regenerate_id(true);
		$_SESSION['user_id'] = $user['id'];
		header('Location: ../admin/dashboard.php');
		exit;
	} else {
		$error = 'Invalid credentials';
	}
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="card centered">
	<h1>Login</h1>
	<?php if (!empty($error)): ?>
		<p style="color:red"><?=esc($error)?></p>
	<?php endif; ?>
	<form method="post" action="">
		<input type="hidden" name="csrf" value="<?=csrf_token()?>">
		<label>Username: <input name="username" placeholder="Enter username" required></label>
		<label>Password: <input name="password" type="password" placeholder="Enter password" required></label>
		<div style="margin-top:12px;"><button type="submit" class="btn btn-primary">Login</button></div>
	</form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php';
