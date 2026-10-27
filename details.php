<?php
/**
 * Single product page: details.php?id=3
 * Size selection, live stock counter and add-to-bag form.
 */
require_once __DIR__ . '/config/db.php';

// Accept only a positive whole number as the id
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$product = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $product = $stmt->fetch();
}

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Tee not found';
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container empty">'
       . '<h1 class="page-title">We couldn\'t find that tee</h1>'
       . '<p>It may have sold out and been removed.</p>'
       . '<a href="products.php" class="btn">Browse all tees</a></div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$stock = (int) $product['stock'];
$sizes = sizeList($product['size']);

// "You might also like": other tees in the same category
$stmt = $pdo->prepare('SELECT * FROM products WHERE category = :category AND id <> :id ORDER BY stock = 0, id LIMIT 4');
$stmt->execute(['category' => $product['category'], 'id' => $product['id']]);
$related = $stmt->fetchAll();

$pageTitle = $product['title'];
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <a href="products.php?category=<?= urlencode($product['category']) ?>" class="btn-link back-link">Back to <?= e($product['category']) ?></a>

        <div class="details">
            <div class="details-media">
                <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['title']) ?>" width="600" height="600">
            </div>

            <div class="details-info">
                <h1 class="page-title"><?= e($product['title']) ?></h1>
                <p class="price price-lg"><?= price($product['price']) ?></p>
                <p><?= e($product['description']) ?></p>

                <form method="post" action="bag.php" class="buy-form">
                    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                    <input type="hidden" name="back" value="details.php?id=<?= (int) $product['id'] ?>">

                    <fieldset class="size-picker">
                        <legend>Size</legend>
                        <div class="size-options">
                            <?php foreach (SIZES as $s): ?>
                                <?php $available = in_array($s, $sizes, true); ?>
                                <label class="size-option">
                                    <input type="radio" name="size" value="<?= $s ?>" required <?= $available && $stock > 0 ? '' : 'disabled' ?>>
                                    <span><?= $s ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <!-- Stock counter: the +/- buttons can't go past what's in stock -->
                    <div class="qty-row">
                        <span id="qtyLabel">Quantity</span>
                        <div class="stepper" role="group" aria-labelledby="qtyLabel">
                            <button type="button" class="stepper-btn" data-step="-1" aria-label="Decrease quantity">&minus;</button>
                            <input type="number" id="qty" name="qty" value="1" min="1" max="<?= max(1, min(10, $stock)) ?>" aria-label="Quantity">
                            <button type="button" class="stepper-btn" data-step="1" aria-label="Increase quantity">+</button>
                        </div>
                        <span class="stock-note<?= $stock < LOW_STOCK ? ' low' : '' ?>" id="stockNote" data-stock="<?= $stock ?>"><?= stockLabel($stock) ?></span>
                    </div>

                    <button type="submit" class="btn btn-block" <?= $stock === 0 ? 'disabled' : '' ?>>
                        <?= $stock === 0 ? 'Sold out' : 'Add to bag' ?>
                    </button>
                </form>

                <table class="spec-table">
                    <tr><th scope="row">Fit</th><td><?= e($product['category']) ?></td></tr>
                    <tr><th scope="row">Sizes</th><td><?= e(implode(', ', $sizes)) ?></td></tr>
                    <tr><th scope="row">Care</th><td>Wash cold inside out, hang dry</td></tr>
                </table>
            </div>
        </div>
    </div>
</section>

<?php if ($related): ?>
<section class="section section-tint">
    <div class="container">
        <h2>More <?= e($product['category']) ?> tees</h2>
        <div class="product-grid">
            <?php foreach ($related as $p): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/quick-add-modal.php'; ?>
<?php endif; ?>

<script>
/* Quantity stepper: keep the value between 1 and the max allowed by stock */
document.querySelectorAll('.stepper-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
        const input = document.getElementById('qty');
        const next = Number(input.value) + Number(btn.dataset.step);
        input.value = Math.min(Number(input.max), Math.max(1, next));
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
