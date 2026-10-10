<?php
/**
 * ADMIN: Contact Messages (inbox)
 *
 * PHP CONCEPTS HERE:
 *  - is_read column: track read/unread state (like an email inbox)
 *  - Mark as read on expand (JS + fetch to a PHP endpoint)
 *  - Unread count badge in the sidebar via shared query
 */

use App\Database\Connection;

$pdo = Connection::getInstance();

// ── Mark as read (called via fetch from JS) ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'mark_read') {
    $pdo->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?")
        ->execute([(int)$_POST['id']]);
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

// ── Delete ────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'delete') {
    $pdo->prepare("DELETE FROM contacts WHERE id=?")->execute([(int)$_POST['id']]);
    $_SESSION['flash'] = 'Message deleted.';
    header('Location: index.php?page=contacts'); exit;
}

$messages = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
$unread   = array_filter($messages, fn($m) => !$m['is_read']);

$adminTitle = 'Contact Messages'; $activePage = 'contacts'; ob_start();
?>
<div class="admin-header">
  <h1>Contact Messages</h1>
  <span style="font-size:.88rem;color:var(--muted)"><?= count($messages) ?> total
    <?php if ($unread): ?>
      · <span style="color:var(--orange);font-weight:700"><?= count($unread) ?> unread</span>
    <?php endif; ?>
  </span>
</div>

<?php if (empty($messages)): ?>
  <div class="admin-panel">
    <p style="color:var(--muted)">No messages yet. When someone submits the contact form, their message appears here — and is also emailed to you.</p>
  </div>
<?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width:8px"></th>
          <th>Name</th>
          <th>Email</th>
          <th>Message</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($messages as $msg): ?>
          <?php $unreadRow = !$msg['is_read']; ?>
          <tr id="msg-<?= $msg['id'] ?>" style="<?= $unreadRow ? 'background:#fffbf5' : '' ?>">

            <!-- Unread dot indicator -->
            <td>
              <?php if ($unreadRow): ?>
                <span id="dot-<?= $msg['id'] ?>"
                      style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--orange)"
                      title="Unread"></span>
              <?php endif; ?>
            </td>

            <td style="font-weight:<?= $unreadRow ? '700' : '400' ?>">
              <?= htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8') ?>
            </td>

            <td>
              <a href="mailto:<?= htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8') ?>"
                 style="color:var(--orange)">
                <?= htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8') ?>
              </a>
            </td>

            <td style="max-width:380px">
              <!--
                CONCEPT: <details> / <summary>
                  Native HTML collapsible — no JS needed to toggle open/close.
                  We use the `toggle` event to mark the message read via fetch()
                  the first time it's expanded.
              -->
              <details data-id="<?= $msg['id'] ?>" data-read="<?= $msg['is_read'] ? '1' : '0' ?>"
                       class="msg-details">
                <summary style="cursor:pointer;font-size:.82rem;color:var(--muted)">
                  <?= $unreadRow ? '<strong>New message</strong>' : 'View message' ?>
                </summary>
                <p style="font-size:.85rem;margin-top:8px;white-space:pre-wrap;line-height:1.6">
                  <?= htmlspecialchars($msg['message'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>
              </details>
            </td>

            <td style="white-space:nowrap;color:var(--muted);font-size:.82rem">
              <?= date('M j, Y H:i', strtotime($msg['created_at'])) ?>
            </td>

            <td>
              <form method="POST" onsubmit="return confirm('Delete this message?')" style="margin:0">
                <input type="hidden" name="_action" value="delete">
                <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                <button type="submit" class="action-link action-link--delete"
                        style="background:none;cursor:pointer;font-family:inherit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<script>
// Mark a message as read the first time its <details> is expanded
document.querySelectorAll('.msg-details').forEach(el => {
    el.addEventListener('toggle', function () {
        if (!this.open) return;
        if (this.dataset.read === '1') return; // already read

        const id  = this.dataset.id;
        const row = document.getElementById('msg-' + id);
        const dot = document.getElementById('dot-' + id);

        const form = new FormData();
        form.append('_action', 'mark_read');
        form.append('id', id);

        fetch('index.php?page=contacts', { method: 'POST', body: form })
            .then(r => r.json())
            .then(() => {
                this.dataset.read = '1';
                // Visually clear unread state
                if (dot)  dot.remove();
                if (row) {
                    row.style.background = '';
                    const nameCell = row.querySelector('td:nth-child(2)');
                    if (nameCell) nameCell.style.fontWeight = '400';
                }
                const summary = this.querySelector('summary');
                if (summary) summary.innerHTML = 'View message';
            });
    });
});
</script>

<?php $adminContent = ob_get_clean(); require BASE_PATH . '/views/admin/_layout.php';
