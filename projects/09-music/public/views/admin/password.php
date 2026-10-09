<?php
/**
 * ADMIN: Change Password
 *
 * PHP CONCEPTS HERE:
 *  - password_verify() — check old password against stored bcrypt hash
 *  - password_hash()   — hash the new password before storing
 *  - Never store plaintext passwords — always hash with PASSWORD_DEFAULT
 *  - PRG: redirect after successful POST to prevent double-submit
 */

use App\Database\Connection;

$pdo   = Connection::getInstance();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password']     ?? '';
    $newPassword = $_POST['new_password']     ?? '';
    $confirm     = $_POST['confirm_password'] ?? '';

    // Fetch current hash for the logged-in admin
    $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $currentHash = $stmt->fetchColumn();

    if (!password_verify($oldPassword, $currentHash)) {
        $error = 'Old password is incorrect.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($newPassword !== $confirm) {
        $error = 'New passwords do not match.';
    } else {
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?")
            ->execute([$newHash, $_SESSION['admin_id']]);

        $_SESSION['flash'] = 'Password changed successfully.';
        header('Location: index.php?page=password');
        exit;
    }
}

$adminTitle = 'Change Password';
$activePage = 'password';
ob_start();
?>

<div class="admin-header"><h1>Change Password</h1></div>

<div class="admin-panel" style="max-width:480px">
  <h2>Update your password</h2>

  <?php if ($error): ?>
    <div class="admin-flash admin-flash--err"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label>Old Password</label>
      <input type="password" name="old_password" required autofocus>
    </div>
    <div class="form-group">
      <label>New Password <span style="color:var(--muted);font-weight:400;text-transform:none">(min 6 chars)</span></label>
      <input type="password" name="new_password" required minlength="6">
    </div>
    <div class="form-group">
      <label>Confirm New Password</label>
      <input type="password" name="confirm_password" required minlength="6">
    </div>
    <button type="submit" class="btn btn-orange" style="margin-top:8px">Change Password</button>
  </form>
</div>

<?php
$adminContent = ob_get_clean();
require BASE_PATH . '/views/admin/_layout.php';
