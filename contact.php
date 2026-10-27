<?php
/**
 * Contact + custom printing requests.
 * Validated in the browser (assets/js/validate.js) AND again here,
 * because client-side checks can be bypassed.
 */
require_once __DIR__ . '/includes/functions.php';

$types = ['question' => 'General question', 'order' => 'Help with an order', 'custom' => 'Custom print request'];

$values  = ['name' => '', 'email' => '', 'phone' => '', 'type' => '', 'quantity' => '', 'message' => ''];
$errors  = [];
$success = false;

// Same patterns as validate.js
const EMAIL_PATTERN = '/^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/';
const PHONE_PATTERN = '/^\+?[0-9\s-]{8,15}$/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    foreach ($values as $field => $_) {
        $values[$field] = trim($_POST[$field] ?? '');
    }

    if (mb_strlen($values['name']) < 2) {
        $errors['name'] = 'Enter your name.';
    }
    if (!preg_match(EMAIL_PATTERN, $values['email'])) {
        $errors['email'] = 'Enter a valid email, like name@example.com.';
    }
    if ($values['phone'] !== '' && !preg_match(PHONE_PATTERN, $values['phone'])) {
        $errors['phone'] = 'Enter 8 to 15 digits, or leave this empty.';
    }
    if (!isset($types[$values['type']])) {
        $errors['type'] = 'Choose what your message is about.';
    }
    if ($values['type'] === 'custom' && (!ctype_digit($values['quantity']) || (int) $values['quantity'] < 12)) {
        $errors['quantity'] = 'Custom prints start at 12 shirts.';
    }
    if (mb_strlen($values['message']) < 20) {
        $errors['message'] = 'Tell us a bit more (at least 20 characters).';
    }

    if (!$errors) {
        $success = true;
        $values = array_fill_keys(array_keys($values), ''); // clear the form
    }
}

$pageTitle = 'Custom prints';
require_once __DIR__ . '/includes/header.php';

// Print one field's error message under the input
$err = fn($f) => '<span class="error-msg" id="' . $f . '-error">' . e($errors[$f] ?? '') . '</span>';
$cls = fn($f) => isset($errors[$f]) ? ' invalid' : '';
?>

<section class="section">
    <div class="container contact-layout">
        <div>
            <h1 class="page-title">Custom prints and questions</h1>
            <p class="lead">Need shirts for a team, event or brand? We print runs from 12 pieces, usually within two weeks.</p>

            <table class="spec-table">
                <tr><th scope="row">Minimum order</th><td>12 shirts per design</td></tr>
                <tr><th scope="row">Ink colors</th><td>Up to 4 per design</td></tr>
                <tr><th scope="row">Turnaround</th><td>10 to 14 working days</td></tr>
                <tr><th scope="row">Email</th><td>hello@store.com</td></tr>
            </table>
        </div>

        <div class="panel">
            <?php if ($success): ?>
                <div class="alert alert-success" role="status">Message sent. We reply within two working days.</div>
            <?php elseif ($errors): ?>
                <div class="alert alert-error" role="alert">Fix the highlighted fields and send again.</div>
            <?php endif; ?>

            <!-- novalidate: turn off the browser's built-in popups so our JS validation runs -->
            <form id="contactForm" method="post" action="contact.php" novalidate>
                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

                <div class="form-grid">
                    <div class="form-group<?= $cls('name') ?>">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?= e($values['name']) ?>" autocomplete="name" aria-describedby="name-error" required>
                        <?= $err('name') ?>
                    </div>

                    <div class="form-group<?= $cls('email') ?>">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= e($values['email']) ?>" autocomplete="email" aria-describedby="email-error" required>
                        <?= $err('email') ?>
                    </div>

                    <div class="form-group<?= $cls('phone') ?>">
                        <label for="phone">Phone <span class="muted">(optional)</span></label>
                        <input type="tel" id="phone" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel" aria-describedby="phone-error">
                        <?= $err('phone') ?>
                    </div>

                    <div class="form-group<?= $cls('type') ?>">
                        <label for="type">About</label>
                        <select id="type" name="type" aria-describedby="type-error" required>
                            <option value="">Choose one</option>
                            <?php foreach ($types as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $values['type'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= $err('type') ?>
                    </div>

                    <!-- Only shown (and required) for custom print requests -->
                    <div class="form-group full<?= $cls('quantity') ?>" id="quantityGroup" <?= $values['type'] === 'custom' ? '' : 'hidden' ?>>
                        <label for="quantity">How many shirts?</label>
                        <input type="number" id="quantity" name="quantity" min="12" value="<?= e($values['quantity']) ?>" aria-describedby="quantity-error">
                        <?= $err('quantity') ?>
                    </div>

                    <div class="form-group full<?= $cls('message') ?>">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6" aria-describedby="message-error" required><?= e($values['message']) ?></textarea>
                        <?= $err('message') ?>
                    </div>
                </div>

                <button type="submit" class="btn">Send message</button>
            </form>
        </div>
    </div>
</section>

<script src="assets/js/validate.js"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
