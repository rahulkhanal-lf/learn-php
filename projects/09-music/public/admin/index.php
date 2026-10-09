<?php
/**
 * ADMIN FRONT CONTROLLER
 *
 * PHP CONCEPTS HERE:
 *  - session_start() for persistent login state
 *  - Whitelist routing (same security pattern as public/index.php)
 *  - Auth guard: redirect to login if not authenticated
 *  - password_verify() to safely check hashed passwords
 */

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

define('BASE_PATH',  dirname(__DIR__));
define('ADMIN_PATH', __DIR__);

session_start();

$page   = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? null;

// ── Auth guard ─────────────────────────────────────────────────────────────
// Every route except login requires a valid session.
if ($page !== 'login' && empty($_SESSION['admin_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// ── Route whitelist ────────────────────────────────────────────────────────
$routes = [
    'login'       => BASE_PATH . '/views/admin/login.php',
    'logout'      => BASE_PATH . '/views/admin/logout.php',
    'dashboard'   => BASE_PATH . '/views/admin/dashboard.php',
    'members'     => BASE_PATH . '/views/admin/members.php',
    'instruments' => BASE_PATH . '/views/admin/instruments.php',
    'events'      => BASE_PATH . '/views/admin/events.php',
    'albums'      => BASE_PATH . '/views/admin/albums.php',
    'initiatives' => BASE_PATH . '/views/admin/initiatives.php',
    'projects'    => BASE_PATH . '/views/admin/projects.php',
    'archives'    => BASE_PATH . '/views/admin/archives.php',
    'contacts'    => BASE_PATH . '/views/admin/contacts.php',
    'gallery'     => BASE_PATH . '/views/admin/gallery.php',
    'password'    => BASE_PATH . '/views/admin/password.php',
];

if (!array_key_exists($page, $routes)) {
    $page = 'dashboard';
}

require $routes[$page];
