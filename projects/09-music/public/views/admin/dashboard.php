<?php
use App\Database\Connection;

$pdo = Connection::getInstance();

$counts = [
    'Members'     => $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn(),
    'Instruments' => $pdo->query("SELECT COUNT(*) FROM instruments")->fetchColumn(),
    'Events'      => $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn(),
    'Albums'      => $pdo->query("SELECT COUNT(*) FROM albums")->fetchColumn(),
    'Projects'    => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'Initiatives' => $pdo->query("SELECT COUNT(*) FROM initiatives")->fetchColumn(),
    'Archives'    => $pdo->query("SELECT COUNT(*) FROM archives")->fetchColumn(),
    'Messages'    => $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
];

$recentMessages = $pdo->query(
    "SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5"
)->fetchAll();

$adminTitle  = 'Dashboard';
$activePage  = 'dashboard';

ob_start();
?>
<div class="admin-header">
  <h1>Dashboard</h1>
  <a href="../../learn-php/projects/09-music/public/index.php" class="btn btn-outline" target="_blank">View Site ↗</a>
</div>

<div class="admin-stats">
  <?php foreach ($counts as $label => $count): ?>
    <div class="stat-card">
      <div class="stat-card__num"><?= (int)$count ?></div>
      <div class="stat-card__label"><?= $label ?></div>
    </div>
  <?php endforeach; ?>
</div>

<div class="admin-panel">
  <h2>Recent Contact Messages</h2>
  <?php if (empty($recentMessages)): ?>
    <p style="color:var(--muted);font-size:.88rem">No messages yet.</p>
  <?php else: ?>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead><tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($recentMessages as $msg): ?>
            <tr>
              <td><?= htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8') ?></td>
              <td style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                <?= htmlspecialchars($msg['message'] ?? '', ENT_QUOTES, 'UTF-8') ?>
              </td>
              <td style="white-space:nowrap;color:var(--muted)"><?= $msg['created_at'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php
$adminContent = ob_get_clean();
require BASE_PATH . '/views/admin/_layout.php';
