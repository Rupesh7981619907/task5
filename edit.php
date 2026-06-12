<?php
require_once __DIR__ . '/../auth/check_auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
	die('Post not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!verify_csrf($_POST['csrf'] ?? '')) {
		die('Invalid CSRF token');
	}
	$title = trim($_POST['title'] ?? '');
	$content = trim($_POST['content'] ?? '');
	if ($title === '' || $content === '') {
		$error = 'Title and content required.';
	} else {
		$up = $db->prepare('UPDATE posts SET title = ?, content = ? WHERE id = ?');
		$up->execute([$title, $content, $id]);
		header('Location: dashboard.php');
		exit;
	}
}
?>
<div class="card">
	<h1>Edit Post</h1>
	<?php if (!empty($error)): ?><p style="color:red"><?=esc($error)?></p><?php endif; ?>
	<form method="post">
		<input type="hidden" name="csrf" value="<?=csrf_token()?>">
		<label>Title: <input name="title" value="<?=esc($post['title'])?>" required></label>
		<label>Content:<br><textarea name="content" rows="10" cols="60" required><?=esc($post['content'])?></textarea></label>
		<div style="margin-top:12px;"><button type="submit" class="btn btn-primary">Save</button></div>
	</form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
