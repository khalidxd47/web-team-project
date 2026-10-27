<?php
/**
 * Small helper functions shared by all pages.
 */

/**
 * Escape text before printing it in HTML (prevents XSS attacks).
 */
function e(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Convert a database date (2026-11-02) into a readable one (02 Nov 2026).
 */
function formatDate(string $date): string
{
    return date('d M Y', strtotime($date));
}

/**
 * Shorten long text for card previews, adding "..." at the end.
 */
function excerpt(string $text, int $length = 110): string
{
    return mb_strimwidth($text, 0, $length, '...', 'UTF-8');
}
