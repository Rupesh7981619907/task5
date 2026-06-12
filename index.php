<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/database.php';

$db = getDB();
$perPage = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$stmt = $db->prepare('SELECT p.id, p.title, p.content, p.created_at, u.username FROM posts p JOIN users u ON p.author_id = u.id ORDER BY p.created_at DESC LIMIT ? OFFSET ?');
$stmt->bindValue(1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count = $db->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$totalPages = max(1, ceil($count / $perPage));
?>
<section class="card centered hero">
    <h1 style="margin:.25rem 0 .5rem;">Final Blog Project</h1>
    <p class="muted" style="max-width:720px">A clean, modern PHP blog — read, search and enjoy curated posts.</p>
    <div style="width:100%;max-width:640px;margin-top:12px;">
        <input id="search" type="search" placeholder="Search posts by title or content..." />
    </div>
</section>

<?php if (empty($posts)): ?>
        <div class="card">
                <p>No posts found.</p>
        </div>
<?php else: ?>
        <?php $first = array_shift($posts); ?>
        <!-- Featured post -->
        <article class="card featured-card" data-title="<?=esc($first['title'])?>" data-content="<?=esc($first['content'])?>">
                <h2 style="font-size:1.6rem;"><?=esc($first['title'])?></h2>
                <p class="muted">By <?=esc($first['username'])?> on <?=esc($first['created_at'])?></p>
                <div class="post-body">
                        <p><?=nl2br(esc(substr($first['content'], 0, 500)))?>... </p>
                </div>
                <div style="margin-top:10px;"><button class="btn btn-primary btn-toggle">Read more</button></div>
        </article>

        <!-- Posts grid -->
        <div class="posts-grid container-wide">
                <?php foreach ($posts as $p): ?>
                        <article class="card post-card" data-title="<?=esc($p['title'])?>" data-content="<?=esc($p['content'])?>">
                                <h3><?=esc($p['title'])?></h3>
                                <p class="muted">By <?=esc($p['username'])?> on <?=esc($p['created_at'])?></p>
                                <div class="post-body">
                                        <p><?=nl2br(esc(substr($p['content'], 0, 200)))?>...</p>
                                </div>
                                <div style="margin-top:8px;">
                                        <button class="btn btn-outline btn-toggle">Read more</button>
                                </div>
                        </article>
                <?php endforeach; ?>
        </div>

        <nav style="margin-top:10px; display:flex;gap:8px;">
                <?php if ($page > 1): ?><a class="btn btn-outline" href="?page=<?=$page-1?>">Previous</a><?php endif; ?>
                <?php if ($page < $totalPages): ?><a class="btn btn-outline" href="?page=<?=$page+1?>">Next</a><?php endif; ?>
        </nav>
<?php endif; ?>

<script>
// Live client-side search and read-more toggles
(() => {
    const qs = (s, root=document) => root.querySelector(s);
    const qsa = (s, root=document) => Array.from(root.querySelectorAll(s));

    const search = qs('#search');
    if (search) {
        search.addEventListener('input', (e) => {
            const q = e.target.value.trim().toLowerCase();
            const cards = qsa('.post-card, .featured-card');
            cards.forEach(card => {
                const title = card.dataset.title.toLowerCase();
                const content = card.dataset.content.toLowerCase();
                const show = q === '' || title.includes(q) || content.includes(q);
                card.style.display = show ? '' : 'none';
            });
        });
    }

    // Toggle full content
    qsa('.btn-toggle').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const card = e.target.closest('.card');
            const body = card.querySelector('.post-body');
            const expanded = card.classList.toggle('expanded');
            if (expanded) {
                body.innerHTML = '<p>' + card.dataset.content.replace(/\n/g,'<br/>') + '</p>';
                e.target.textContent = 'Show less';
            } else {
                // collapse to excerpt length depending on featured or not
                const full = card.dataset.content;
                const len = card.classList.contains('featured-card') ? 500 : 200;
                body.innerHTML = '<p>' + (full.length > len ? full.substr(0,len) + '...' : full).replace(/\n/g,'<br/>') + '</p>';
                e.target.textContent = 'Read more';
            }
        });
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php';
