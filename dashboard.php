<?php
require_once __DIR__ . '/../auth/check_auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

$db = getDB();
$stmt = $db->query('SELECT p.id, p.title, p.created_at, u.username, p.content FROM posts p JOIN users u ON p.author_id = u.id ORDER BY p.created_at DESC');
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalPosts = count($posts);
$recent = array_slice($posts, 0, 5);
$username = null;
if (!empty($_SESSION['user_id'])) {
	$uStmt = $db->prepare('SELECT username FROM users WHERE id = ?');
	$uStmt->execute([$_SESSION['user_id']]);
	$uRow = $uStmt->fetch(PDO::FETCH_ASSOC);
	$username = $uRow['username'] ?? null;
}
?>
<div class="card">
	<div class="admin-header">
		<div style="display:flex;align-items:center;gap:12px;">
				<div>
				<h1 style="margin:0;">Admin Dashboard</h1>
			</div>
			<div class="admin-controls" style="margin-left:auto;">
				<a class="btn btn-secondary" href="create.php">Create New Post</a>
				<div class="admin-search">
					<input id="admin-search-input" type="search" placeholder="Filter posts by title or author..." style="padding:.5rem;border-radius:8px;border:1px solid rgba(20,30,60,.06)">
				</div>
			</div>
		</div>

		<div class="stats-row">
			<div class="stat-card">
				<div class="num"><?=esc($totalPosts)?></div>
				<div class="muted">Total posts</div>
			</div>
			<div class="stat-card">
				<div class="num"><?=esc(count($recent))?></div>
				<div class="muted">Recent posts shown</div>
			</div>
		</div>
	</div>

	<?php if (empty($posts)): ?>
		<p>No posts yet.</p>
	<?php else: ?>
		<div style="overflow:auto;margin-top:12px;">
		<table class="admin-table">
			<thead>
			<tr><th>Title</th><th>Author</th><th>Created</th><th class="actions">Actions</th></tr>
			</thead>
			<tbody id="admin-posts-body">
			<?php foreach ($posts as $p): ?>
				<tr data-title="<?=esc(strtolower($p['title']))?>" data-author="<?=esc(strtolower($p['username']))?>">
					<td><?=esc($p['title'])?></td>
					<td><?=esc($p['username'])?></td>
					<td><?=esc($p['created_at'])?></td>
					<td class="actions">
						<div class="table-actions">
							<a class="btn btn-secondary" href="edit.php?id=<?=$p['id']?>">Edit</a>
							<form action="delete.php" method="post" style="display:inline">
								<input type="hidden" name="id" value="<?=$p['id']?>">
								<input type="hidden" name="csrf" value="<?=csrf_token()?>">
								<button type="submit" class="btn btn-danger" onclick="return confirm('Delete post?')">Delete</button>
							</form>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		</div>
	<?php endif; ?>
</div>

<script>
// Client-side filtering for admin table
(function(){
  const input = document.getElementById('admin-search-input');
  if (!input) return;
  const tbody = document.getElementById('admin-posts-body');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  input.addEventListener('input', (e) => {
	const q = e.target.value.trim().toLowerCase();
	rows.forEach(r => {
	  const title = r.dataset.title || '';
	  const author = r.dataset.author || '';
	  r.style.display = (q === '' || title.includes(q) || author.includes(q)) ? '' : 'none';
	});
  });
})();
</script>
<?php require_once __DIR__ . '/../includes/footer.php';
