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

// $_GET['page'] is untrusted user input — only use it to look up our whitelist.
$page = $_GET['page'] ?? 'home';

// array_key_exists: check key exists (safer than isset for explicit null values)
if (!array_key_exists($page, $routes)) {
    $page = 'home'; // unknown pages fall back to home (or you could show a 404)
}

// ── Dispatch ──────────────────────────────────────────────────────────────────

// require the resolved, whitelisted file path — safe.
require $routes[$page];
