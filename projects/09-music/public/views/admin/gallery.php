<?php
/**
 * ADMIN: Gallery Upload
 *
 * PHP CONCEPTS HERE:
 *  - glob() to scan filesystem for existing images
 *  - Multiple file upload via $_FILES with input[multiple]
 *  - Loop over $_FILES to handle batch uploads
 *  - unlink() to delete a file from the filesystem
 */

use App\Helpers\Upload;

$imgDir = BASE_PATH . '/images/gallery/';
$imgUrl = '../images/gallery/';
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';

    if ($act === 'upload') {
        $uploaded = 0;
        // Handle multiple files — $_FILES['photos'] contains arrays for each file
        $count = count($_FILES['photos']['name']);
        for ($i = 0; $i < $count; $i++) {
            // Reconstruct a single-file $_FILES-compatible array for the Upload helper
            $_FILES['_single'] = [
                'name'     => $_FILES['photos']['name'][$i],
                'type'     => $_FILES['photos']['type'][$i],
                'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                'error'    => $_FILES['photos']['error'][$i],
                'size'     => $_FILES['photos']['size'][$i],
            ];
            try {
                Upload::handle('_single', $imgDir);
                $uploaded++;
            } catch (\RuntimeException $e) {
                $error .= $e->getMessage() . ' ';
            }
        }
        if ($uploaded) $_SESSION['flash'] = "$uploaded photo(s) uploaded.";
    }

    if ($act === 'delete') {
        $file = basename($_POST['file'] ?? ''); // basename() prevents directory traversal
        if ($file) {
            Upload::delete($file, $imgDir);
            $_SESSION['flash'] = 'Photo deleted.';
        }
    }

    if (!$error) { header('Location: index.php?page=gallery'); exit; }
}

// Scan filesystem for gallery images
$photos = glob($imgDir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
$photos = array_map('basename', $photos ?: []);

$adminTitle = 'Gallery'; $activePage = 'gallery'; ob_start();
?>
<div class="admin-header">
  <h1>Gallery</h1>
  <span style="font-size:.88rem;color:var(--muted)"><?= count($photos) ?> photos</span>
</div>

<?php if ($error): ?><div class="admin-flash admin-flash--err"><?= htmlspecialchars(trim($error), ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<!-- Upload form -->
<div class="admin-panel">
  <h2>Upload Photos</h2>
  <form method="POST" enctype="multipart/form-data" style="display:flex;gap:16px;align-items:flex-end;flex-wrap:wrap">
    <input type="hidden" name="_action" value="upload">
    <div class="form-group" style="margin:0;flex:1;min-width:240px">
      <label>Select images (you can pick multiple)</label>
      <input type="file" name="photos[]" accept="image/*" multiple required style="padding:8px">
      <span style="font-size:.75rem;color:var(--muted)">JPG, PNG, WEBP · max 5 MB each</span>
    </div>
    <button type="submit" class="btn btn-orange">Upload</button>
  </form>
</div>

<!-- Photo grid -->
<?php if (empty($photos)): ?>
  <div class="admin-panel"><p style="color:var(--muted)">No photos yet. Upload some above.</p></div>
<?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px">
    <?php foreach ($photos as $photo): ?>
      <div style="position:relative;border:1px solid var(--border);border-radius:8px;overflow:hidden;background:#fff">
        <img src="<?= $imgUrl . htmlspecialchars($photo, ENT_QUOTES, 'UTF-8') ?>"
             style="width:100%;aspect-ratio:4/3;object-fit:cover;display:block">
        <div style="padding:8px;display:flex;align-items:center;justify-content:space-between">
          <span style="font-size:.7rem;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100px"><?= htmlspecialchars($photo, ENT_QUOTES, 'UTF-8') ?></span>
          <form method="POST" onsubmit="return confirm('Delete this photo?')" style="margin:0">
            <input type="hidden" name="_action" value="delete">
            <input type="hidden" name="file" value="<?= htmlspecialchars($photo, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:.8rem;font-weight:700;padding:2px 6px" title="Delete">✕</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php $adminContent = ob_get_clean(); require BASE_PATH . '/views/admin/_layout.php';
