<?php
/**
 * Shared page header + navigation bar.
 *
 * Usage at the top of every page:
 *     $pageTitle = 'Events';
 *     require_once __DIR__ . '/includes/header.php';
 */
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Home';

// Name of the current file (e.g. "events.php"), used to highlight the active link
$currentPage = basename($_SERVER['PHP_SELF']);

// Navigation items: file => label
$navLinks = [
    'index.php'     => 'Home',
    'about.php'     => 'About',
    'events.php'    => 'Events',
    'gallery.php'   => 'Gallery',
    'contact.php'   => 'Contact',
    'dashboard.php' => 'Dashboard',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Computer Science Club at Umm Al-Qura University - events, workshops and competitions.">
    <title><?= e($pageTitle) ?> | UQU CS Club</title>

    <!-- 1) EXTERNAL CSS: main stylesheet shared by all pages -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- 2) INTERNAL CSS: small styles that only belong to the header -->
    <style>
        .announcement {
            text-align: center;
            font-size: 0.9rem;
            padding: 0.45rem 1rem;
        }

        .announcement a {
            color: inherit;
            text-decoration: underline;
            font-weight: 600;
        }

        /* Hide visually but keep it readable for screen readers */
        .skip-link {
            position: absolute;
            left: -9999px;
        }

        .skip-link:focus {
            left: 1rem;
            top: 1rem;
            z-index: 200;
            background: #fff;
            padding: 0.5rem 1rem;
        }
    </style>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- 3) INLINE CSS: style attribute applied directly to one element -->
    <div class="announcement" style="background-color: var(--color-accent); color: var(--color-secondary);">
        Registration is open for the 48-Hour Hackathon! <a href="events.php">See details</a>
    </div>

    <nav class="navbar" aria-label="Main navigation">
        <div class="container">
            <a href="index.php" class="logo">
                <span class="logo-icon">&lt;/&gt;</span>
                UQU CS Club
            </a>

            <!-- Hamburger button (visible on mobile only) -->
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="navLinks">
                &#9776;
            </button>

            <ul class="nav-links" id="navLinks">
                <?php foreach ($navLinks as $file => $label): ?>
                    <li>
                        <a href="<?= $file ?>" class="<?= $currentPage === $file ? 'active' : '' ?>">
                            <?= $label ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>

    <main id="main-content">
