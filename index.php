<?php
/**
 * Home page: hero banner, featured tees grid and the quick-add modal.
 */
require_once __DIR__ . '/config/db.php';

// Featured: in-stock tees, limited with a bound integer parameter
$stmt = $pdo->prepare('SELECT * FROM products WHERE stock > 0 ORDER BY id LIMIT :limit');
$stmt->bindValue(':limit', 8, PDO::PARAM_INT);
$stmt->execute();
$featured = $stmt->fetchAll();

// Category list with a count for each, used for the shop-by-category row
$categories = $pdo->query('SELECT category, COUNT(*) AS total FROM products GROUP BY category ORDER BY category')->fetchAll();

$pageTitle = 'Shop';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container hero-inner">
        <h1 class="hero-title" data-text="Printed by hand. Worn to death.">Printed by hand. Worn to death.</h1>
        <div class="hero-side">
            <p class="lead">Heavy cotton tees, screen-printed in small batches on our own press. When a run sells out, it's gone.</p>
            <div class="hero-actions">
                <a href="products.php" class="btn">Shop all tees</a>
                <a href="contact.php" class="btn btn-outline">Request a custom print</a>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>This week's tees</h2>
            <a href="products.php" class="btn-link">View all <?= array_sum(array_column($categories, 'total')) ?> tees</a>
        </div>

        <div class="product-grid">
            <?php foreach ($featured as $p): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-tint">
    <div class="container">
        <h2>Shop by fit</h2>
        <ul class="category-list">
            <?php foreach ($categories as $c): ?>
                <li>
                    <a href="products.php?category=<?= urlencode($c['category']) ?>">
                        <span><?= e($c['category']) ?></span>
                        <span class="muted"><?= (int) $c['total'] ?> styles</span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php include __DIR__ . '/includes/quick-add-modal.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
