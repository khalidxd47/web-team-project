<?php
/**
 * Shared page header + navigation bar.
 *
 * Usage at the top of every page:
 *     $pageTitle = 'Products';
 *     require_once __DIR__ . '/includes/header.php';
 */
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Home';
$user = currentUser();
$flash = takeFlash();

// Name of the current file (e.g. "products.php"), used to highlight the active link
$currentPage = basename($_SERVER['PHP_SELF']);

// Navigation items: file => label
$navLinks = [
    'index.php'    => 'Shop',
    'products.php' => 'All tees',
    'gallery.php'  => 'Lookbook',
    'about.php'    => 'About',
    'contact.php'  => 'Custom prints',
];
if (isAdmin()) {
    $navLinks['dashboard.php'] = 'Inventory';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Store - small-batch screen-printed t-shirts, printed by hand.">
    <title><?= e($pageTitle) ?> | Store</title>

    <!-- Archivo: one family, two widths (condensed for display, normal for text) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,400..900&display=swap">

    <!-- 1) EXTERNAL CSS: main stylesheet shared by all pages -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- 2) INTERNAL CSS: styles that only belong to the header -->
    <style>
        .announcement {
            text-align: center;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        /* Hidden until focused with the keyboard */
        .skip-link {
            position: absolute;
            left: -9999px;
        }

        .skip-link:focus {
            left: 1rem;
            top: 1rem;
            z-index: 200;
            background: var(--paper);
            padding: 0.5rem 1rem;
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- 3) INLINE CSS: style attribute applied directly to one element -->
    <div class="announcement" style="background-color: var(--fluoro); color: var(--ink);">
        Free shipping on orders over $60. New Vintage Wash drop is live.
    </div>

    <nav class="navbar" aria-label="Main navigation">
        <div class="container">
            <a href="index.php" class="logo" aria-label="Store home">Store</a>

            <!-- Hamburger button (visible on mobile only) -->
            <button class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false" aria-controls="navLinks">
                &#9776;
            </button>

            <ul class="nav-links" id="navLinks">
                <?php foreach ($navLinks as $file => $label): ?>
                    <li>
                        <a href="<?= $file ?>" <?= $currentPage === $file ? 'class="active" aria-current="page"' : '' ?>><?= $label ?></a>
                    </li>
                <?php endforeach; ?>

                <?php if ($user): ?>
                    <li><a href="logout.php">Log out</a></li>
                <?php else: ?>
                    <li><a href="login.php" <?= $currentPage === 'login.php' ? 'class="active"' : '' ?>>Log in</a></li>
                <?php endif; ?>

                <li>
                    <a href="bag.php" class="bag-link<?= $currentPage === 'bag.php' ? ' active' : '' ?>">
                        Bag <span class="bag-count"><?= bagCount() ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <main id="main-content">
        <?php if ($flash): ?>
            <div class="container">
                <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
            </div>
        <?php endif; ?>
