<?php
/**
 * Small helpers shared by every page: output escaping, formatting,
 * session/auth checks, CSRF protection and the shopping bag.
 */

// Start the session once per request (it stores the logged-in user and the bag)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Valid sizes, in display order
const SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

// Below this many units a product counts as "low stock"
const LOW_STOCK = 10;

/** Escape text before printing it into HTML (prevents XSS). */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** 34 -> "$34.00" */
function price(float|string $amount): string
{
    return '$' . number_format((float) $amount, 2);
}

/** Shorten long text for cards. */
function excerpt(string $text, int $length = 90): string
{
    return mb_strimwidth($text, 0, $length, '...');
}

/** "S,M,L" -> ['S', 'M', 'L'] */
function sizeList(string $sizes): array
{
    return array_values(array_filter(array_map('trim', explode(',', $sizes))));
}

/** Human stock label used on product pages. */
function stockLabel(int $stock): string
{
    if ($stock === 0) {
        return 'Sold out';
    }
    return $stock < LOW_STOCK ? "Only $stock left" : 'In stock';
}

// ---------- Auth ----------

/** The logged-in user as ['id','name','role'], or null for guests. */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isAdmin(): bool
{
    return (currentUser()['role'] ?? '') === 'admin';
}

/** Send guests to the login page, then back here after they sign in. */
function requireLogin(): void
{
    if (!currentUser()) {
        header('Location: login.php?next=' . urlencode(basename($_SERVER['REQUEST_URI'])));
        exit;
    }
}

/** Only admins may continue; customers get a 403 page. */
function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        exit('Access denied: this page is for store admins only.');
    }
}

// ---------- CSRF ----------

/** A random secret that every POST form sends back, so other sites can't submit our forms. */
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function checkCsrf(): void
{
    if (!hash_equals(csrfToken(), $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Invalid request. Please reload the page and try again.');
    }
}

// ---------- Bag & flash messages ----------

/** Total number of items in the bag (shown in the navbar). */
function bagCount(): int
{
    return array_sum(array_column($_SESSION['bag'] ?? [], 'qty'));
}

/** Store a one-time message to show after a redirect. */
function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

/** Read and clear the one-time message. */
function takeFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
