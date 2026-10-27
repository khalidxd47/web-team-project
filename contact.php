<?php
/**
 * Contact page: form validated in the browser (assets/js/validate.js)
 * AND again on the server here, because client-side checks can be bypassed.
 */
$pageTitle = 'Contact';
require_once __DIR__ . '/includes/functions.php';

$values  = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
$errors  = [];
$success = false;

// Pre-fill the subject when coming from an event page (contact.php?event=...)
if (!empty($_GET['event'])) {
    $values['subject'] = 'Registration: ' . $_GET['event'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $_) {
        $values[$field] = trim($_POST[$field] ?? '');
    }

    if ($values['name'] === '' || mb_strlen($values['name']) < 3) {
        $errors['name'] = 'Please enter your full name (at least 3 characters).';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if ($values['subject'] === '') {
        $errors['subject'] = 'Please enter a subject.';
    }
    if (mb_strlen($values['message']) < 10) {
        $errors['message'] = 'Your message must be at least 10 characters.';
    }

    if (!$errors) {
        $success = true;
        $values  = array_fill_keys(array_keys($values), ''); // clear the form
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Questions, ideas or want to join? Send us a message.</p>
    </div>
</section>

<section class="section">
    <div class="container contact-grid">
        <div class="panel">
            <?php if ($success): ?>
                <div class="alert alert-success">Thank you! Your message has been sent. We'll reply soon.</div>
            <?php elseif ($errors): ?>
                <div class="alert alert-error">Please fix the errors below.</div>
            <?php endif; ?>

            <!-- novalidate: turn off the browser's built-in popups so our JS validation runs -->
            <form id="contactForm" method="post" action="contact.php" novalidate>
                <div class="form-grid">
                    <?php
                    // Helper to print one field's error message
                    $err = fn($f) => '<span class="error-msg">' . e($errors[$f] ?? '') . '</span>';
                    ?>
                    <div class="form-group <?= isset($errors['name']) ? 'invalid' : '' ?>">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" value="<?= e($values['name']) ?>" required>
                        <?= $err('name') ?>
                    </div>

                    <div class="form-group <?= isset($errors['email']) ? 'invalid' : '' ?>">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?= e($values['email']) ?>" placeholder="student@uqu.edu.sa" required>
                        <?= $err('email') ?>
                    </div>

                    <div class="form-group full <?= isset($errors['subject']) ? 'invalid' : '' ?>">
                        <label for="subject">Subject *</label>
                        <input type="text" id="subject" name="subject" value="<?= e($values['subject']) ?>" required>
                        <?= $err('subject') ?>
                    </div>

                    <div class="form-group full <?= isset($errors['message']) ? 'invalid' : '' ?>">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" required><?= e($values['message']) ?></textarea>
                        <?= $err('message') ?>
                    </div>
                </div>

                <button type="submit" class="btn" style="margin-top: 1rem;">Send Message</button>
            </form>
        </div>

        <aside class="panel">
            <h3>Club Office</h3>
            <ul class="info-list">
                <li><strong>Address</strong>College of Computing, Umm Al-Qura University, Makkah</li>
                <li><strong>Email</strong>cs.club@uqu.edu.sa</li>
                <li><strong>Office Hours</strong>Sunday - Thursday, 10:00 AM - 2:00 PM</li>
            </ul>
        </aside>
    </div>
</section>

<script src="assets/js/validate.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
