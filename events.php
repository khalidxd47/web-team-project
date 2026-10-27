<?php
/**
 * Events page: all events from the database, filterable by category.
 * Clicking a card opens a modal filled by JavaScript (assets/js/main.js).
 */
require_once __DIR__ . '/config/db.php';

// Category list for the filter buttons
$categories = $pdo->query('SELECT DISTINCT category FROM events ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);

// Selected category from the URL (?category=Workshop); ignore unknown values
$selected = $_GET['category'] ?? '';
if (!in_array($selected, $categories, true)) {
    $selected = '';
}

if ($selected !== '') {
    $stmt = $pdo->prepare('SELECT * FROM events WHERE category = :category ORDER BY event_date ASC');
    $stmt->execute(['category' => $selected]);
} else {
    $stmt = $pdo->prepare('SELECT * FROM events ORDER BY event_date ASC');
    $stmt->execute();
}
$events = $stmt->fetchAll();

$pageTitle = 'Events';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Club Events</h1>
        <p>Workshops, competitions, seminars and more. Click a card for a quick look.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Category filter -->
        <div class="filter-bar">
            <a href="events.php" class="<?= $selected === '' ? 'active' : '' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="events.php?category=<?= urlencode($cat) ?>" class="<?= $selected === $cat ? 'active' : '' ?>">
                    <?= e($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($events)): ?>
            <p class="text-center">No events found.</p>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($events as $event): ?>
                    <!-- data-* attributes carry the event info to the modal -->
                    <article class="card clickable js-event-card"
                             data-id="<?= (int) $event['id'] ?>"
                             data-name="<?= e($event['name']) ?>"
                             data-description="<?= e($event['description']) ?>"
                             data-category="<?= e($event['category']) ?>"
                             data-date="<?= formatDate($event['event_date']) ?>"
                             data-location="<?= e($event['location']) ?>"
                             data-image="<?= e($event['image_url']) ?>">
                        <div class="card-img">
                            <img src="<?= e($event['image_url']) ?>" alt="<?= e($event['name']) ?>" loading="lazy">
                        </div>
                        <div class="card-body">
                            <span class="badge"><?= e($event['category']) ?></span>
                            <h3><?= e($event['name']) ?></h3>
                            <div class="card-meta">&#128197; <?= formatDate($event['event_date']) ?> &middot; &#128205; <?= e($event['location']) ?></div>
                            <p><?= e(excerpt($event['description'])) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Event modal (content is filled in by JavaScript) -->
<div class="modal" id="eventModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-content">
        <button class="modal-close" id="modalClose" aria-label="Close">&times;</button>
        <img id="modalImage" src="" alt="">
        <div class="modal-body">
            <span class="badge" id="modalCategory"></span>
            <h2 id="modalTitle"></h2>
            <div class="card-meta">&#128197; <span id="modalDate"></span> &middot; &#128205; <span id="modalLocation"></span></div>
            <p id="modalDescription"></p>
            <a href="#" id="modalLink" class="btn btn-sm" style="align-self: flex-start;">Full Details</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
