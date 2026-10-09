<?php
/**
 * ADMIN LOGIN
 *
 * PHP CONCEPTS HERE:
 *  - $_SERVER['REQUEST_METHOD'] to distinguish GET vs POST
 *  - password_verify() — never compare plaintext; always hash
 *  - session_regenerate_id(true) prevents session fixation attacks
 *  - PRG: redirect after successful POST to prevent double-submit
 */

use App\Database\Connection;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $pdo  = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // password_verify() compares the plaintext against the stored bcrypt hash
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true); // prevents session fixation
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_user'] = $user['username'];
            header('Location: index.php?page=dashboard');
            exit;
        }
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login — Kutumba</title>
  <link rel="stylesheet" href="../css/app.css">
  <style>
    body { background: #f4f4f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    .login-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 48px 40px;
      width: 100%;
      max-width: 400px;
      box-shadow: var(--shadow-lg);
    }
    .login-card h1 { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 800; color: var(--text); margin-bottom: 6px; }
    .login-card p  { font-size: .85rem; color: var(--muted); margin-bottom: 32px; }
    .login-logo { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
    .login-logo img { height: 36px; }
    .login-logo span { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-logo">
      <img src="../images/logo.svg" alt="Kutumba">
      <span>Admin Panel</span>
    </div>

    <h1>Sign In</h1>
    <p>Manage Kutumba website content</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="admin" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-orange" style="width:100%;justify-content:center;margin-top:8px">
        Sign In
      </button>
    </form>
  </div>
</body>
</html>
