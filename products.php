<?php
/**
 * Catalog: every tee, with category and size filters (?category=...&size=...).
 * Filter values are whitelisted, then passed to a prepared statement.
 */
require_once __DIR__ . '/config/db.php';

$categories = $pdo->query('SELECT DISTINCT category FROM products ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);

// Keep a filter only if it is one of the known values
$category = in_array($_GET['category'] ?? '', $categories, true) ? $_GET['category'] : '';
$size = in_array($_GET['size'] ?? '', SIZES, true) ? $_GET['size'] : '';

// Build the WHERE clause from the active filters only
$where = [];
$params = [];
if ($category !== '') {
    $where[] = 'category = :category';
    $params['category'] = $category;
}
if ($size !== '') {
    $where[] = 'FIND_IN_SET(:size, size) > 0'; // size column is a list like "S,M,L"
    $params['size'] = $size;
}

$sql = 'SELECT * FROM products' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY stock = 0, title';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

/** Build a filter link that keeps the other filter as it is. */
function filterUrl(string $key, string $value, string $category, string $size): string
{
    $query = ['category' => $category, 'size' => $size];
    $query[$key] = $value;
    return 'products.php?' . http_build_query(array_filter($query)); // array_filter drops empty filters
}

$pageTitle = 'All tees';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">All tees</h1>

        <div class="filters">
            <nav class="filter-group" aria-label="Filter by category">
                <span class="filter-label">Fit</span>
                <a href="<?= e(filterUrl('category', '', $category, $size)) ?>" class="chip<?= $category === '' ? ' active' : '' ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= e(filterUrl('category', $cat, $category, $size)) ?>" class="chip<?= $category === $cat ? ' active' : '' ?>"><?= e($cat) ?></a>
                <?php endforeach; ?>
            </nav>

            <nav class="filter-group" aria-label="Filter by size">
                <span class="filter-label">Size</span>
                <a href="<?= e(filterUrl('size', '', $category, $size)) ?>" class="chip<?= $size === '' ? ' active' : '' ?>">Any</a>
                <?php foreach (SIZES as $s): ?>
                    <a href="<?= e(filterUrl('size', $s, $category, $size)) ?>" class="chip<?= $size === $s ? ' active' : '' ?>"><?= $s ?></a>
                <?php endforeach; ?>
            </nav>
        </div>

        <p class="muted result-count"><?= count($products) ?> <?= count($products) === 1 ? 'tee' : 'tees' ?></p>

        <?php if ($products): ?>
            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                    <?php include __DIR__ . '/includes/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty">
                <p>No tees match these filters.</p>
                <a href="products.php" class="btn">Clear filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/quick-add-modal.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
