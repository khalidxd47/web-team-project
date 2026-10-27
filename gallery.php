<?php
/**
 * Gallery page: slideshow (assets/js/slideshow.js) + photo grid of event images.
 */
require_once __DIR__ . '/config/db.php';

$stmt = $pdo->prepare('SELECT id, name, category, image_url FROM events ORDER BY event_date DESC');
$stmt->execute();
$photos = $stmt->fetchAll();

// The slideshow shows the 6 most recent photos
$slides = array_slice($photos, 0, 6);

$pageTitle = 'Gallery';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Gallery</h1>
        <p>Moments from our workshops, contests and gatherings</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($slides): ?>
            <!-- Slideshow -->
            <div class="slideshow" id="slideshow">
                <?php foreach ($slides as $i => $slide): ?>
                    <div class="slide <?= $i === 0 ? 'active' : '' ?>">
                        <img src="<?= e(str_replace('/800/500', '/1200/700', $slide['image_url'])) ?>" alt="<?= e($slide['name']) ?>">
                        <div class="slide-caption"><?= e($slide['name']) ?></div>
                    </div>
                <?php endforeach; ?>

                <button class="slide-btn prev" id="prevBtn" aria-label="Previous slide">&#10094;</button>
                <button class="slide-btn next" id="nextBtn" aria-label="Next slide">&#10095;</button>
                <div class="slide-dots" id="slideDots"></div>
            </div>
        <?php endif; ?>

        <h2 class="section-title">All Photos</h2>
        <p class="section-subtitle">Hover a photo to see the event name</p>

        <div class="gallery-grid">
            <?php foreach ($photos as $photo): ?>
                <a href="details.php?id=<?= (int) $photo['id'] ?>">
                    <figure class="gallery-item">
                        <img src="<?= e($photo['image_url']) ?>" alt="<?= e($photo['name']) ?>" loading="lazy">
                        <figcaption><?= e($photo['name']) ?></figcaption>
                    </figure>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script src="assets/js/slideshow.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
