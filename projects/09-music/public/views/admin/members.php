<?php
/**
 * ADMIN: Members CRUD
 *
 * PHP CONCEPTS HERE:
 *  - enctype="multipart/form-data" — required for file uploads
 *  - $_FILES superglobal — holds uploaded file info
 *  - Upload::handle() helper — validates + moves the file
 *  - Keeping the old photo when no new one is uploaded (null coalescing)
 */

use App\Database\Connection;
use App\Helpers\Upload;

$pdo     = Connection::getInstance();
$imgDir  = BASE_PATH . '/images/members/';
$imgUrl  = '../images/members/';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';

    try {
        if ($act === 'create') {
            $photo = Upload::handle('photo', $imgDir) ?? 'placeholder.jpg';
            $pdo->prepare("INSERT INTO members (name, instrument, bio, photo, display_order) VALUES (?,?,?,?,?)")
                ->execute([trim($_POST['name']), trim($_POST['instrument']), trim($_POST['bio']), $photo, (int)$_POST['display_order']]);
            $_SESSION['flash'] = 'Member added.';
        }

        if ($act === 'update') {
            $existing = $pdo->prepare("SELECT photo FROM members WHERE id=?");
            $existing->execute([(int)$_POST['id']]);
            $oldPhoto = $existing->fetchColumn();

            $newPhoto = Upload::handle('photo', $imgDir);
            if ($newPhoto && $oldPhoto !== 'placeholder.jpg') {
                Upload::delete($oldPhoto, $imgDir); // remove old file
            }
            $photo = $newPhoto ?? $oldPhoto;

            $pdo->prepare("UPDATE members SET name=?, instrument=?, bio=?, photo=?, display_order=? WHERE id=?")
                ->execute([trim($_POST['name']), trim($_POST['instrument']), trim($_POST['bio']), $photo, (int)$_POST['display_order'], (int)$_POST['id']]);
            $_SESSION['flash'] = 'Member updated.';
        }

        if ($act === 'delete') {
            $existing = $pdo->prepare("SELECT photo FROM members WHERE id=?");
            $existing->execute([(int)$_POST['id']]);
            Upload::delete($existing->fetchColumn(), $imgDir);
            $pdo->prepare("DELETE FROM members WHERE id=?")->execute([(int)$_POST['id']]);
            $_SESSION['flash'] = 'Member deleted.';
        }

        header('Location: index.php?page=members'); exit;

    } catch (\RuntimeException $e) {
        $error = $e->getMessage();
    }
}

$members    = $pdo->query("SELECT * FROM members ORDER BY display_order ASC")->fetchAll();
$editMember = null;
if (!empty($_GET['edit'])) {
    $s = $pdo->prepare("SELECT * FROM members WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $editMember = $s->fetch();
}

$adminTitle = 'Members';
$activePage = 'members';
ob_start();
?>

<div class="admin-header"><h1>Members</h1></div>

<?php if ($error): ?>
  <div class="admin-flash admin-flash--err"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<!-- Add / Edit form -->
<div class="admin-panel">
  <h2><?= $editMember ? 'Edit Member' : 'Add Member' ?></h2>

  <!-- enctype="multipart/form-data" is REQUIRED for file uploads -->
  <form method="POST" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <input type="hidden" name="_action" value="<?= $editMember ? 'update' : 'create' ?>">
    <?php if ($editMember): ?>
      <input type="hidden" name="id" value="<?= $editMember['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
      <label>Name</label>
      <input type="text" name="name" required value="<?= htmlspecialchars($editMember['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="form-group">
      <label>Instrument</label>
      <input type="text" name="instrument" value="<?= htmlspecialchars($editMember['instrument'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="form-group" style="grid-column:1/-1">
      <label>Bio</label>
      <textarea name="bio"><?= htmlspecialchars($editMember['bio'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <!-- Photo upload with current image preview -->
    <div class="form-group">
      <label>Photo <?= $editMember ? '(leave empty to keep current)' : '' ?></label>
      <?php if (!empty($editMember['photo']) && $editMember['photo'] !== 'placeholder.jpg'): ?>
        <img src="<?= $imgUrl . htmlspecialchars($editMember['photo'], ENT_QUOTES, 'UTF-8') ?>"
             style="width:80px;height:80px;object-fit:cover;border-radius:6px;margin-bottom:8px;display:block;border:1px solid var(--border)">
      <?php endif; ?>
      <input type="file" name="photo" accept="image/*" style="padding:6px">
      <span style="font-size:.75rem;color:var(--muted)">JPG, PNG, WEBP · max 5 MB</span>
    </div>

    <div class="form-group">
      <label>Display Order</label>
      <input type="number" name="display_order" value="<?= (int)($editMember['display_order'] ?? 0) ?>">
    </div>

    <div style="grid-column:1/-1;display:flex;gap:10px">
      <button type="submit" class="btn btn-orange"><?= $editMember ? 'Update Member' : 'Add Member' ?></button>
      <?php if ($editMember): ?>
        <a href="index.php?page=members" class="btn btn-outline">Cancel</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<!-- Table -->
<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th>Photo</th><th>Name</th><th>Instrument</th><th>Order</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($members as $m): ?>
        <tr>
          <td>
            <img src="<?= $imgUrl . htmlspecialchars($m['photo'] ?? 'placeholder.jpg', ENT_QUOTES, 'UTF-8') ?>"
                 style="width:44px;height:44px;object-fit:cover;border-radius:6px;border:1px solid var(--border)">
          </td>
          <td><?= htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8') ?></td>
          <td style="color:var(--muted)"><?= htmlspecialchars($m['instrument'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= $m['display_order'] ?></td>
          <td>
            <a href="index.php?page=members&edit=<?= $m['id'] ?>" class="action-link action-link--edit">Edit</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('Delete this member?')">
              <input type="hidden" name="_action" value="delete">
              <input type="hidden" name="id" value="<?= $m['id'] ?>">
              <button type="submit" class="action-link action-link--delete" style="background:none;cursor:pointer;font-family:inherit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php
$adminContent = ob_get_clean();
require BASE_PATH . '/views/admin/_layout.php';
