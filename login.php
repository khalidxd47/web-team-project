<?php
/**
 * Login: checks the email + password against the users table,
 * stores the user in the session, then redirects by role.
 */
require_once __DIR__ . '/config/db.php';

// Only allow redirects to a local page like "bag.php" or "details.php?id=3" (no open redirects)
$next = $_GET['next'] ?? $_POST['next'] ?? '';
if (!preg_match('/^[a-z]+\.php(\?[\w=&%+-]*)?$/i', $next)) {
    $next = '';
}

// Already logged in? Skip the form
if (currentUser()) {
    header('Location: ' . (isAdmin() ? 'dashboard.php' : 'index.php'));
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, name, password, role FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    // password_verify compares the typed password with the stored bcrypt hash
    if ($row && password_verify($password, $row['password'])) {
        session_regenerate_id(true); // new session id after login prevents session fixation
        $_SESSION['user'] = ['id' => (int) $row['id'], 'name' => $row['name'], 'role' => $row['role']];
        flash('Welcome back, ' . $row['name'] . '.');

        // Role-based redirect
        $target = $row['role'] === 'admin' ? 'dashboard.php' : ($next ?: 'index.php');
        header('Location: ' . $target);
        exit;
    }
    $error = 'That email and password don\'t match an account. Check both and try again.';
}

$pageTitle = 'Log in';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container narrow">
        <h1 class="page-title">Log in</h1>
        <p class="lead">Log in to check out your bag.</p>

        <?php if ($error): ?>
            <div class="alert alert-error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" class="form panel">
            <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="next" value="<?= e($next) ?>">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn">Log in</button>
            <p class="form-note">New here? <a href="register.php">Create an account</a></p>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
