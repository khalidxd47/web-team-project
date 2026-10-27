<?php
/**
 * One product card. Expects $p (a products row) to be set before including.
 * The "Quick add" button carries the product data in data-* attributes,
 * which main.js reads to fill the quick-add modal.
 */
$stock = (int) $p['stock'];
?>
<article class="product-card<?= $stock === 0 ? ' is-sold-out' : '' ?>">
    <a href="details.php?id=<?= (int) $p['id'] ?>" class="product-media">
        <img src="<?= e($p['image_url']) ?>" alt="<?= e($p['title']) ?>" loading="lazy" width="400" height="400">
    </a>

    <div class="product-info">
        <h3><a href="details.php?id=<?= (int) $p['id'] ?>"><?= e($p['title']) ?></a></h3>
        <p class="price"><?= price($p['price']) ?></p>
    </div>
    <p class="product-meta">
        <span><?= e($p['category']) ?></span>
        <span class="stock-note<?= $stock < LOW_STOCK ? ' low' : '' ?>"><?= stockLabel($stock) ?></span>
    </p>

    <?php if ($stock > 0): ?>
        <button type="button" class="btn btn-outline btn-sm js-quick-add"
                data-id="<?= (int) $p['id'] ?>"
                data-title="<?= e($p['title']) ?>"
                data-price="<?= price($p['price']) ?>"
                data-image="<?= e($p['image_url']) ?>"
                data-description="<?= e($p['description']) ?>"
                data-sizes="<?= e($p['size']) ?>"
                data-stock="<?= $stock ?>">
            Quick add
        </button>
    <?php endif; ?>
</article>
