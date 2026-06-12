<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/database.php';

$q = trim($_GET['q'] ?? '');
$db = getDB();
$posts = [];
if ($q !== '') {
    $stmt = $db->prepare("SELECT p.id, p.title, p.content, u.username, p.created_at FROM posts p JOIN users u ON p.author_id=u.id WHERE p.title LIKE ? OR p.content LIKE ? ORDER BY p.created_at DESC");
    $like = '%'.$q.'%';
    $stmt->execute([$like, $like]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<h1>Search</h1>
<form method="get">
    <input name="q" value="<?=esc($q)?>" placeholder="Search posts">
    <button type="submit">Search</button>
</form>
<?php if ($q === ''): ?>
    <p>Enter keywords to search posts.</p>
<?php else: ?>
    <?php if (empty($posts)): ?>
        <p>No results for <?=esc($q)?></p>
    <?php else: ?>
        <?php foreach ($posts as $p): ?>
            <article>
                <h2><?=esc($p['title'])?></h2>
                <p><?=nl2br(esc(substr($p['content'],0,300)))?>...</p>
                <p><small>By <?=esc($p['username'])?> on <?=esc($p['created_at'])?></small></p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php';
