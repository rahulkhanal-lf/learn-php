<?php
use App\Database\Connection;
use App\Helpers\Upload;

$pdo    = Connection::getInstance();
$imgDir = BASE_PATH . '/images/instruments/';
$imgUrl = '../images/instruments/';
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'reorder') {
    $ids = array_map('intval', $_POST['ids'] ?? []);
    foreach ($ids as $i => $id) {
        $pdo->prepare("UPDATE instruments SET display_order = ? WHERE id = ?")->execute([$i + 1, $id]);
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';
    try {
        if ($act === 'create') {
            $photo = Upload::handle('photo', $imgDir) ?? 'placeholder.jpg';
            $pdo->prepare("INSERT INTO instruments (name, description, photo, display_order, alt_text) VALUES (?,?,?,?,?)")
                ->execute([trim($_POST['name']), trim($_POST['description']), $photo, (int)$_POST['display_order'], trim($_POST['alt_text'])]);
            $_SESSION['flash'] = 'Instrument added.';
        }
        if ($act === 'update') {
            $existing = $pdo->prepare("SELECT photo FROM instruments WHERE id=?");
            $existing->execute([(int)$_POST['id']]);
            $oldPhoto = $existing->fetchColumn();
            $newPhoto = Upload::handle('photo', $imgDir);
            if ($newPhoto && $oldPhoto !== 'placeholder.jpg') Upload::delete($oldPhoto, $imgDir);
            $photo = $newPhoto ?? $oldPhoto;
            $pdo->prepare("UPDATE instruments SET name=?, description=?, photo=?, display_order=?, alt_text=? WHERE id=?")
                ->execute([trim($_POST['name']), trim($_POST['description']), $photo, (int)$_POST['display_order'], trim($_POST['alt_text']), (int)$_POST['id']]);
            $_SESSION['flash'] = 'Instrument updated.';
        }
        if ($act === 'delete') {
            $existing = $pdo->prepare("SELECT photo FROM instruments WHERE id=?");
            $existing->execute([(int)$_POST['id']]);
            Upload::delete($existing->fetchColumn(), $imgDir);
            $pdo->prepare("DELETE FROM instruments WHERE id=?")->execute([(int)$_POST['id']]);
            $_SESSION['flash'] = 'Instrument deleted.';
        }
        header('Location: index.php?page=instruments'); exit;
    } catch (\RuntimeException $e) { $error = $e->getMessage(); }
}

$items = $pdo->query("SELECT * FROM instruments ORDER BY display_order ASC")->fetchAll();
$edit  = null;
if (!empty($_GET['edit'])) { $s=$pdo->prepare("SELECT * FROM instruments WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }

$adminTitle = 'Instruments'; $activePage = 'instruments'; ob_start();
?>
<div class="admin-header"><h1>Instruments</h1></div>

<?php if ($error): ?><div class="admin-flash admin-flash--err"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="admin-panel">
  <h2><?= $edit ? 'Edit Instrument' : 'Add Instrument' ?></h2>
  <form method="POST" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <input type="hidden" name="_action" value="<?= $edit ? 'update' : 'create' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-group"><label>Name</label><input type="text" name="name" required value="<?= htmlspecialchars($edit['name']??'',ENT_QUOTES,'UTF-8') ?>"></div>
    <div class="form-group">
      <label>Photo <?= $edit ? '(leave empty to keep current)' : '' ?></label>
      <?php if (!empty($edit['photo']) && $edit['photo'] !== 'placeholder.jpg'): ?>
        <img src="<?= $imgUrl . htmlspecialchars($edit['photo'],ENT_QUOTES,'UTF-8') ?>"
             style="width:80px;height:80px;object-fit:contain;border-radius:6px;margin-bottom:8px;display:block;border:1px solid var(--border)">
      <?php endif; ?>
      <input type="file" name="photo" accept="image/*" style="padding:6px">
      <span style="font-size:.75rem;color:var(--muted)">JPG, PNG, WEBP · max 5 MB</span>
    </div>
    <div class="form-group" style="grid-column:1/-1"><label>Description</label><textarea name="description"><?= htmlspecialchars($edit['description']??'',ENT_QUOTES,'UTF-8') ?></textarea></div>
    <div class="form-group" style="grid-column:1/-1">
      <label>Image Alt Text <span style="color:var(--muted);font-weight:400;text-transform:none;font-size:.8rem"> — e.g. "Madal drum, a traditional Nepali percussion instrument"</span></label>
      <input type="text" name="alt_text" placeholder="Describe the image in a few words" value="<?= htmlspecialchars($edit['alt_text']??'',ENT_QUOTES,'UTF-8') ?>">
    </div>
    <input type="hidden" name="display_order" value="<?= (int)($edit['display_order']??0) ?>">
    <div style="grid-column:1/-1;display:flex;gap:10px">
      <button type="submit" class="btn btn-orange"><?= $edit ? 'Update' : 'Add Instrument' ?></button>
      <?php if ($edit): ?><a href="index.php?page=instruments" class="btn btn-outline">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th style="width:32px"></th><th>Photo</th><th>Name</th><th>Actions</th></tr></thead>
    <tbody id="sortable-instruments">
      <?php foreach ($items as $r): ?>
        <tr data-id="<?= $r['id'] ?>">
          <td><span class="drag-handle" title="Drag to reorder">⠿</span></td>
          <td><img src="<?= $imgUrl . htmlspecialchars($r['photo']??'placeholder.jpg',ENT_QUOTES,'UTF-8') ?>"
                   style="width:44px;height:44px;object-fit:contain;border-radius:6px;border:1px solid var(--border)"></td>
          <td><?= htmlspecialchars($r['name'],ENT_QUOTES,'UTF-8') ?></td>
          <td>
            <a href="index.php?page=instruments&edit=<?= $r['id'] ?>" class="action-link action-link--edit">Edit</a>
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

<?php $adminContent = ob_get_clean(); require BASE_PATH . '/views/admin/_layout.php'; ?>
<script>initSortable('sortable-instruments', 'instruments');</script>
