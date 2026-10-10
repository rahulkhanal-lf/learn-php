<?php
use App\Database\Connection;
use App\Helpers\Upload;

$pdo    = Connection::getInstance();
$imgDir = BASE_PATH . '/images/albums/';
$imgUrl = '../images/albums/';
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';
    try {
        if ($act === 'create') {
            $cover = Upload::handle('cover_image', $imgDir) ?? 'placeholder.jpg';
            $pdo->prepare("INSERT INTO albums (title, year, cover_image, description, alt_text) VALUES (?,?,?,?,?)")
                ->execute([trim($_POST['title']), $_POST['year']?:null, $cover, trim($_POST['description']), trim($_POST['alt_text'])]);
            $_SESSION['flash'] = 'Album added.';
        }
        if ($act === 'update') {
            $existing = $pdo->prepare("SELECT cover_image FROM albums WHERE id=?");
            $existing->execute([(int)$_POST['id']]);
            $oldCover = $existing->fetchColumn();
            $newCover = Upload::handle('cover_image', $imgDir);
            if ($newCover && $oldCover !== 'placeholder.jpg') Upload::delete($oldCover, $imgDir);
            $cover = $newCover ?? $oldCover;
            $pdo->prepare("UPDATE albums SET title=?, year=?, cover_image=?, description=?, alt_text=? WHERE id=?")
                ->execute([trim($_POST['title']), $_POST['year']?:null, $cover, trim($_POST['description']), trim($_POST['alt_text']), (int)$_POST['id']]);
            $_SESSION['flash'] = 'Album updated.';
        }
        if ($act === 'delete') {
            $existing = $pdo->prepare("SELECT cover_image FROM albums WHERE id=?");
            $existing->execute([(int)$_POST['id']]);
            Upload::delete($existing->fetchColumn(), $imgDir);
            $pdo->prepare("DELETE FROM albums WHERE id=?")->execute([(int)$_POST['id']]);
            $_SESSION['flash'] = 'Album deleted.';
        }
        header('Location: index.php?page=albums'); exit;
    } catch (\RuntimeException $e) { $error = $e->getMessage(); }
}

$items = $pdo->query("SELECT * FROM albums ORDER BY year DESC")->fetchAll();
$edit  = null;
if (!empty($_GET['edit'])) { $s=$pdo->prepare("SELECT * FROM albums WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }

$adminTitle = 'Albums'; $activePage = 'albums'; ob_start();
?>
<div class="admin-header"><h1>Albums</h1></div>

<?php if ($error): ?><div class="admin-flash admin-flash--err"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="admin-panel">
  <h2><?= $edit ? 'Edit Album' : 'Add Album' ?></h2>
  <form method="POST" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <input type="hidden" name="_action" value="<?= $edit ? 'update' : 'create' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-group"><label>Title</label><input type="text" name="title" required value="<?= htmlspecialchars($edit['title']??'',ENT_QUOTES,'UTF-8') ?>"></div>
    <div class="form-group"><label>Year</label><input type="number" name="year" value="<?= $edit['year']??'' ?>"></div>
    <div class="form-group">
      <label>Cover Image <?= $edit ? '(leave empty to keep current)' : '' ?></label>
      <?php if (!empty($edit['cover_image']) && $edit['cover_image'] !== 'placeholder.jpg'): ?>
        <img src="<?= $imgUrl . htmlspecialchars($edit['cover_image'],ENT_QUOTES,'UTF-8') ?>"
             style="width:80px;height:80px;object-fit:cover;border-radius:6px;margin-bottom:8px;display:block;border:1px solid var(--border)">
      <?php endif; ?>
      <input type="file" name="cover_image" accept="image/*" style="padding:6px">
      <span style="font-size:.75rem;color:var(--muted)">JPG, PNG, WEBP · max 5 MB</span>
    </div>
    <div class="form-group" style="grid-column:1/-1"><label>Description</label><textarea name="description"><?= htmlspecialchars($edit['description']??'',ENT_QUOTES,'UTF-8') ?></textarea></div>
    <div class="form-group" style="grid-column:1/-1">
      <label>Cover Image Alt Text <span style="color:var(--muted);font-weight:400;text-transform:none;font-size:.8rem"> — e.g. "Kutumba self-titled debut album cover, 2008"</span></label>
      <input type="text" name="alt_text" placeholder="Describe the album cover" value="<?= htmlspecialchars($edit['alt_text']??'',ENT_QUOTES,'UTF-8') ?>">
    </div>
    <div style="grid-column:1/-1;display:flex;gap:10px">
      <button type="submit" class="btn btn-orange"><?= $edit ? 'Update' : 'Add Album' ?></button>
      <?php if ($edit): ?><a href="index.php?page=albums" class="btn btn-outline">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>Cover</th><th>Title</th><th>Year</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($items as $r): ?>
        <tr>
          <td><img src="<?= $imgUrl . htmlspecialchars($r['cover_image']??'placeholder.jpg',ENT_QUOTES,'UTF-8') ?>"
                   style="width:44px;height:44px;object-fit:cover;border-radius:6px;border:1px solid var(--border)"></td>
          <td><?= htmlspecialchars($r['title'],ENT_QUOTES,'UTF-8') ?></td>
          <td><?= $r['year'] ?></td>
          <td>
            <a href="index.php?page=albums&edit=<?= $r['id'] ?>" class="action-link action-link--edit">Edit</a>
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
