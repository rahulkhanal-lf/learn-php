<?php
use App\Database\Connection;

$pdo = Connection::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';
    if ($act === 'create') {
        $pdo->prepare("INSERT INTO initiatives (year, title, date_text, locations) VALUES (?,?,?,?)")
            ->execute([$_POST['year']?:null, trim($_POST['title']), trim($_POST['date_text']), trim($_POST['locations'])]);
        $_SESSION['flash'] = 'Initiative added.';
    }
    if ($act === 'update') {
        $pdo->prepare("UPDATE initiatives SET year=?, title=?, date_text=?, locations=? WHERE id=?")
            ->execute([$_POST['year']?:null, trim($_POST['title']), trim($_POST['date_text']), trim($_POST['locations']), (int)$_POST['id']]);
        $_SESSION['flash'] = 'Initiative updated.';
    }
    if ($act === 'delete') {
        $pdo->prepare("DELETE FROM initiatives WHERE id=?")->execute([(int)$_POST['id']]);
        $_SESSION['flash'] = 'Initiative deleted.';
    }
    header('Location: index.php?page=initiatives'); exit;
}

$items = $pdo->query("SELECT * FROM initiatives ORDER BY year ASC")->fetchAll();
$edit  = null;
if (!empty($_GET['edit'])) { $s=$pdo->prepare("SELECT * FROM initiatives WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }

$adminTitle = 'Initiatives'; $activePage = 'initiatives'; ob_start();
?>
<div class="admin-header"><h1>Initiatives</h1></div>

<div class="admin-panel">
  <h2><?= $edit ? 'Edit Initiative' : 'Add Initiative' ?></h2>
  <form method="POST" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <input type="hidden" name="_action" value="<?= $edit ? 'update' : 'create' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-group"><label>Year</label><input type="number" name="year" required value="<?= $edit['year']??'' ?>"></div>
    <div class="form-group"><label>Date Text</label><input type="text" name="date_text" placeholder="e.g. March 8" value="<?= htmlspecialchars($edit['date_text']??'',ENT_QUOTES,'UTF-8') ?>"></div>
    <div class="form-group" style="grid-column:1/-1"><label>Title</label><input type="text" name="title" required value="<?= htmlspecialchars($edit['title']??'',ENT_QUOTES,'UTF-8') ?>"></div>
    <div class="form-group" style="grid-column:1/-1"><label>Locations (semicolon-separated)</label><textarea name="locations"><?= htmlspecialchars($edit['locations']??'',ENT_QUOTES,'UTF-8') ?></textarea></div>
    <div style="grid-column:1/-1;display:flex;gap:10px">
      <button type="submit" class="btn btn-orange"><?= $edit ? 'Update' : 'Add Initiative' ?></button>
      <?php if ($edit): ?><a href="index.php?page=initiatives" class="btn btn-outline">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>#</th><th>Year</th><th>Title</th><th>Date</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($items as $r): ?>
        <tr>
          <td style="color:var(--muted)"><?= $r['id'] ?></td>
          <td><?= $r['year'] ?></td>
          <td><?= htmlspecialchars($r['title'],ENT_QUOTES,'UTF-8') ?></td>
          <td style="color:var(--muted)"><?= htmlspecialchars($r['date_text']??'',ENT_QUOTES,'UTF-8') ?></td>
          <td>
            <a href="index.php?page=initiatives&edit=<?= $r['id'] ?>" class="action-link action-link--edit">Edit</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete?')">
              <input type="hidden" name="_action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>">
              <button type="submit" class="action-link action-link--delete" style="background:none;cursor:pointer;font-family:inherit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php $adminContent = ob_get_clean(); require BASE_PATH . '/views/admin/_layout.php';
