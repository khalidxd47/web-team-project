<?php
/**
 * Inventory dashboard (admins only): full CRUD for products,
 * a live stock tracker and instant search.
 *
 *   Create  -> POST action=create
 *   Read    -> table below + instant search (GET ?ajax=search&q=...)
 *   Update  -> GET ?edit=ID fills the form, then POST action=update
 *   Delete  -> POST action=delete
 *   Stock   -> POST ajax=stock (+1 / -1 from the table, returns JSON)
 *
 * All queries use PDO prepared statements.
 */
require_once __DIR__ . '/config/db.php';
requireAdmin();

$categories = ['Essentials', 'Graphic', 'Oversized', 'Vintage Wash', 'Long Sleeve'];
$images = array_map('basename', glob(__DIR__ . '/assets/images/*.svg'));

/** Stock summary for the tracker tiles. */
function stockStats(PDO $pdo): array
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) AS products, COALESCE(SUM(stock), 0) AS units,
                COALESCE(SUM(stock > 0 AND stock < :low), 0) AS low, COALESCE(SUM(stock = 0), 0) AS sold_out
         FROM products'
    );
    $stmt->execute(['low' => LOW_STOCK]);
    return array_map('intval', $stmt->fetch());
}

/** Products matching a search term (empty term = all). */
function findProducts(PDO $pdo, string $q): array
{
    $term = '%' . $q . '%';
    $stmt = $pdo->prepare(
        'SELECT id, title, category, size, price, stock FROM products
         WHERE title LIKE :t1 OR category LIKE :t2 OR size LIKE :t3
         ORDER BY stock, title'
    );
    $stmt->execute(['t1' => $term, 't2' => $term, 't3' => $term]);
    return $stmt->fetchAll();
}

// ---------- INSTANT SEARCH + LIVE REFRESH (JSON) ----------
if (($_GET['ajax'] ?? '') === 'search') {
    header('Content-Type: application/json');
    echo json_encode(['rows' => findProducts($pdo, trim($_GET['q'] ?? '')), 'stats' => stockStats($pdo)]);
    exit;
}

// ---------- STOCK +1 / -1 (JSON) ----------
if (($_POST['ajax'] ?? '') === 'stock') {
    checkCsrf();
    $id = (int) ($_POST['id'] ?? 0);
    // Two fixed statements instead of "stock + :delta": stock is UNSIGNED, so it must never go below 0
    $sql = ($_POST['delta'] ?? '') === '1'
        ? 'UPDATE products SET stock = stock + 1 WHERE id = :id'
        : 'UPDATE products SET stock = stock - 1 WHERE id = :id AND stock > 0';
    $pdo->prepare($sql)->execute(['id' => $id]);

    $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);
    header('Content-Type: application/json');
    echo json_encode(['stock' => (int) $stmt->fetchColumn(), 'stats' => stockStats($pdo)]);
    exit;
}

$errors = [];
$form = ['id' => '', 'title' => '', 'description' => '', 'category' => '', 'size' => '', 'price' => '', 'stock' => '', 'image_url' => ''];

// ---------- CREATE / UPDATE / DELETE (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => (int) ($_POST['id'] ?? 0)]);
        flash('Product deleted.');
        header('Location: dashboard.php');
        exit;
    }

    // Collect and trim the submitted values
    foreach ($form as $field => $_) {
        $form[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
    }
    // Sizes arrive as checkboxes (size[]); keep only valid ones, in the standard order
    $form['size'] = implode(',', array_intersect(SIZES, (array) ($_POST['sizes'] ?? [])));

    // Server-side validation
    if ($form['title'] === '' || mb_strlen($form['title']) > 150) {
        $errors[] = 'Enter a title (up to 150 characters).';
    }
    if (mb_strlen($form['description']) < 10) {
        $errors[] = 'Write a description of at least 10 characters.';
    }
    if (!in_array($form['category'], $categories, true)) {
        $errors[] = 'Choose a category.';
    }
    if ($form['size'] === '') {
        $errors[] = 'Tick at least one size.';
    }
    if (!is_numeric($form['price']) || $form['price'] <= 0 || $form['price'] > 999999) {
        $errors[] = 'Enter a price above 0, like 34.00.';
    }
    if (!ctype_digit($form['stock'])) {
        $errors[] = 'Enter stock as a whole number (0 or more).';
    }
    if ($form['image_url'] === '' || mb_strlen($form['image_url']) > 255) {
        $errors[] = 'Choose an image.';
    }

    if (!$errors) {
        $params = [
            'title'       => $form['title'],
            'description' => $form['description'],
            'category'    => $form['category'],
            'size'        => $form['size'],
            'price'       => round((float) $form['price'], 2),
            'stock'       => (int) $form['stock'],
            'image_url'   => $form['image_url'],
        ];

        if ($action === 'update') {
            $params['id'] = (int) $form['id'];
            $stmt = $pdo->prepare(
                'UPDATE products SET title = :title, description = :description, category = :category,
                        size = :size, price = :price, stock = :stock, image_url = :image_url
                 WHERE id = :id'
            );
            flash('Changes saved.');
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO products (title, description, category, size, price, stock, image_url)
                 VALUES (:title, :description, :category, :size, :price, :stock, :image_url)'
            );
            flash('Product added.');
        }
        $stmt->execute($params);

        // Post/Redirect/Get: refreshing the page won't submit the form twice
        header('Location: dashboard.php');
        exit;
    }
}

// ---------- Load a product into the form for editing ----------
if (!$errors && isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['edit']]);
    $form = $stmt->fetch() ?: $form;
}
$isEdit = (string) $form['id'] !== '';
$checkedSizes = sizeList((string) $form['size']);

// ---------- READ: all products + stock summary ----------
$products = findProducts($pdo, '');
$stats = stockStats($pdo);

$pageTitle = 'Inventory';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Inventory</h1>

        <!-- Live stock tracker: refreshed by JavaScript every 15 seconds and after each change -->
        <dl class="stock-stats" id="stockStats" aria-live="polite">
            <div><dt>Products</dt><dd data-stat="products"><?= $stats['products'] ?></dd></div>
            <div><dt>Units in stock</dt><dd data-stat="units"><?= $stats['units'] ?></dd></div>
            <div class="warn"><dt>Low stock (under <?= LOW_STOCK ?>)</dt><dd data-stat="low"><?= $stats['low'] ?></dd></div>
            <div class="danger"><dt>Sold out</dt><dd data-stat="sold_out"><?= $stats['sold_out'] ?></dd></div>
        </dl>

        <?php if ($errors): ?>
            <div class="alert alert-error" role="alert"><?= implode('<br>', array_map('e', $errors)) ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <!-- ===== Products table + instant search ===== -->
            <div class="panel">
                <div class="section-head">
                    <h2>Products (<span id="resultCount"><?= count($products) ?></span>)</h2>
                </div>
                <label for="liveSearch" class="visually-hidden">Search products</label>
                <input type="search" id="liveSearch" class="search-input" placeholder="Search by title, fit or size" autocomplete="off">

                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Fit</th>
                                <th scope="col">Sizes</th>
                                <th scope="col" class="num">Price</th>
                                <th scope="col">Stock</th>
                                <th scope="col"><span class="visually-hidden">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody id="productsBody">
                            <!-- Rows are drawn by render() below, from the same data the search returns -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===== Create / Update form ===== -->
            <div class="panel">
                <h2><?= $isEdit ? 'Edit product' : 'Add a product' ?></h2>

                <form method="post" action="dashboard.php" class="form">
                    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'create' ?>">
                    <input type="hidden" name="id" value="<?= e((string) $form['id']) ?>">

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" maxlength="150" value="<?= e($form['title']) ?>" required>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="category">Fit</label>
                            <select id="category" name="category" required>
                                <option value="">Choose one</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat ?>" <?= $form['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="image_url">Image</label>
                            <select id="image_url" name="image_url" required>
                                <?php foreach ($images as $img): ?>
                                    <?php $path = 'assets/images/' . $img; ?>
                                    <option value="<?= e($path) ?>" <?= $form['image_url'] === $path ? 'selected' : '' ?>><?= e(ucfirst(str_replace(['tee-', '.svg'], '', $img))) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Price ($)</label>
                            <input type="number" id="price" name="price" min="0.01" step="0.01" value="<?= e((string) $form['price']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" id="stock" name="stock" min="0" step="1" value="<?= e((string) $form['stock']) ?>" required>
                        </div>
                    </div>

                    <fieldset class="size-picker">
                        <legend>Sizes</legend>
                        <div class="size-options">
                            <?php foreach (SIZES as $s): ?>
                                <label class="size-option">
                                    <input type="checkbox" name="sizes[]" value="<?= $s ?>" <?= in_array($s, $checkedSizes, true) ? 'checked' : '' ?>>
                                    <span><?= $s ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required><?= e($form['description']) ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn"><?= $isEdit ? 'Save changes' : 'Add product' ?></button>
                        <?php if ($isEdit): ?>
                            <a href="dashboard.php" class="btn-link">Cancel editing</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
/* Instant search, live stock tracker and +/- stock buttons */
(function () {
    const input = document.getElementById('liveSearch');
    const tbody = document.getElementById('productsBody');
    const count = document.getElementById('resultCount');
    const csrf = <?= json_encode(csrfToken()) ?>;
    const LOW = <?= LOW_STOCK ?>;
    let debounce;

    // Escape text before inserting it as HTML (prevents XSS)
    const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    // Stock cell: number + a small bar (full at 50 units), colored by level
    function stockCell(stock) {
        const level = stock === 0 ? 'out' : stock < LOW ? 'low' : 'ok';
        const width = Math.min(100, stock * 2);
        return `<div class="stock-cell ${level}">
                    <button type="button" class="stepper-btn" data-delta="-1" aria-label="Remove one" ${stock === 0 ? 'disabled' : ''}>&minus;</button>
                    <span class="stock-num">${stock}</span>
                    <button type="button" class="stepper-btn" data-delta="1" aria-label="Add one">+</button>
                    <span class="stock-bar"><span style="width:${width}%"></span></span>
                </div>`;
    }

    function render(rows) {
        count.textContent = rows.length;
        if (rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No products match. Try a different word.</td></tr>';
            return;
        }
        tbody.innerHTML = rows.map((p) => `
            <tr data-id="${Number(p.id)}">
                <td><a href="details.php?id=${Number(p.id)}">${esc(p.title)}</a></td>
                <td>${esc(p.category)}</td>
                <td>${esc(p.size.replaceAll(',', ', '))}</td>
                <td class="num">$${Number(p.price).toFixed(2)}</td>
                <td>${stockCell(Number(p.stock))}</td>
                <td>
                    <div class="row-actions">
                        <a href="dashboard.php?edit=${Number(p.id)}" class="btn-link">Edit</a>
                        <form method="post" action="dashboard.php" class="js-delete" data-title="${esc(p.title)}">
                            <input type="hidden" name="csrf" value="${esc(csrf)}">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="${Number(p.id)}">
                            <button type="submit" class="btn-link danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>`).join('');
    }

    function renderStats(stats) {
        Object.entries(stats).forEach(([key, value]) => {
            document.querySelector(`[data-stat="${key}"]`).textContent = value;
        });
    }

    async function refresh() {
        try {
            const res = await fetch('dashboard.php?ajax=search&q=' + encodeURIComponent(input.value.trim()));
            const data = await res.json();
            render(data.rows);
            renderStats(data.stats);
        } catch (err) {
            console.error('Refresh failed:', err);
        }
    }

    // Search while typing, 250ms after the last keystroke
    input.addEventListener('input', () => {
        clearTimeout(debounce);
        debounce = setTimeout(refresh, 250);
    });

    // +/- buttons: one listener on the table handles every row (event delegation)
    tbody.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-delta]');
        if (!btn) return;
        const row = btn.closest('tr');
        const body = new URLSearchParams({ ajax: 'stock', csrf, id: row.dataset.id, delta: btn.dataset.delta });

        btn.disabled = true;
        const res = await fetch('dashboard.php', { method: 'POST', body });
        const data = await res.json();
        row.cells[4].innerHTML = stockCell(data.stock);
        renderStats(data.stats);
    });

    // Ask before deleting (the title is read from data-title, so quotes in it can't break the script)
    tbody.addEventListener('submit', (e) => {
        if (!confirm('Delete ' + e.target.dataset.title + '? This can\'t be undone.')) e.preventDefault();
    });

    // First draw from the server data, then poll so customer orders show up without reloading
    render(<?= json_encode($products) ?>);
    setInterval(() => {
        if (!document.hidden) refresh();
    }, 15000);
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
