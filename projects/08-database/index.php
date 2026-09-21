<?php
require_once 'db.php';

$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int) ($_GET['id'] ?? 0);
$errors = [];
$msg    = $_GET['msg'] ?? '';

// ─── Handle POST (create / update) ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = htmlspecialchars(trim($_POST['name']  ?? ''), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
    $age   = (int) ($_POST['age'] ?? 0);

    if (strlen($name) < 2)                         $errors[] = "Name too short.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if ($age < 1 || $age > 120)                    $errors[] = "Age must be 1–120.";

    if (empty($errors)) {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, age) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $age]);
        } elseif ($action === 'edit' && $id) {
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, age=? WHERE id=?");
            $stmt->execute([$name, $email, $age, $id]);
        }
        header("Location: index.php?msg=saved");
        exit;
    }
}

// ─── Handle Delete ────────────────────────────────────────────────────────────
if ($action === 'delete' && $id) {
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    header("Location: index.php?msg=deleted");
    exit;
}

// ─── Fetch for edit ───────────────────────────────────────────────────────────
$editUser = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $editUser = $stmt->fetch();
}

// ─── Fetch all users ─────────────────────────────────────────────────────────
$users = $pdo->query("SELECT * FROM users ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users CRUD</title>
    <style>
        body { font-family: sans-serif; max-width: 760px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #f4f4f4; }
        input { padding: 6px; width: 100%; box-sizing: border-box; margin-bottom: 6px; }
        button { padding: 7px 18px; }
        .btn-del { color: red; }
        .msg { background: #e6f4ea; padding: 10px; margin-bottom: 12px; }
        .errors { background: #fce8e6; padding: 10px; margin-bottom: 12px; }
    </style>
</head>
<body>

<h2>Users CRUD — PDO MySQL</h2>

<?php if ($msg): ?>
    <div class="msg"><?= $msg === 'saved' ? 'Saved successfully.' : 'Deleted.' ?></div>
<?php endif; ?>

<!-- ── Form ── -->
<h3><?= $action === 'edit' ? 'Edit User' : 'Add User' ?></h3>

<?php if ($errors): ?>
    <div class="errors"><?= implode('<br>', $errors) ?></div>
<?php endif; ?>

<form method="POST" action="index.php?action=<?= $action ?><?= $id ? "&id=$id" : '' ?>">
    <input type="text"   name="name"  placeholder="Name"  value="<?= htmlspecialchars($editUser['name']  ?? '') ?>">
    <input type="email"  name="email" placeholder="Email" value="<?= htmlspecialchars($editUser['email'] ?? '') ?>">
    <input type="number" name="age"   placeholder="Age"   value="<?= htmlspecialchars($editUser['age']   ?? '') ?>">
    <button type="submit"><?= $action === 'edit' ? 'Update' : 'Create' ?></button>
    <?php if ($action === 'edit'): ?>
        <a href="index.php">Cancel</a>
    <?php endif; ?>
</form>

<!-- ── Table ── -->
<h3>All Users (<?= count($users) ?>)</h3>
<table>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Age</th><th>Created</th><th>Actions</th></tr>
    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= $u['age'] ?></td>
        <td><?= $u['created_at'] ?></td>
        <td>
            <a href="index.php?action=edit&id=<?= $u['id'] ?>">Edit</a> |
            <a href="index.php?action=delete&id=<?= $u['id'] ?>"
               class="btn-del"
               onclick="return confirm('Delete <?= htmlspecialchars($u['name']) ?>?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
