# PHP Database (MySQL with PDO)

## Why PDO?

- Works with multiple databases (MySQL, PostgreSQL, SQLite, etc.)
- Supports prepared statements (prevents SQL injection)
- Consistent API regardless of the database driver

## Connecting to MySQL

```php
<?php
$host   = 'localhost';
$dbname = 'myapp';
$user   = 'root';
$pass   = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Never expose DB errors to users in production
    throw new RuntimeException("DB connection failed.", 0, $e);
}
?>
```

## SELECT — Fetch All Rows

```php
<?php
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(); // array of assoc arrays

foreach ($users as $user) {
    echo $user['name'] . "\n";
}
?>
```

## SELECT with Parameters (Prepared Statements)

```php
<?php
// Positional placeholders
$stmt = $pdo->prepare("SELECT * FROM users WHERE age > ? AND city = ?");
$stmt->execute([18, "Kathmandu"]);
$users = $stmt->fetchAll();

// Named placeholders (recommended — clearer)
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':email' => 'rahul@example.com']);
$user = $stmt->fetch(); // single row
?>
```

## INSERT

```php
<?php
$stmt = $pdo->prepare("INSERT INTO users (name, email, age) VALUES (:name, :email, :age)");
$stmt->execute([
    ':name'  => 'Rahul',
    ':email' => 'rahul@example.com',
    ':age'   => 25,
]);

$newId = $pdo->lastInsertId(); // ID of the inserted row
echo "Inserted ID: $newId";
?>
```

## UPDATE

```php
<?php
$stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
$stmt->execute([
    ':name' => 'Rahul Khanal',
    ':id'   => 1,
]);

$rowsAffected = $stmt->rowCount();
echo "Updated $rowsAffected row(s)";
?>
```

## DELETE

```php
<?php
$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute([':id' => 1]);

echo "Deleted " . $stmt->rowCount() . " row(s)";
?>
```

## Transactions

```php
<?php
try {
    $pdo->beginTransaction();

    $pdo->prepare("UPDATE accounts SET balance = balance - 500 WHERE id = 1")->execute();
    $pdo->prepare("UPDATE accounts SET balance = balance + 500 WHERE id = 2")->execute();

    $pdo->commit();
    echo "Transfer successful.";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Transfer failed: " . $e->getMessage();
}
?>
```

## Fetch Modes

```php
<?php
// Associative array (default with FETCH_ASSOC option set)
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// ["id" => 1, "name" => "Rahul"]

// Numeric array
$user = $stmt->fetch(PDO::FETCH_NUM);
// [1, "Rahul"]

// Both
$user = $stmt->fetch(PDO::FETCH_BOTH);

// stdClass object
$user = $stmt->fetch(PDO::FETCH_OBJ);
echo $user->name;

// Fetch into a specific class
$user = $stmt->fetchObject(User::class);
?>
```

## Reusable DB Connection (Singleton Pattern)

```php
<?php
// db.php
function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=myapp;charset=utf8mb4",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }

    return $pdo;
}

// Usage in any file:
require_once 'db.php';
$pdo = getDB();
?>
```

## Complete CRUD Example

```php
<?php
require_once 'db.php';

class UserRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function findAll(): array {
        return $this->pdo->query("SELECT * FROM users ORDER BY id")->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $email): int {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, string $email): bool {
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $id]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}

// Usage
$repo = new UserRepository();

$id = $repo->create("Rahul", "rahul@example.com");
$user = $repo->findById($id);
$repo->update($id, "Rahul Khanal", "rahul@example.com");
$all = $repo->findAll();
$repo->delete($id);
?>
```

## Creating Tables (DDL)

```php
<?php
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id    INT AUTO_INCREMENT PRIMARY KEY,
        name  VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        age   INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");
?>
```
