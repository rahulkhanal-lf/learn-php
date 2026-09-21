<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use App\Database\Connection;

$pdo = Connection::getInstance();

// Create database if it doesn't exist (connect without dbname first)
$config = require dirname(__DIR__) . '/config/database.php';
$rootPdo = new PDO(
    "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}",
    $config['username'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
$rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

// Now use the app connection
$pdo->exec("
    CREATE TABLE IF NOT EXISTS artists (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(150) NOT NULL,
        country    VARCHAR(100),
        genre      VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$pdo->exec("
    CREATE TABLE IF NOT EXISTS songs (
        id               INT AUTO_INCREMENT PRIMARY KEY,
        title            VARCHAR(200) NOT NULL,
        artist_id        INT NOT NULL,
        album            VARCHAR(200),
        genre            VARCHAR(100),
        duration_seconds INT,
        release_year     YEAR,
        created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE
    )
");

// Seed artists
$artistCount = $pdo->query("SELECT COUNT(*) FROM artists")->fetchColumn();
if ($artistCount == 0) {
    $stmt = $pdo->prepare("INSERT INTO artists (name, country, genre) VALUES (?, ?, ?)");
    $stmt->execute(["The Beatles",    "UK",        "Rock"]);
    $stmt->execute(["Taylor Swift",   "USA",       "Pop"]);
    $stmt->execute(["Kendrick Lamar", "USA",       "Hip-Hop"]);
    $stmt->execute(["Daft Punk",      "France",    "Electronic"]);
    $stmt->execute(["Bob Marley",     "Jamaica",   "Reggae"]);
}

// Seed songs
$songCount = $pdo->query("SELECT COUNT(*) FROM songs")->fetchColumn();
if ($songCount == 0) {
    $artists = $pdo->query("SELECT id, name FROM artists")->fetchAll(PDO::FETCH_KEY_PAIR);
    $byName  = array_flip($artists);

    $stmt = $pdo->prepare("INSERT INTO songs (title, artist_id, album, genre, duration_seconds, release_year) VALUES (?,?,?,?,?,?)");
    $stmt->execute(["Hey Jude",        $byName["The Beatles"],    "Non-album single",    "Rock",       431, 1968]);
    $stmt->execute(["Let It Be",       $byName["The Beatles"],    "Let It Be",           "Rock",       243, 1970]);
    $stmt->execute(["Shake It Off",    $byName["Taylor Swift"],   "1989",                "Pop",        219, 2014]);
    $stmt->execute(["Blinding Lights", $byName["Taylor Swift"],   "Lover",               "Pop",        200, 2019]);
    $stmt->execute(["HUMBLE.",         $byName["Kendrick Lamar"], "DAMN.",               "Hip-Hop",    177, 2017]);
    $stmt->execute(["Get Lucky",       $byName["Daft Punk"],      "Random Access Memo.", "Electronic", 248, 2013]);
    $stmt->execute(["No Woman No Cry", $byName["Bob Marley"],     "Natty Dread",         "Reggae",     285, 1974]);
}

echo "<pre style='font-family:monospace;background:#1e1e1e;color:#86efac;padding:20px;'>";
echo "✅ Database '{$config['database']}' ready.\n";
echo "✅ Tables: artists, songs created.\n";
echo "✅ Seed data inserted.\n\n";
echo "👉 <a href='index.php' style='color:#a78bfa'>Open Music App →</a>";
echo "</pre>";
