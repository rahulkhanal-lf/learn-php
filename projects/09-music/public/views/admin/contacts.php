<?php
use App\Database\Connection;

$pdo = Connection::getInstance();

// Allow deleting a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action']??'') === 'delete') {
    $pdo->prepare("DELETE FROM contacts WHERE id=?")->execute([(int)$_POST['id']]);
    $_SESSION['flash'] = 'Message deleted.';
    header('Location: index.php?page=contacts'); exit;
}

$messages = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();

$adminTitle = 'Contact Messages'; $activePage = 'contacts'; ob_start();
?>
<div class="admin-header">
  <h1>Contact Messages</h1>
  <span style="font-size:.88rem;color:var(--muted)"><?= count($messages) ?> total</span>
</div>

<?php if (empty($messages)): ?>
  <div class="admin-panel"><p style="color:var(--muted)">No messages yet.</p></div>
<?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($messages as $msg): ?>
          <tr>
            <td style="color:var(--muted)"><?= $msg['id'] ?></td>
            <td><?= htmlspecialchars($msg['name'],ENT_QUOTES,'UTF-8') ?></td>
            <td><a href="mailto:<?= htmlspecialchars($msg['email'],ENT_QUOTES,'UTF-8') ?>" style="color:var(--orange)"><?= htmlspecialchars($msg['email'],ENT_QUOTES,'UTF-8') ?></a></td>
            <td style="max-width:360px">
              <details>
                <summary style="cursor:pointer;font-size:.82rem;color:var(--muted)">View message</summary>
                <p style="font-size:.85rem;margin-top:8px;white-space:pre-wrap"><?= htmlspecialchars($msg['message']??'',ENT_QUOTES,'UTF-8') ?></p>
              </details>
            </td>
            <td style="white-space:nowrap;color:var(--muted);font-size:.82rem"><?= $msg['created_at'] ?></td>
            <td>
              <form method="POST" onsubmit="return confirm('Delete this message?')">
                <input type="hidden" name="_action" value="delete">
                <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                <button type="submit" class="action-link action-link--delete" style="background:none;cursor:pointer;font-family:inherit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php $adminContent = ob_get_clean(); require BASE_PATH . '/views/admin/_layout.php';
