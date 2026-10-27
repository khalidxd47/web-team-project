<?php
/**
 * Quick-add modal markup. main.js fills it from the clicked card's
 * data-* attributes and the form posts to bag.php.
 */
$back = basename($_SERVER['REQUEST_URI']);
?>
<div class="modal" id="quickAddModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" hidden>
    <div class="modal-content">
        <button type="button" class="modal-close" id="modalClose" aria-label="Close">&times;</button>

        <img id="modalImage" src="" alt="" class="modal-image">

        <div class="modal-body">
            <h2 id="modalTitle"></h2>
            <p class="price" id="modalPrice"></p>
            <p class="muted" id="modalDescription"></p>

            <form method="post" action="bag.php">
                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="back" value="<?= e($back) ?>">
                <input type="hidden" name="id" id="modalId">

                <fieldset class="size-picker">
                    <legend>Size</legend>
                    <div id="modalSizes" class="size-options"></div>
                </fieldset>

                <div class="qty-row">
                    <label for="modalQty">Quantity</label>
                    <input type="number" id="modalQty" name="qty" value="1" min="1" max="10">
                    <span class="muted" id="modalStock"></span>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn">Add to bag</button>
                    <a href="#" id="modalLink" class="btn-link">See full details</a>
                </div>
            </form>
        </div>
    </div>
</div>
