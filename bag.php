<?php
/**
 * Shopping bag (stored in the session).
 *
 *   POST action=add       -> add a product + size + quantity
 *   POST action=remove    -> remove one line
 *   POST action=checkout  -> logged-in users only; reduces stock in a transaction
 *
 * $_SESSION['bag'] looks like: ['3-M' => ['id' => 3, 'size' => 'M', 'qty' => 2], ...]
 */
require_once __DIR__ . '/config/db.php';

$_SESSION['bag'] = $_SESSION['bag'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $action = $_POST['action'] ?? '';

    // Where to go after adding (only simple local pages are allowed)
    $back = $_POST['back'] ?? 'bag.php';
    if (!preg_match('/^[a-z]+\.php(\?[\w=&%+-]*)?$/i', $back)) {
        $back = 'bag.php';
    }

    if ($action === 'add') {
        $id = (int) ($_POST['id'] ?? 0);
        $size = $_POST['size'] ?? '';
        $qty = max(1, min(10, (int) ($_POST['qty'] ?? 1)));

        $stmt = $pdo->prepare('SELECT title, size, stock FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();
        $key = $id . '-' . $size;
        $inBag = $_SESSION['bag'][$key]['qty'] ?? 0;

        if (!$product) {
            flash('That product no longer exists.', 'error');
        } elseif (!in_array($size, sizeList($product['size']), true)) {
            flash('Choose a size before adding to your bag.', 'error');
            $back = 'details.php?id=' . $id;
        } elseif ($inBag + $qty > $product['stock']) {
            flash('Only ' . $product['stock'] . ' left of ' . $product['title'] . '. Lower the quantity and try again.', 'error');
        } else {
            $_SESSION['bag'][$key] = ['id' => $id, 'size' => $size, 'qty' => $inBag + $qty];
            flash('Added ' . $product['title'] . ' (' . $size . ') to your bag.');
        }
        header('Location: ' . $back);
        exit;
    }

    if ($action === 'remove') {
        unset($_SESSION['bag'][$_POST['key'] ?? '']);
        flash('Removed from your bag.');
        header('Location: bag.php');
        exit;
    }

    if ($action === 'checkout') {
        requireLogin();

        // Transaction: either every line's stock is reduced, or none is
        $pdo->beginTransaction();
        $update = $pdo->prepare('UPDATE products SET stock = stock - :qty WHERE id = :id AND stock >= :need');
        $ok = true;
        foreach ($_SESSION['bag'] as $line) {
            $update->execute(['qty' => $line['qty'], 'id' => $line['id'], 'need' => $line['qty']]);
            if ($update->rowCount() === 0) { // not enough stock left
                $ok = false;
                break;
            }
        }

        if ($ok && $_SESSION['bag']) {
            $pdo->commit();
            $_SESSION['bag'] = [];
            flash('Order placed. We\'ll email you when it ships.');
        } else {
            $pdo->rollBack();
            flash('Some items sold out while you were shopping. Update your bag and try again.', 'error');
        }
        header('Location: bag.php');
        exit;
    }
}

// ---------- Load bag lines with their product details ----------
$lines = [];
$total = 0;
if ($_SESSION['bag']) {
    $ids = array_unique(array_column($_SESSION['bag'], 'id'));
    $marks = implode(',', array_fill(0, count($ids), '?')); // one ? per id
    $stmt = $pdo->prepare("SELECT id, title, price, stock, image_url FROM products WHERE id IN ($marks)");
    $stmt->execute(array_values($ids));
    $products = array_column($stmt->fetchAll(), null, 'id'); // index rows by id

    foreach ($_SESSION['bag'] as $key => $line) {
        if (!isset($products[$line['id']])) {
            continue; // product was deleted by an admin
        }
        $p = $products[$line['id']];
        $subtotal = $p['price'] * $line['qty'];
        $total += $subtotal;
        $lines[] = $line + ['key' => $key, 'product' => $p, 'subtotal' => $subtotal];
    }
}

$pageTitle = 'Your bag';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Your bag</h1>

        <?php if (!$lines): ?>
            <div class="empty">
                <p>Your bag is empty.</p>
                <a href="products.php" class="btn">Browse all tees</a>
            </div>
        <?php else: ?>
            <div class="bag-layout">
                <ul class="bag-lines">
                    <?php foreach ($lines as $line): ?>
                        <li class="bag-line">
                            <img src="<?= e($line['product']['image_url']) ?>" alt="" width="96" height="96">
                            <div>
                                <a href="details.php?id=<?= (int) $line['id'] ?>" class="bag-title"><?= e($line['product']['title']) ?></a>
                                <p class="muted">Size <?= e($line['size']) ?>, quantity <?= (int) $line['qty'] ?></p>
                            </div>
                            <p class="price"><?= price($line['subtotal']) ?></p>
                            <form method="post" action="bag.php">
                                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="key" value="<?= e($line['key']) ?>">
                                <button type="submit" class="btn-link">Remove</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <aside class="panel bag-summary">
                    <p class="summary-row"><span>Total</span> <strong class="price"><?= price($total) ?></strong></p>
                    <p class="muted"><?= $total >= 60 ? 'Shipping is free on this order.' : 'Add ' . price(60 - $total) . ' more for free shipping.' ?></p>

                    <?php if (currentUser()): ?>
                        <form method="post" action="bag.php">
                            <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                            <input type="hidden" name="action" value="checkout">
                            <button type="submit" class="btn btn-block">Place order</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php?next=bag.php" class="btn btn-block">Log in to check out</a>
                    <?php endif; ?>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
