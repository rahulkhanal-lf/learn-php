<?php
/**
 * SETUP — Creates DB, tables, and seeds Kutumba data.
 * Run once: http://localhost/learn-php/projects/09-music/public/setup.php
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$config = require dirname(__DIR__) . '/config/database.php';

// Connect without dbname to create the database first
$dsn = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";

$root = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$root->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

use App\Database\Connection;
$pdo = Connection::getInstance();

// ── Tables ────────────────────────────────────────────────────────────────────

$pdo->exec("CREATE TABLE IF NOT EXISTS members (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    instrument    VARCHAR(150),
    bio           TEXT,
    photo         VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS instruments (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150) NOT NULL,
    description   TEXT,
    photo         VARCHAR(255),
    display_order INT DEFAULT 0
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS events (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(200) NOT NULL,
    year          YEAR,
    date_text     VARCHAR(200),
    location      VARCHAR(255),
    image         VARCHAR(255),
    is_upcoming   TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS albums (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200) NOT NULL,
    year        YEAR,
    cover_image VARCHAR(255),
    description TEXT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS initiatives (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    year      YEAR,
    title     VARCHAR(200),
    date_text VARCHAR(200),
    locations TEXT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    message    TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS projects (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(200) NOT NULL,
    description  TEXT,
    year         YEAR,
    display_order INT DEFAULT 0
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS archives (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    year YEAR NOT NULL
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS archive_entries (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    archive_id INT NOT NULL,
    entry      VARCHAR(255) NOT NULL,
    FOREIGN KEY (archive_id) REFERENCES archives(id) ON DELETE CASCADE
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ── Seed Data ─────────────────────────────────────────────────────────────────

// Members
if (!$pdo->query("SELECT COUNT(*) FROM members")->fetchColumn()) {
    $stmt = $pdo->prepare("INSERT INTO members (name, instrument, bio, photo, display_order) VALUES (?,?,?,?,?)");
    $stmt->execute(['Pavit Maharjan',  'Madal / Dhimay',     'Lead percussionist',    'placeholder.jpg', 1]);
    $stmt->execute(['Sadhu Ram Pyakurel', 'Sarangi',         'Folk strings master',   'placeholder.jpg', 2]);
    $stmt->execute(['Bikram Shretha',  'Arbajo',              'String specialist',     'placeholder.jpg', 3]);
    $stmt->execute(['Samir Joshi',     'Bansuri / Murali',    'Wind instruments',      'placeholder.jpg', 4]);
    $stmt->execute(['Kapil Sharma',    'Tungna',              'Traditional lute',      'placeholder.jpg', 5]);
    $stmt->execute(['Ramhari Adhikari','Panche Baja',         'Folk percussion',       'placeholder.jpg', 6]);
    $stmt->execute(['Sita Ghimire',    'Vocals',              'Folk vocalist',         'placeholder.jpg', 7]);
}

// Instruments
if (!$pdo->query("SELECT COUNT(*) FROM instruments")->fetchColumn()) {
    $stmt = $pdo->prepare("INSERT INTO instruments (name, description, photo, display_order) VALUES (?,?,?,?)");
    $stmt->execute(['Sarangi', 'The sarangi belongs to the Gandharva caste and was traditionally used as a storytelling instrument. It is a four-string instrument that usually produces C, C and G, G notes and is played with a bow. The structure of a Sarangi is very interesting; it has got no fret boards or fingering frets- notes are changed by putting fingers in-between the strings and shortening the length of vibrations. It has no joints and traditionally the body is carved out of a single block of Khhira wood, but is often replaced these days by Saaj wood. A wooden key is used to tune the strings and a small piece of wooden block is used as a bridge on top of the skin piece. The traditional horsetail bow is often replaced by a nylon bow now.', 'sarangi.png', 1]);
    $stmt->execute(['Arbajo', 'It is believed that the instrument Arbajo was handed down by Saint Bhrama Bharat to human beings in the Satya Yug. Back then the instrument was used to deliver messages from one place to another. The Arbajo is similar to a Guitar but slightly smaller in size. It is regarded as tshe male partner of the Sarangi and is played in consortium to produce balanced harmony. It consists of four strings that are used to produce rhythmic sounds.', 'arbajo.png', 2]);
    $stmt->execute(['Madal', 'The Madal is the most commonly used percussion instrument in Nepal. It is a barrel-shaped drum used throughout Nepal and is considered the national drum of Nepal. It produces two different tones from two sides — one provides bass notes and the other treble notes.', 'placeholder.jpg', 3]);
    $stmt->execute(['Bansuri', 'The Bansuri is a side-blown flute made of bamboo. It is one of the most ancient musical instruments of Nepal and India. It is associated with Lord Krishna and is prominently used in folk music across the Himalayan region.', 'placeholder.jpg', 4]);
}

// Initiatives
if (!$pdo->query("SELECT COUNT(*) FROM initiatives")->fetchColumn()) {
    $stmt = $pdo->prepare("INSERT INTO initiatives (year, title, date_text, locations) VALUES (?,?,?,?)");
    $stmt->execute([2008, '"PEACE SONG"',          'March 8', null]);
    $stmt->execute([2011, '"STREE SHAKTI"',         'Feb 01, Feb 02, Feb 03, Feb 04', 'Pokhara Bus Park, Narayanghat; Saracha Chowk; Habanchowk, Pokhara; Chepilachunge, Pokhara']);
    $stmt->execute([2011, '"SAVE BAGMATI"',         'Jul 18, Jul 22, Aug 29', 'Kaalmochan Ghat; Santanpul; Bagmati Bridge, Tripureshwor']);
    $stmt->execute([2011, '"COMMUNITY DEVELOPMENT"', null, null]);
    $stmt->execute([2015, '"EARTHQUAKE RELIEF"',    'April–May', 'Kathmandu, Lalitpur, Bhaktapur']);
    $stmt->execute([2019, '"FUNDRAISER CONCERT"',   'Multiple dates', 'Kathmandu Valley']);
}

// Events
if (!$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn()) {
    $stmt = $pdo->prepare("INSERT INTO events (title, year, date_text, location, image, is_upcoming, display_order) VALUES (?,?,?,?,?,?,?)");
    // Past events by year
    foreach ([2019,2018,2017,2016,2015,2014,2013,2012,2011,2010,2009,2008] as $i => $y) {
        $stmt->execute(["Annual Concert $y", $y, null, 'Kathmandu', null, 0, $i]);
    }
    // Upcoming
    $stmt->execute(['Fundraiser Concert', date('Y'), 'Coming Soon', 'Kathmandu', 'upcoming1.jpg', 1, 1]);
    $stmt->execute(['Revelry of Colors',  date('Y'), 'Coming Soon', 'Lalitpur',  'upcoming2.jpg', 1, 2]);
}

// Albums
if (!$pdo->query("SELECT COUNT(*) FROM albums")->fetchColumn()) {
    $stmt = $pdo->prepare("INSERT INTO albums (title, year, cover_image, description) VALUES (?,?,?,?)");
    $stmt->execute(['Kutumba',      2006, 'placeholder.jpg', 'Debut album']);
    $stmt->execute(['Dalla Kunda',  2009, 'placeholder.jpg', 'Second studio album']);
    $stmt->execute(['Naulo Bihani', 2012, 'placeholder.jpg', 'Third album']);
    $stmt->execute(['Mithila',      2014, 'placeholder.jpg', 'Fourth album']);
    $stmt->execute(['Dhukuti',      2017, 'placeholder.jpg', 'Fifth album']);
    $stmt->execute(['Karmaih',      2019, 'placeholder.jpg', 'Sixth album']);
    $stmt->execute(['Lau',          2022, 'placeholder.jpg', 'Seventh album']);
}

// Projects
if (!$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn()) {
    $stmt = $pdo->prepare("INSERT INTO projects (title, description, year, display_order) VALUES (?,?,?,?)");
    $stmt->execute(['Folk Fusion',              'Blending traditional Nepali folk with contemporary arrangements.',           2015, 1]);
    $stmt->execute(['School Outreach',          'Teaching traditional instruments to school children across Nepal.',         2016, 2]);
    $stmt->execute(['Archive Project',          'Recording and preserving rare folk songs from remote villages.',            2018, 3]);
    $stmt->execute(['Earthquake Relief Concert','Fundraising concert series for 2015 earthquake victims.',                   2015, 4]);
}

// Archives
if (!$pdo->query("SELECT COUNT(*) FROM archives")->fetchColumn()) {
    $archiveData = [
        [2008, ['Band formation in Kathmandu', 'First public performance at Patan']],
        [2010, ['First studio recording', 'International tour — Germany']],
        [2013, ['Album release: "Mithila"', 'Festival at Pokhara']],
        [2015, ['Earthquake relief concerts', '"Naulo Bihani" album release']],
        [2019, ['Europe tour', 'Fundraiser concert series']],
    ];
    $insArch  = $pdo->prepare("INSERT INTO archives (year) VALUES (?)");
    $insEntry = $pdo->prepare("INSERT INTO archive_entries (archive_id, entry) VALUES (?,?)");
    foreach ($archiveData as [$year, $entries]) {
        $insArch->execute([$year]);
        $archId = $pdo->lastInsertId();
        foreach ($entries as $entry) {
            $insEntry->execute([$archId, $entry]);
        }
    }
}

// Admin user (default: admin / admin123)
if (!$pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn()) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?,?)")
        ->execute(['admin', $hash]);
}

echo "<pre style='background:#161616;color:#86efac;padding:24px;font-family:monospace;font-size:.9rem'>";
echo "✅ Database '{$config['database']}' ready\n";
echo "✅ Tables: members, instruments, events, albums, initiatives, contacts, projects, archives, admin_users\n";
echo "✅ Seed data inserted\n\n";
echo "👉 <a href='index.php' style='color:#ff6600'>Open Kutumba Clone →</a>";
echo "</pre>";
