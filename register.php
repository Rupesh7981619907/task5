<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

// Handle form submission before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf($_POST['csrf'] ?? '')) {
		die('Invalid CSRF token');
	}
	$username = trim($_POST['username'] ?? '');
	$password = $_POST['password'] ?? '';
	if ($username === '' || strlen($password) < 6) {
		$error = 'Username and password (6+ chars) required.';
	} else {
		$db = getDB();
		$stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
		$stmt->execute([$username]);
		if ($stmt->fetch()) {
			$error = 'Username already taken.';
		} else {
			$hash = password_hash($password, PASSWORD_DEFAULT);
			$ins = $db->prepare('INSERT INTO users (username, password) VALUES (?,?)');
			$ins->execute([$username, $hash]);
			header('Location: login.php');
			exit;
		}
	}
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="card centered">
	<h1>Register</h1>
	<?php if (!empty($error)): ?>
		<p style="color:red"><?=esc($error)?></p>
	<?php endif; ?>
	<form method="post" action="">
		<input type="hidden" name="csrf" value="<?=csrf_token()?>">
		<label>Username: <input name="username" placeholder="Enter username" required></label>
		<label>Password: <input name="password" type="password" placeholder="Choose a password (6+ chars)" required></label>
		<div style="margin-top:12px;"><button type="submit" class="btn btn-primary">Register</button></div>
	</form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php';
