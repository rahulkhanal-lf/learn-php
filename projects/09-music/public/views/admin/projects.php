<?php
use App\Database\Connection;

$pdo = Connection::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';
    if ($act === 'create') {
        $pdo->prepare("INSERT INTO projects (title, description, year, display_order) VALUES (?,?,?,?)")
            ->execute([trim($_POST['title']), trim($_POST['description']), $_POST['year']?:null, (int)$_POST['display_order']]);
        $_SESSION['flash'] = 'Project added.';
    }
    if ($act === 'update') {
        $pdo->prepare("UPDATE projects SET title=?, description=?, year=?, display_order=? WHERE id=?")
            ->execute([trim($_POST['title']), trim($_POST['description']), $_POST['year']?:null, (int)$_POST['display_order'], (int)$_POST['id']]);
        $_SESSION['flash'] = 'Project updated.';
    }
    if ($act === 'delete') {
        $pdo->prepare("DELETE FROM projects WHERE id=?")->execute([(int)$_POST['id']]);
        $_SESSION['flash'] = 'Project deleted.';
    }
    header('Location: index.php?page=projects'); exit;
}

$items = $pdo->query("SELECT * FROM projects ORDER BY display_order ASC")->fetchAll();
$edit  = null;
if (!empty($_GET['edit'])) { $s=$pdo->prepare("SELECT * FROM projects WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }

$adminTitle = 'Projects'; $activePage = 'projects'; ob_start();
?>
<div class="admin-header"><h1>Projects</h1></div>

<div class="admin-panel">
  <h2><?= $edit ? 'Edit Project' : 'Add Project' ?></h2>
  <form method="POST" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <input type="hidden" name="_action" value="<?= $edit ? 'update' : 'create' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-group" style="grid-column:1/-1"><label>Title</label><input type="text" name="title" required value="<?= htmlspecialchars($edit['title']??'',ENT_QUOTES,'UTF-8') ?>"></div>
    <div class="form-group"><label>Year</label><input type="number" name="year" value="<?= $edit['year']??'' ?>"></div>
    <div class="form-group"><label>Display Order</label><input type="number" name="display_order" value="<?= (int)($edit['display_order']??0) ?>"></div>
    <div class="form-group" style="grid-column:1/-1"><label>Description</label><textarea name="description"><?= htmlspecialchars($edit['description']??'',ENT_QUOTES,'UTF-8') ?></textarea></div>
    <div style="grid-column:1/-1;display:flex;gap:10px">
      <button type="submit" class="btn btn-orange"><?= $edit ? 'Update' : 'Add Project' ?></button>
      <?php if ($edit): ?><a href="index.php?page=projects" class="btn btn-outline">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>#</th><th>Title</th><th>Year</th><th>Order</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($items as $r): ?>
        <tr>
          <td style="color:var(--muted)"><?= $r['id'] ?></td>
          <td><?= htmlspecialchars($r['title'],ENT_QUOTES,'UTF-8') ?></td>
          <td><?= $r['year'] ?></td>
          <td><?= $r['display_order'] ?></td>
          <td>
            <a href="index.php?page=projects&edit=<?= $r['id'] ?>" class="action-link action-link--edit">Edit</a>
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
