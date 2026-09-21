# PHP Forms

## HTML Form Basics

```html
<!-- GET: data visible in URL -->
<form method="GET" action="process.php">
    <input type="text" name="username">
    <button type="submit">Submit</button>
</form>

<!-- POST: data in request body -->
<form method="POST" action="process.php">
    <input type="text" name="username">
    <button type="submit">Submit</button>
</form>
```

## Accessing Form Data

```php
<?php
// GET data
$username = $_GET['username'] ?? '';

// POST data
$username = $_POST['username'] ?? '';

// Both GET and POST
$username = $_REQUEST['username'] ?? '';
?>
```

## Input Validation

```php
<?php
$errors = [];

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$age = $_POST['age'] ?? '';

if (empty($name)) {
    $errors[] = "Name is required.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address.";
}

if (!is_numeric($age) || $age < 0 || $age > 120) {
    $errors[] = "Age must be between 0 and 120.";
}

if (empty($errors)) {
    // process the form
    echo "Valid input!";
} else {
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
}
?>
```

## Sanitizing Input

```php
<?php
// Remove HTML tags
$name = strip_tags($_POST['name'] ?? '');

// Encode special HTML chars (prevent XSS)
$name = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');

// filter_var sanitizers
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$url   = filter_var($_POST['url'],   FILTER_SANITIZE_URL);
$int   = filter_var($_POST['age'],   FILTER_SANITIZE_NUMBER_INT);
?>
```

## filter_var Validators

```php
<?php
filter_var($email, FILTER_VALIDATE_EMAIL);   // bool
filter_var($url,   FILTER_VALIDATE_URL);     // bool
filter_var($ip,    FILTER_VALIDATE_IP);      // bool
filter_var($val,   FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 100]]);
?>
```

## Complete Form Example

```php
<?php
// process.php

$name = $email = $message = "";
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email   = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

    if (empty($name))    $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if (strlen($message) < 10) $errors[] = "Message too short.";

    if (empty($errors)) {
        $success = true;
        // save to DB, send email, etc.
    }
}
?>

<!DOCTYPE html>
<html>
<body>

<?php if ($success): ?>
    <p>Thank you, <?= $name ?>! We'll contact you at <?= $email ?>.</p>
<?php else: ?>

    <?php foreach ($errors as $error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endforeach; ?>

    <form method="POST">
        <input type="text" name="name" value="<?= $name ?>" placeholder="Name"><br>
        <input type="email" name="email" value="<?= $email ?>" placeholder="Email"><br>
        <textarea name="message"><?= $message ?></textarea><br>
        <button type="submit">Send</button>
    </form>

<?php endif; ?>

</body>
</html>
```

## File Uploads

```html
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="photo">
    <button type="submit">Upload</button>
</form>
```

```php
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];

    // $_FILES structure:
    // $file['name']     - original filename
    // $file['type']     - MIME type
    // $file['tmp_name'] - temp path on server
    // $file['error']    - error code
    // $file['size']     - size in bytes

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Upload failed.";
    } elseif (!in_array($file['type'], $allowedTypes)) {
        echo "Only JPG, PNG, GIF allowed.";
    } elseif ($file['size'] > $maxSize) {
        echo "File too large.";
    } else {
        $dest = "uploads/" . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $dest);
        echo "Uploaded to $dest";
    }
}
?>
```

## Sessions

```php
<?php
session_start(); // must be before any output

// Set session variables
$_SESSION['user_id'] = 42;
$_SESSION['username'] = "Rahul";

// Read
echo $_SESSION['username'];

// Remove one
unset($_SESSION['username']);

// Destroy entire session (logout)
session_destroy();
?>
```

## Cookies

```php
<?php
// Set cookie (name, value, expiry, path, domain, secure, httponly)
setcookie("theme", "dark", time() + 86400 * 30, "/");

// Read
$theme = $_COOKIE['theme'] ?? 'light';

// Delete (set expiry in the past)
setcookie("theme", "", time() - 3600, "/");
?>
```
