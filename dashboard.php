<?php
/**
 * Dashboard: full CRUD for the events table + live search.
 *
 *   Create  -> POST action=create
 *   Read    -> table below + live search (GET ?ajax=1&q=...)
 *   Update  -> GET ?edit=ID fills the form, then POST action=update
 *   Delete  -> POST action=delete
 *
 * All queries use PDO prepared statements.
 */
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$categories = ['Workshop', 'Seminar', 'Competition', 'Hackathon', 'Bootcamp', 'Social'];

// CSRF token: a random secret that every form must send back, so other sites can't submit forms here
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf'];

// ---------- LIVE SEARCH (called by JavaScript, returns JSON) ----------
if (isset($_GET['ajax'])) {
    $term = '%' . trim($_GET['q'] ?? '') . '%';
    $stmt = $pdo->prepare(
        'SELECT id, name, category, event_date, location FROM events
         WHERE name LIKE :t1 OR category LIKE :t2 OR location LIKE :t3
         ORDER BY event_date DESC'
    );
    $stmt->execute(['t1' => $term, 't2' => $term, 't3' => $term]);

    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll());
    exit;
}

$errors = [];
$form = ['id' => '', 'name' => '', 'description' => '', 'category' => '', 'event_date' => '', 'location' => '', 'image_url' => ''];

// ---------- CREATE / UPDATE / DELETE (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Invalid request.');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM events WHERE id = :id');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        $_SESSION['flash'] = 'Event deleted successfully.';
        header('Location: dashboard.php');
        exit;
    }

    // Collect and trim the submitted values
    foreach ($form as $field => $_) {
        $form[$field] = trim($_POST[$field] ?? '');
    }

    // Server-side validation
    if ($form['name'] === '' || mb_strlen($form['name']) > 150) {
        $errors[] = 'Name is required (max 150 characters).';
    }
    if (mb_strlen($form['description']) < 10) {
        $errors[] = 'Description must be at least 10 characters.';
    }
    if (!in_array($form['category'], $categories, true)) {
        $errors[] = 'Please choose a valid category.';
    }
    $date = DateTime::createFromFormat('Y-m-d', $form['event_date']);
    if (!$date || $date->format('Y-m-d') !== $form['event_date']) {
        $errors[] = 'Please enter a valid date.';
    }
    if ($form['location'] === '' || mb_strlen($form['location']) > 150) {
        $errors[] = 'Location is required (max 150 characters).';
    }
    if ($form['image_url'] === '' || mb_strlen($form['image_url']) > 255) {
        $errors[] = 'Image URL is required (max 255 characters).';
    }

    if (!$errors) {
        $params = [
            'name'        => $form['name'],
            'description' => $form['description'],
            'category'    => $form['category'],
            'event_date'  => $form['event_date'],
            'location'    => $form['location'],
            'image_url'   => $form['image_url'],
        ];

        if ($action === 'update') {
            $params['id'] = (int) $form['id'];
            $stmt = $pdo->prepare(
                'UPDATE events SET name = :name, description = :description, category = :category,
                        event_date = :event_date, location = :location, image_url = :image_url
                 WHERE id = :id'
            );
            $_SESSION['flash'] = 'Event updated successfully.';
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO events (name, description, category, event_date, location, image_url)
                 VALUES (:name, :description, :category, :event_date, :location, :image_url)'
            );
            $_SESSION['flash'] = 'Event added successfully.';
        }
        $stmt->execute($params);

        // Post/Redirect/Get: refreshing the page won't submit the form twice
        header('Location: dashboard.php');
        exit;
    }
}

// ---------- Load an event into the form for editing ----------
if (!$errors && isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $form = $stmt->fetch() ?: $form;
}
$isEdit = $form['id'] !== '' && $form['id'] !== null;

// ---------- READ: all events for the table ----------
$events = $pdo->query('SELECT id, name, category, event_date, location FROM events ORDER BY event_date DESC')->fetchAll();

// One-time success message after a redirect
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Events Dashboard</h1>
        <p>Add, edit, delete and search club events</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ($flash): ?>
            <div class="alert alert-success"><?= e($flash) ?></div>
        <?php endif; ?>
        <?php if ($errors): ?>
            <div class="alert alert-error"><?= implode('<br>', array_map('e', $errors)) ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <!-- ===== Create / Update form ===== -->
            <div class="panel">
                <h2 style="margin-bottom: 1rem;"><?= $isEdit ? 'Edit Event' : 'Add New Event' ?></h2>

                <form method="post" action="dashboard.php">
                    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'create' ?>">
                    <input type="hidden" name="id" value="<?= e((string) $form['id']) ?>">

                    <div class="form-grid">
                        <div class="form-group full">
                            <label for="name">Event Name</label>
                            <input type="text" id="name" name="name" maxlength="150" value="<?= e($form['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                <option value="">-- Select --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat ?>" <?= $form['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="event_date">Date</label>
                            <input type="date" id="event_date" name="event_date" value="<?= e($form['event_date']) ?>" required>
                        </div>
                        <div class="form-group full">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location" maxlength="150" value="<?= e($form['location']) ?>" required>
                        </div>
                        <div class="form-group full">
                            <label for="image_url">Image URL</label>
                            <input type="text" id="image_url" name="image_url" maxlength="255" value="<?= e($form['image_url']) ?>" placeholder="https://... or assets/images/..." required>
                        </div>
                        <div class="form-group full">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4" required><?= e($form['description']) ?></textarea>
                        </div>
                    </div>

                    <div class="actions" style="margin-top: 1rem;">
                        <button type="submit" class="btn"><?= $isEdit ? 'Save Changes' : 'Add Event' ?></button>
                        <?php if ($isEdit): ?>
                            <a href="dashboard.php" class="btn" style="background: transparent; color: var(--color-primary);">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- ===== Events table + live search ===== -->
            <div class="panel">
                <h2 style="margin-bottom: 1rem;">All Events (<span id="resultCount"><?= count($events) ?></span>)</h2>
                <input type="search" id="liveSearch" class="search-input" placeholder="&#128269; Search by name, category or location..." autocomplete="off">

                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr><th>Name</th><th>Category</th><th>Date</th><th>Location</th><th>Actions</th></tr>
                        </thead>
                        <tbody id="eventsBody">
                            <?php foreach ($events as $ev): ?>
                                <tr>
                                    <td><?= e($ev['name']) ?></td>
                                    <td><?= e($ev['category']) ?></td>
                                    <td><?= e($ev['event_date']) ?></td>
                                    <td><?= e($ev['location']) ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="dashboard.php?edit=<?= (int) $ev['id'] ?>" class="btn btn-sm">Edit</a>
                                            <form method="post" action="dashboard.php" onsubmit="return confirm('Delete this event?');">
                                                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int) $ev['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
/* Live search: ask the server for matching events while the user types */
(function () {
    const input = document.getElementById('liveSearch');
    const tbody = document.getElementById('eventsBody');
    const count = document.getElementById('resultCount');
    const csrf = <?= json_encode($csrf) ?>;
    let debounce;

    // Escape text before inserting it as HTML (prevents XSS)
    const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    function render(events) {
        count.textContent = events.length;
        if (events.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No matching events.</td></tr>';
            return;
        }
        tbody.innerHTML = events.map((ev) => `
            <tr>
                <td>${esc(ev.name)}</td>
                <td>${esc(ev.category)}</td>
                <td>${esc(ev.event_date)}</td>
                <td>${esc(ev.location)}</td>
                <td>
                    <div class="actions">
                        <a href="dashboard.php?edit=${Number(ev.id)}" class="btn btn-sm">Edit</a>
                        <form method="post" action="dashboard.php" onsubmit="return confirm('Delete this event?');">
                            <input type="hidden" name="csrf" value="${esc(csrf)}">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="${Number(ev.id)}">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>`).join('');
    }

    input.addEventListener('input', () => {
        // Debounce: wait 250ms after the last keystroke before sending a request
        clearTimeout(debounce);
        debounce = setTimeout(async () => {
            try {
                const res = await fetch('dashboard.php?ajax=1&q=' + encodeURIComponent(input.value));
                render(await res.json());
            } catch (err) {
                console.error('Search failed:', err);
            }
        }, 250);
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
