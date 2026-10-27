<?php
/**
 * Event details page: loads ONE event by id using a prepared statement.
 * URL example: details.php?id=3
 */
require_once __DIR__ . '/config/db.php';

// Accept only a positive integer id; anything else becomes false/null
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$event = false;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $event = $stmt->fetch();
}

// Unknown id: send a real 404 status
if (!$event) {
    http_response_code(404);
    $pageTitle = 'Event Not Found';
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="section text-center"><div class="container">
            <h1>Event not found</h1>
            <p style="margin: 1rem 0 2rem;">The event you are looking for does not exist or was removed.</p>
            <a href="events.php" class="btn">Back to Events</a>
          </div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Up to 3 other events in the same category
$stmt = $pdo->prepare('SELECT * FROM events WHERE category = :category AND id <> :id ORDER BY event_date LIMIT 3');
$stmt->execute(['category' => $event['category'], 'id' => $event['id']]);
$related = $stmt->fetchAll();

$isUpcoming = $event['event_date'] >= date('Y-m-d');

$pageTitle = $event['name'];
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><?= e($event['name']) ?></h1>
        <p><?= e($event['category']) ?> &middot; <?= formatDate($event['event_date']) ?></p>
    </div>
</section>

<section class="section">
    <div class="container details">
        <img class="details-img" src="<?= e($event['image_url']) ?>" alt="<?= e($event['name']) ?>">

        <div class="panel">
            <span class="badge"><?= e($event['category']) ?></span>
            <ul class="info-list">
                <li><strong>Date</strong><?= formatDate($event['event_date']) ?></li>
                <li><strong>Location</strong><?= e($event['location']) ?></li>
                <li>
                    <strong>Status</strong>
                    <span style="color: <?= $isUpcoming ? 'var(--color-success)' : 'var(--color-text-muted)' ?>; font-weight: 600;">
                        <?= $isUpcoming ? 'Upcoming' : 'Finished' ?>
                    </span>
                </li>
            </ul>
            <p style="margin-bottom: 1.5rem;"><?= nl2br(e($event['description'])) ?></p>
            <?php if ($isUpcoming): ?>
                <a href="contact.php?event=<?= urlencode($event['name']) ?>" class="btn">Register Interest</a>
            <?php endif; ?>
            <a href="events.php" class="btn" style="background: transparent; color: var(--color-primary);">&larr; All Events</a>
        </div>
    </div>
</section>

<?php if ($related): ?>
<section class="section" style="padding-top: 0;">
    <div class="container">
        <h2 class="section-title">More <?= e($event['category']) ?> Events</h2>
        <p class="section-subtitle">You might also like</p>
        <div class="card-grid">
            <?php foreach ($related as $item): ?>
                <article class="card">
                    <div class="card-img">
                        <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>" loading="lazy">
                    </div>
                    <div class="card-body">
                        <h3><?= e($item['name']) ?></h3>
                        <div class="card-meta">&#128197; <?= formatDate($item['event_date']) ?></div>
                        <a href="details.php?id=<?= (int) $item['id'] ?>" class="btn btn-sm">View Details</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
