<?php
use App\Database\Connection;

$pdo = Connection::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';

    // Create a new archive year
    if ($act === 'create_year') {
        $pdo->prepare("INSERT INTO archives (year) VALUES (?)")->execute([(int)$_POST['year']]);
        $_SESSION['flash'] = 'Archive year added.';
    }
    // Add entry to an existing archive
    if ($act === 'add_entry') {
        $pdo->prepare("INSERT INTO archive_entries (archive_id, entry) VALUES (?,?)")
            ->execute([(int)$_POST['archive_id'], trim($_POST['entry'])]);
        $_SESSION['flash'] = 'Entry added.';
    }
    // Delete an entry
    if ($act === 'delete_entry') {
        $pdo->prepare("DELETE FROM archive_entries WHERE id=?")->execute([(int)$_POST['id']]);
        $_SESSION['flash'] = 'Entry deleted.';
    }
    // Delete an entire archive year (cascades to entries via FK)
    if ($act === 'delete_year') {
        $pdo->prepare("DELETE FROM archives WHERE id=?")->execute([(int)$_POST['id']]);
        $_SESSION['flash'] = 'Archive year deleted.';
    }
    header('Location: index.php?page=archives'); exit;
}

$archives = $pdo->query("SELECT * FROM archives ORDER BY year ASC")->fetchAll();
foreach ($archives as &$arch) {
    $s = $pdo->prepare("SELECT * FROM archive_entries WHERE archive_id=? ORDER BY id ASC");
    $s->execute([$arch['id']]);
    $arch['entries'] = $s->fetchAll();
}

$adminTitle = 'Archives'; $activePage = 'archives'; ob_start();
?>
<div class="admin-header"><h1>Archives</h1></div>

<!-- Add year -->
<div class="admin-panel">
  <h2>Add Archive Year</h2>
  <form method="POST" style="display:flex;gap:16px;align-items:flex-end">
    <input type="hidden" name="_action" value="create_year">
    <div class="form-group" style="margin:0;flex:0 0 160px">
      <label>Year</label>
      <input type="number" name="year" required placeholder="2024">
    </div>
    <button type="submit" class="btn btn-orange">Add Year</button>
  </form>
</div>

<!-- Each archive year with entries -->
<?php foreach ($archives as $arch): ?>
  <div class="admin-panel" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <h2 style="margin:0;border:0;padding:0"><?= (int)$arch['year'] ?></h2>
      <form method="POST" onsubmit="return confirm('Delete this entire year and all entries?')">
        <input type="hidden" name="_action" value="delete_year">
        <input type="hidden" name="id" value="<?= $arch['id'] ?>">
        <button type="submit" class="action-link action-link--delete" style="background:none;cursor:pointer;font-family:inherit">Delete Year</button>
      </form>
    </div>

    <!-- Existing entries -->
    <?php foreach ($arch['entries'] as $entry): ?>
      <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:.88rem;color:var(--text)">— <?= htmlspecialchars($entry['entry'],ENT_QUOTES,'UTF-8') ?></span>
        <form method="POST" style="margin:0" onsubmit="return confirm('Delete entry?')">
          <input type="hidden" name="_action" value="delete_entry">
          <input type="hidden" name="id" value="<?= $entry['id'] ?>">
          <button type="submit" class="action-link action-link--delete" style="background:none;cursor:pointer;font-family:inherit;font-size:.72rem">✕</button>
        </form>
      </div>
    <?php endforeach; ?>

    <!-- Add entry form -->
    <form method="POST" style="display:flex;gap:12px;align-items:flex-end;margin-top:16px">
      <input type="hidden" name="_action" value="add_entry">
      <input type="hidden" name="archive_id" value="<?= $arch['id'] ?>">
      <div class="form-group" style="margin:0;flex:1">
        <label>New entry</label>
        <input type="text" name="entry" placeholder="e.g. First international tour" required>
      </div>
      <button type="submit" class="btn btn-orange" style="padding:10px 18px">Add</button>
    </form>
  </div>
<?php endforeach; ?>

<?php $adminContent = ob_get_clean(); require BASE_PATH . '/views/admin/_layout.php';
