<?php
/**
 * FRONT CONTROLLER — Entry point for every request.
 *
 * PHP CONCEPTS HERE:
 *  - define() for constants
 *  - Whitelist routing (security: never allow arbitrary file paths)
 *  - phpdotenv loading
 *  - PSR-4 autoloading via Composer
 *
 * Every URL hits this file first:
 *   index.php              → home
 *   index.php?page=about   → about
 *   index.php?page=contact → contact
 *
 * In production, .htaccess rewrites /about → index.php?page=about
 * for clean URLs — but ?page= works fine for local learning.
 */

// ── Bootstrap ─────────────────────────────────────────────────────────────────

// Composer's autoloader: maps App\ namespace to src/ directory.
// After this line, `use App\Models\Song;` works without any require_once.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load .env file into $_ENV using vlucas/phpdotenv
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$dotenv->required(['DB_HOST', 'DB_DATABASE', 'DB_USERNAME']);

/**
 * BASE_PATH constant — absolute path to the public/ directory.
 *
 * Why? PHP's require paths are resolved relative to the file calling them,
 * which causes bugs when the same file is required from different places.
 * A constant always resolves to the same absolute location.
 *
 * __DIR__ = directory of the current file = .../public/
 */
define('BASE_PATH', __DIR__);

// ── Route Map ─────────────────────────────────────────────────────────────────
//
// CONCEPT: Whitelist routing — only explicitly listed pages are allowed.
// If someone sends ?page=../../config/database, it gets rejected.
// The router NEVER does: require $_GET['page'] . '.php';  ← dangerous!
//
$routes = [
    'home'     => BASE_PATH . '/views/pages/home.php',
    'about'    => BASE_PATH . '/views/pages/about.php',
    'profile'  => BASE_PATH . '/views/pages/profile.php',
    'projects' => BASE_PATH . '/views/pages/projects.php',
    'gallery'  => BASE_PATH . '/views/pages/gallery.php',
    'events'   => BASE_PATH . '/views/pages/events.php',
    'albums'   => BASE_PATH . '/views/pages/albums.php',
    'archives' => BASE_PATH . '/views/pages/archives.php',
    'contact'  => BASE_PATH . '/views/pages/contact.php',
];

// ── Resolve Page ──────────────────────────────────────────────────────────────

$page = $_GET['page'] ?? 'home';

if (!array_key_exists($page, $routes)) {
    $page = 'home';
}

// ── Per-page SEO meta ─────────────────────────────────────────────────────────
//
// CONCEPT: Each page gets its own <meta name="description"> (150–160 chars).
// Google shows this text in search results below the page title.
// We also set $ogImage for the Open Graph image tag.
// The page file can override these by setting them again before ob_get_clean().
//
$pageMeta = [
    'home'     => [
        'desc'  => 'Kutumba — Nepal\'s premier folk instrumental ensemble. Discover authentic Nepali music, our musicians, albums, and upcoming events.',
        'image' => 'images/og-default.jpg',
    ],
    'about'    => [
        'desc'  => 'Meet the musicians of Kutumba — a folk instrumental ensemble committed to preserving the diversity of indigenous Nepali music.',
        'image' => 'images/og-default.jpg',
    ],
    'albums'   => [
        'desc'  => 'Explore albums released by Kutumba — Nepal\'s leading folk instrumental ensemble celebrating traditional Nepali sounds.',
        'image' => 'images/og-default.jpg',
    ],
    'events'   => [
        'desc'  => 'Upcoming and past performances by Kutumba — concerts, cultural festivals, and events across Nepal and beyond.',
        'image' => 'images/og-default.jpg',
    ],
    'projects' => [
        'desc'  => 'Kutumba\'s musical projects — research, collaborations, and creative initiatives rooted in Nepali folk tradition.',
        'image' => 'images/og-default.jpg',
    ],
    'archives' => [
        'desc'  => 'Historical archive of Kutumba\'s performances, recordings, and milestones since the band\'s founding.',
        'image' => 'images/og-default.jpg',
    ],
    'gallery'  => [
        'desc'  => 'Photo gallery of Kutumba — behind the scenes, on stage, and cultural moments captured in images.',
        'image' => 'images/og-default.jpg',
    ],
    'contact'  => [
        'desc'  => 'Contact Kutumba for bookings, collaborations, media inquiries, and general questions.',
        'image' => 'images/og-default.jpg',
    ],
    'profile'  => [
        'desc'  => 'Kutumba band profile — the story, instruments, and musical heritage of Nepal\'s folk instrumental ensemble.',
        'image' => 'images/og-default.jpg',
    ],
];

$metaDesc = $pageMeta[$page]['desc']  ?? '';
$ogImage  = $pageMeta[$page]['image'] ?? 'images/og-default.jpg';

// ── Dispatch ──────────────────────────────────────────────────────────────────

require $routes[$page];
