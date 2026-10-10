<?php
use App\Database\Connection;

$pdo = Connection::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'reorder') {
    $ids = array_map('intval', $_POST['ids'] ?? []);
    foreach ($ids as $i => $id) {
        $pdo->prepare("UPDATE events SET display_order = ? WHERE id = ?")->execute([$i + 1, $id]);
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';
    if ($act === 'create') {
        $pdo->prepare("INSERT INTO events (title, year, date_text, location, image, is_upcoming, display_order) VALUES (?,?,?,?,?,?,?)")
            ->execute([trim($_POST['title']), $_POST['year']?:null, trim($_POST['date_text']), trim($_POST['location']), trim($_POST['image']), isset($_POST['is_upcoming'])?1:0, (int)$_POST['display_order']]);
        $_SESSION['flash'] = 'Event added.';
    }
    if ($act === 'update') {
        $pdo->prepare("UPDATE events SET title=?, year=?, date_text=?, location=?, image=?, is_upcoming=?, display_order=? WHERE id=?")
            ->execute([trim($_POST['title']), $_POST['year']?:null, trim($_POST['date_text']), trim($_POST['location']), trim($_POST['image']), isset($_POST['is_upcoming'])?1:0, (int)$_POST['display_order'], (int)$_POST['id']]);
        $_SESSION['flash'] = 'Event updated.';
    }
    if ($act === 'delete') {
        $pdo->prepare("DELETE FROM events WHERE id=?")->execute([(int)$_POST['id']]);
        $_SESSION['flash'] = 'Event deleted.';
    }
    header('Location: index.php?page=events'); exit;
}

$items = $pdo->query("SELECT * FROM events ORDER BY year DESC, display_order ASC")->fetchAll();
$edit  = null;
if (!empty($_GET['edit'])) { $s=$pdo->prepare("SELECT * FROM events WHERE id=?"); $s->execute([(int)$_GET['edit']]); $edit=$s->fetch(); }

$adminTitle = 'Events'; $activePage = 'events'; ob_start();
?>
<div class="admin-header"><h1>Events</h1></div>

<div class="admin-panel">
  <h2><?= $edit ? 'Edit Event' : 'Add Event' ?></h2>
  <form method="POST" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <input type="hidden" name="_action" value="<?= $edit ? 'update' : 'create' ?>">
    <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
    <div class="form-group" style="grid-column:1/-1"><label>Title</label><input type="text" name="title" required value="<?= htmlspecialchars($edit['title']??'',ENT_QUOTES,'UTF-8') ?>"></div>
    <div class="form-group"><label>Year</label><input type="number" name="year" min="2000" max="2100" value="<?= $edit['year']??'' ?>"></div>
    <div class="form-group"><label>Date Text</label><input type="text" name="date_text" placeholder="e.g. March 8" value="<?= htmlspecialchars($edit['date_text']??'',ENT_QUOTES,'UTF-8') ?>"></div>
    <div class="form-group"><label>Location</label><input type="text" name="location" value="<?= htmlspecialchars($edit['location']??'',ENT_QUOTES,'UTF-8') ?>"></div>
    <div class="form-group"><label>Image filename</label><input type="text" name="image" placeholder="event.jpg" value="<?= htmlspecialchars($edit['image']??'',ENT_QUOTES,'UTF-8') ?>"></div>
    <input type="hidden" name="display_order" value="<?= (int)($edit['display_order']??0) ?>">
    <div class="form-group" style="align-items:flex-start;padding-top:24px">
      <label style="display:flex;align-items:center;gap:8px;cursor:pointer;text-transform:none;font-size:.88rem">
        <input type="checkbox" name="is_upcoming" <?= !empty($edit['is_upcoming'])?'checked':'' ?>> Mark as Upcoming
      </label>
    </div>
    <div style="grid-column:1/-1;display:flex;gap:10px">
      <button type="submit" class="btn btn-orange"><?= $edit ? 'Update' : 'Add Event' ?></button>
      <?php if ($edit): ?><a href="index.php?page=events" class="btn btn-outline">Cancel</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead><tr><th style="width:32px"></th><th>Title</th><th>Year</th><th>Location</th><th>Upcoming</th><th>Actions</th></tr></thead>
    <tbody id="sortable-events">
      <?php foreach ($items as $r): ?>
        <tr data-id="<?= $r['id'] ?>">
          <td><span class="drag-handle" title="Drag to reorder">⠿</span></td>
          <td><?= htmlspecialchars($r['title'],ENT_QUOTES,'UTF-8') ?></td>
          <td><?= $r['year'] ?></td>
          <td><?= htmlspecialchars($r['location']??'',ENT_QUOTES,'UTF-8') ?></td>
          <td><?= $r['is_upcoming'] ? '<span style="color:var(--orange);font-weight:700">Yes</span>' : 'No' ?></td>
          <td>
            <a href="index.php?page=events&edit=<?= $r['id'] ?>" class="action-link action-link--edit">Edit</a>
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
<script>initSortable('sortable-events', 'events');</script>
