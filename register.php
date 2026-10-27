<?php
/**
 * Register: creates a new customer account with a hashed password,
 * then logs the user in straight away.
 */
require_once __DIR__ . '/config/db.php';

if (currentUser()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors[] = 'Enter your name (2 to 100 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address, like name@example.com.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Use a password with at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'The two passwords don\'t match.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with this email already exists. Log in instead.';
        }
    }

    if (!$errors) {
        // New sign-ups are always customers; admins are created in the database only
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, \'customer\')');
        $stmt->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int) $pdo->lastInsertId(), 'name' => $name, 'role' => 'customer'];
        flash('Account created. Welcome to Store, ' . $name . '.');
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Create account';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container narrow">
        <h1 class="page-title">Create an account</h1>
        <p class="lead">Save your bag and check out faster.</p>

        <?php if ($errors): ?>
            <div class="alert alert-error" role="alert"><?= implode('<br>', array_map('e', $errors)) ?></div>
        <?php endif; ?>

        <form method="post" action="register.php" class="form panel">
            <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?= e($name) ?>" autocomplete="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" required>
            </div>
            <div class="form-group">
                <label for="confirm">Confirm password</label>
                <input type="password" id="confirm" name="confirm" minlength="8" autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn">Create account</button>
            <p class="form-note">Already have an account? <a href="login.php">Log in</a></p>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
