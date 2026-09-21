<?php
require_once 'db.php';

$pdo = getDB();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(100) NOT NULL,
        email      VARCHAR(150) NOT NULL UNIQUE,
        age        INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Seed some data (only if table is empty)
$count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($count == 0) {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, age) VALUES (?, ?, ?)");
    $stmt->execute(["Alice",   "alice@example.com",   28]);
    $stmt->execute(["Bob",     "bob@example.com",     34]);
    $stmt->execute(["Carol",   "carol@example.com",   22]);
    $stmt->execute(["David",   "david@example.com",   30]);
    $stmt->execute(["Eve",     "eve@example.com",     26]);
}

echo "Database & table ready. <a href='index.php'>Go to CRUD</a>";
