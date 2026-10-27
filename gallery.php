<?php
/**
 * Lookbook: slideshow (Next/Prev, dots, auto-play) and a grid of every tee.
 */
require_once __DIR__ . '/config/db.php';

// One slide per category: the first in-stock tee of each
$slides = $pdo->query(
    'SELECT p.* FROM products p
     JOIN (SELECT MIN(id) AS id FROM products WHERE stock > 0 GROUP BY category) f ON f.id = p.id
     ORDER BY p.category'
)->fetchAll();

$all = $pdo->query('SELECT id, title, image_url, category FROM products ORDER BY category, title')->fetchAll();

$pageTitle = 'Lookbook';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Lookbook</h1>
        <p class="lead">One tee from each fit in the current drop. Use the arrows or your keyboard.</p>

        <div class="slideshow" id="slideshow" aria-roledescription="carousel" aria-label="Lookbook">
            <?php foreach ($slides as $i => $s): ?>
                <figure class="slide<?= $i === 0 ? ' active' : '' ?>">
                    <img src="<?= e($s['image_url']) ?>" alt="<?= e($s['title']) ?>">
                    <figcaption>
                        <span class="slide-fit"><?= e($s['category']) ?></span>
                        <a href="details.php?id=<?= (int) $s['id'] ?>"><?= e($s['title']) ?></a>
                        <span class="price"><?= price($s['price']) ?></span>
                    </figcaption>
                </figure>
            <?php endforeach; ?>

            <button type="button" class="slide-btn prev" id="prevBtn" aria-label="Previous look">&#8249;</button>
            <button type="button" class="slide-btn next" id="nextBtn" aria-label="Next look">&#8250;</button>
            <div class="dots" id="slideDots"></div>
        </div>
    </div>
</section>

<section class="section section-tint">
    <div class="container">
        <h2>Every tee</h2>
        <div class="gallery-grid">
            <?php foreach ($all as $p): ?>
                <a href="details.php?id=<?= (int) $p['id'] ?>" class="gallery-item">
                    <img src="<?= e($p['image_url']) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                    <span class="gallery-caption"><?= e($p['title']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script src="assets/js/slideshow.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
