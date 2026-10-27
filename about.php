<?php
/**
 * About page: mission, vision, values and the club board.
 */
$pageTitle = 'About';
require_once __DIR__ . '/includes/header.php';

// Board members (static data: no database needed for this page)
$board = [
    ['initials' => 'P',  'role' => 'President',            'task' => 'Leads the club and represents it to the college.'],
    ['initials' => 'VP', 'role' => 'Vice President',       'task' => 'Coordinates teams and supports the president.'],
    ['initials' => 'EM', 'role' => 'Events Manager',       'task' => 'Plans workshops, contests and hackathons.'],
    ['initials' => 'TL', 'role' => 'Technical Lead',       'task' => 'Runs training sessions and mentors members.'],
    ['initials' => 'MC', 'role' => 'Media & Comms',        'task' => 'Manages social media, photos and announcements.'],
    ['initials' => 'PR', 'role' => 'Partnerships',         'task' => 'Builds relationships with companies and sponsors.'],
];
?>

<section class="page-header">
    <div class="container">
        <h1>About the Club</h1>
        <p>Students helping students grow in computer science</p>
    </div>
</section>

<section class="section">
    <div class="container about-grid">
        <div class="panel">
            <h3>&#127919; Our Mission</h3>
            <p>To build a community where students learn practical skills beyond the classroom through hands-on workshops, teamwork and friendly competition.</p>
        </div>
        <div class="panel">
            <h3>&#128301; Our Vision</h3>
            <p>To be the leading student tech community at Umm Al-Qura University, preparing graduates for the Saudi digital economy under Vision 2030.</p>
        </div>
        <div class="panel">
            <h3>&#129309; Our Values</h3>
            <p>Curiosity, collaboration, integrity and sharing knowledge. Every member, at every level, is welcome to learn and to teach.</p>
        </div>
    </div>
</section>

<section class="section" style="background-color: var(--color-surface);">
    <div class="container">
        <h2 class="section-title">Meet the Board</h2>
        <p class="section-subtitle">The team behind this year's activities</p>

        <div class="card-grid">
            <?php foreach ($board as $member): ?>
                <div class="panel team-card">
                    <div class="avatar"><?= e($member['initials']) ?></div>
                    <h3><?= e($member['role']) ?></h3>
                    <p style="color: var(--color-text-muted);"><?= e($member['task']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
