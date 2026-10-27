<?php
/**
 * Home page: hero section, club statistics, latest events, and embedded map.
 */
require_once __DIR__ . '/config/db.php';

// ---------- Club statistics ----------
// Fixed query with no user input, so query() is enough here
$stats = $pdo->query(
    'SELECT COUNT(*) AS total_events,
            COUNT(DISTINCT category) AS total_categories,
            SUM(event_date >= CURDATE()) AS upcoming_events
     FROM events'
)->fetch();

// ---------- Latest club updates: the next 3 upcoming events ----------
$limit = 3;
$stmt = $pdo->prepare(
    'SELECT id, name, description, category, event_date, location, image_url
     FROM events
     WHERE event_date >= CURDATE()
     ORDER BY event_date ASC
     LIMIT :limit'
);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // LIMIT must be bound as an integer
$stmt->execute();
$latestEvents = $stmt->fetchAll();

// If there are no upcoming events, show the most recent past ones instead
if (empty($latestEvents)) {
    $stmt = $pdo->prepare(
        'SELECT id, name, description, category, event_date, location, image_url
         FROM events
         ORDER BY event_date DESC
         LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $latestEvents = $stmt->fetchAll();
}

$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="hero">
    <div class="container">
        <h1>Code. Build. <span>Compete.</span></h1>
        <p>
            Welcome to the Computer Science Club at Umm Al-Qura University. Join workshops,
            hackathons, and seminars that turn classroom knowledge into real-world skills.
        </p>
        <div class="hero-actions">
            <a href="events.php" class="btn">Explore Events</a>
            <a href="contact.php" class="btn btn-outline">Join the Club</a>
        </div>
    </div>
</section>

<!-- ===================== STATS ===================== -->
<section class="container">
    <div class="stats">
        <div class="stat-box">
            <div class="stat-number"><?= (int) $stats['total_events'] ?></div>
            <div class="stat-label">Events This Year</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?= (int) $stats['upcoming_events'] ?></div>
            <div class="stat-label">Upcoming Events</div>
        </div>
        <div class="stat-box">
            <div class="stat-number"><?= (int) $stats['total_categories'] ?></div>
            <div class="stat-label">Activity Types</div>
        </div>
    </div>
</section>

<!-- ===================== LATEST UPDATES ===================== -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Latest Club Updates</h2>
        <p class="section-subtitle">Don't miss our upcoming activities</p>

        <?php if (empty($latestEvents)): ?>
            <p class="text-center">No events yet. Check back soon!</p>
        <?php else: ?>
            <div class="card-grid">
                <?php foreach ($latestEvents as $event): ?>
                    <article class="card">
                        <div class="card-img">
                            <img src="<?= e($event['image_url']) ?>" alt="<?= e($event['name']) ?>" loading="lazy">
                        </div>
                        <div class="card-body">
                            <span class="badge"><?= e($event['category']) ?></span>
                            <h3><?= e($event['name']) ?></h3>
                            <div class="card-meta">
                                &#128197; <?= formatDate($event['event_date']) ?>
                                &nbsp;&middot;&nbsp;
                                &#128205; <?= e($event['location']) ?>
                            </div>
                            <p><?= e(excerpt($event['description'])) ?></p>
                            <a href="details.php?id=<?= (int) $event['id'] ?>" class="btn btn-sm">View Details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p class="text-center" style="margin-top: 2.5rem;">
            <a href="events.php" class="btn">View All Events</a>
        </p>
    </div>
</section>

<!-- ===================== EMBEDDED MEDIA ===================== -->
<section class="section" style="background-color: var(--color-surface);">
    <div class="container media-grid">
        <div class="media-text">
            <h2>Find Us on Campus</h2>
            <p>
                Most of our workshops take place in the computer labs of the College of Computing
                at Umm Al-Qura University in Makkah. Everyone is welcome, from first-year students
                to seniors, whatever your programming level.
            </p>
            <p>Have a question or want to propose an event? We'd love to hear from you.</p>
            <a href="contact.php" class="btn">Contact Us</a>
        </div>

        <!-- Embedded Google Map (iframe) -->
        <div class="media-frame">
            <iframe
                src="https://maps.google.com/maps?q=Umm%20Al-Qura%20University%2C%20Makkah&z=14&output=embed"
                title="Map of Umm Al-Qura University"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen>
            </iframe>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
