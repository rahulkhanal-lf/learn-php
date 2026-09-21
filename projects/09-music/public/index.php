<?php

// Bootstrap — load autoloader and .env
require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Required env vars — throws if missing
$dotenv->required(['DB_HOST', 'DB_DATABASE', 'DB_USERNAME']);

// Simple front controller — route by ?action=
use App\Controllers\SongController;

$action = $_GET['action'] ?? 'index';
$controller = new SongController();

match($action) {
    'store'  => $controller->store(),
    'delete' => $controller->delete(),
    default  => $controller->index(),
};
