<?php
/**
 * ADMIN: Hero Slides
 *
 * PHP CONCEPTS HERE:
 *  - Same Upload::handle() pattern as gallery and members
 *  - Reorder via SortableJS (same AJAX pattern as instruments/members)
 *  - Key-value settings: hero_slides table stores ordered slide images
 */

use App\Database\Connection;
use App\Helpers\Upload;

$pdo    = Connection::getInstance();
$imgDir = BASE_PATH . '/images/hero/';
$imgUrl = '../images/hero/';
$error  = '';

// ── Drag-and-drop reorder (AJAX) ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'reorder') {
    $ids = array_map('intval', $_POST['ids'] ?? []);
    foreach ($ids as $i => $id) {
        $pdo->prepare("UPDATE hero_slides SET display_order = ? WHERE id = ?")
            ->execute([$i + 1, $id]);
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';

    if ($act === 'upload') {
        try {
            $image = Upload::handle('image', $imgDir);
            if ($image) {
                $maxOrder = $pdo->query("SELECT MAX(display_order) FROM hero_slides")->fetchColumn() ?? 0;
                $pdo->prepare("INSERT INTO hero_slides (image, alt_text, display_order) VALUES (?,?,?)")
                    ->execute([$image, trim($_POST['alt_text']), (int)$maxOrder + 1]);
                $_SESSION['flash'] = 'Slide added.';
            }
        } catch (\RuntimeException $e) {
            $error = $e->getMessage();
        }
    }

    if ($act === 'delete') {
        $existing = $pdo->prepare("SELECT image FROM hero_slides WHERE id=?");
        $existing->execute([(int)$_POST['id']]);
        $img = $existing->fetchColumn();
        if ($img) {
            Upload::delete($img, $imgDir);
            $pdo->prepare("DELETE FROM hero_slides WHERE id=?")->execute([(int)$_POST['id']]);
            $_SESSION['flash'] = 'Slide deleted.';
        }
    }

    if (!$error) { header('Location: index.php?page=hero'); exit; }
}

$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC")->fetchAll();

$adminTitle = 'Hero Slides'; $activePage = 'hero'; ob_start();
?>

<div class="admin-header">
  <h1>Hero Slides</h1>
  <span style="font-size:.88rem;color:var(--muted)"><?= count($slides) ?> slide<?= count($slides) !== 1 ? 's' : '' ?></span>
</div>

<?php if ($error): ?>
  <div class="admin-flash admin-flash--err"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<!-- Upload form -->
<div class="admin-panel">
  <h2>Add Slide</h2>
  <form method="POST" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <input type="hidden" name="_action" value="upload">
    <div class="form-group">
      <label>Image <span style="color:var(--muted);font-weight:400;text-transform:none;font-size:.8rem">JPG, PNG, WEBP · max 5 MB · recommended 1920×800px</span></label>
      <input type="file" name="image" accept="image/*" required style="padding:6px">
    </div>
    <div class="form-group">
      <label>Alt Text <span style="color:var(--muted);font-weight:400;text-transform:none;font-size:.8rem">describes the slide for SEO &amp; accessibility</span></label>
      <input type="text" name="alt_text" placeholder="e.g. Kutumba performing at Patan Durbar Square">
    </div>
    <div style="grid-column:1/-1">
      <button type="submit" class="btn btn-orange">Upload Slide</button>
    </div>
  </form>
</div>

<?php if (empty($slides)): ?>
  <div class="admin-panel">
    <p style="color:var(--muted)">No slides yet. Upload one above — it will appear in the hero slider on the homepage.</p>
  </div>
<?php else: ?>

  <p style="font-size:.84rem;color:var(--muted);margin-bottom:12px">
    Drag rows to reorder. The first slide is shown first on the homepage.
  </p>

  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width:32px"></th>
          <th style="width:140px">Preview</th>
          <th>Alt Text</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="sortable-hero">
        <?php foreach ($slides as $s): ?>
          <tr data-id="<?= $s['id'] ?>">
            <td><span class="drag-handle" title="Drag to reorder">⠿</span></td>
            <td>
              <img src="<?= $imgUrl . htmlspecialchars($s['image'], ENT_QUOTES, 'UTF-8') ?>"
                   style="width:120px;height:60px;object-fit:cover;border-radius:6px;border:1px solid var(--border)">
            </td>
            <td style="color:var(--muted);font-size:.84rem">
              <?= htmlspecialchars($s['alt_text'] ?: '—', ENT_QUOTES, 'UTF-8') ?>
            </td>
            <td>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete this slide?')">
                <input type="hidden" name="_action" value="delete">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button type="submit" class="action-link action-link--delete" style="background:none;cursor:pointer;font-family:inherit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php endif; ?>

<?php $adminContent = ob_get_clean(); require BASE_PATH . '/views/admin/_layout.php'; ?>
<script>initSortable('sortable-hero', 'hero');</script>
