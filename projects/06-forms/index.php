<?php
session_start();

$fields  = ["name" => "", "email" => "", "age" => "", "message" => ""];
$errors  = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = htmlspecialchars(trim($_POST['name']    ?? ''), ENT_QUOTES, 'UTF-8');
    $email   = htmlspecialchars(trim($_POST['email']   ?? ''), ENT_QUOTES, 'UTF-8');
    $age     = trim($_POST['age'] ?? '');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

    if (empty($name) || strlen($name) < 2) {
        $errors['name'] = "Name must be at least 2 characters.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Enter a valid email address.";
    }
    if (!is_numeric($age) || $age < 1 || $age > 120) {
        $errors['age'] = "Age must be between 1 and 120.";
    }
    if (strlen($message) < 10) {
        $errors['message'] = "Message must be at least 10 characters.";
    }

    if (empty($errors)) {
        $_SESSION['last_submission'] = [
            "name"    => $name,
            "email"   => $email,
            "age"     => (int) $age,
            "message" => $message,
        ];
        $success = true;
    } else {
        $fields = compact('name', 'email', 'age', 'message');
    }
}

function err(array $errors, string $field): string {
    return isset($errors[$field])
        ? "<span style='color:red;font-size:0.85em'>{$errors[$field]}</span>"
        : "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Forms</title>
    <style>
        body { font-family: sans-serif; max-width: 540px; margin: 40px auto; }
        input, textarea { width: 100%; padding: 8px; margin: 4px 0 2px; box-sizing: border-box; }
        button { padding: 10px 24px; background: #0078d4; color: #fff; border: none; cursor: pointer; }
        .success { background: #e6f4ea; border: 1px solid #34a853; padding: 16px; }
        pre { background: #f4f4f4; padding: 12px; }
    </style>
</head>
<body>

<h2>Contact Form</h2>

<?php if ($success): ?>
    <div class="success">
        <strong>Submitted successfully!</strong>
        <pre><?= json_encode($_SESSION['last_submission'], JSON_PRETTY_PRINT) ?></pre>
        <a href="index.php">Submit another</a>
    </div>
<?php else: ?>
    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?= $fields['name'] ?>">
        <?= err($errors, 'name') ?>

        <label>Email</label>
        <input type="email" name="email" value="<?= $fields['email'] ?>">
        <?= err($errors, 'email') ?>

        <label>Age</label>
        <input type="number" name="age" value="<?= $fields['age'] ?>">
        <?= err($errors, 'age') ?>

        <label>Message</label>
        <textarea name="message" rows="4"><?= $fields['message'] ?></textarea>
        <?= err($errors, 'message') ?>

        <br><button type="submit">Send</button>
    </form>
<?php endif; ?>

</body>
</html>
