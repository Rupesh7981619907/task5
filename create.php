<?php
require_once __DIR__ . '/../auth/check_auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf($_POST['csrf'] ?? '')) {
		die('Invalid CSRF token');
	}
	$title = trim($_POST['title'] ?? '');
	$content = trim($_POST['content'] ?? '');
	if ($title === '' || $content === '') {
		$error = 'Title and content required.';
	} else {
		$db = getDB();
		$stmt = $db->prepare('INSERT INTO posts (title, content, author_id) VALUES (?,?,?)');
		$stmt->execute([$title, $content, $_SESSION['user_id']]);
		header('Location: dashboard.php');
		exit;
	}
}
?>
<div class="card">
	<h1>Create Post</h1>
	<?php if (!empty($error)): ?><p style="color:red"><?=esc($error)?></p><?php endif; ?>
	<form method="post" action="">
		<input type="hidden" name="csrf" value="<?=csrf_token()?>">
		<label>Title: <input name="title" required></label>
		<label>Content:<br><textarea name="content" rows="10" cols="60" required></textarea></label>
		<div style="margin-top:12px;"><button type="submit" class="btn btn-primary">Create</button></div>
	</form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
