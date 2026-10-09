<?php
/**
 * PAGE: Songs — full CRUD with search
 */

use App\Controllers\SongController;

// Handle POST/delete before any output
$controller = new SongController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->store();   // redirects on success
    exit;
}
if (($_GET['action'] ?? '') === 'delete') {
    $controller->delete();  // redirects
    exit;
}

use App\Models\Song;
use App\Models\Artist;

$search  = trim($_GET['q'] ?? '');
$songs   = $search ? (new Song())->search($search) : (new Song())->findAll();
$artists = (new Artist())->allForSelect();
$errors  = [];
$msg     = $_GET['msg'] ?? '';

$pageTitle   = 'Songs';
$currentPage = 'songs';

ob_start();
?>

<div class="container">

  <div class="page-header">
    <h1>Songs</h1>
    <p>Manage your music library — add, search, and remove songs.</p>
  </div>

  <?php if ($msg): ?>
    <div class="alert alert-success">
      <?= $msg === 'created' ? '✅ Song added successfully.' : '🗑️ Song deleted.' ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
      <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
    </div>
  <?php endif; ?>

  <!-- ── Add Song Form ── -->
  <div class="card" style="margin-bottom:var(--space-xl)">
    <h2 style="font-size:1rem;font-weight:700;margin-bottom:var(--space-lg)">Add Song</h2>
    <form method="POST" action="index.php?page=songs">
      <div class="form-grid">
        <div class="form-group">
          <label for="title">Title *</label>
          <input id="title" type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label for="artist_id">Artist *</label>
          <select id="artist_id" name="artist_id" required>
            <option value="">— select artist —</option>
            <?php foreach ($artists as $a): ?>
              <option value="<?= $a['id'] ?>" <?= (($_POST['artist_id'] ?? '') == $a['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($a['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="album">Album</label>
          <input id="album" type="text" name="album" value="<?= htmlspecialchars($_POST['album'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="genre">Genre</label>
          <input id="genre" type="text" name="genre" value="<?= htmlspecialchars($_POST['genre'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="duration_seconds">Duration (seconds)</label>
          <input id="duration_seconds" type="number" name="duration_seconds" value="<?= htmlspecialchars($_POST['duration_seconds'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="release_year">Release Year</label>
          <input id="release_year" type="number" name="release_year" min="1900" max="<?= date('Y') ?>"
                 value="<?= htmlspecialchars($_POST['release_year'] ?? '') ?>">
        </div>
      </div>
      <div style="margin-top:var(--space-md)">
        <button type="submit" class="btn btn-primary">Add Song</button>
      </div>
    </form>
  </div>

  <!-- ── Search + Table ── -->
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-md);flex-wrap:wrap;gap:var(--space-sm)">
      <h2 style="font-size:1rem;font-weight:700">All Songs (<?= count($songs) ?>)</h2>
      <form method="GET" style="display:flex;gap:var(--space-sm)">
        <input type="hidden" name="page" value="songs">
        <input type="search" name="q" placeholder="Search title, artist, genre…"
               value="<?= htmlspecialchars($search) ?>" style="width:260px">
        <button type="submit" class="btn btn-outline btn-sm">Search</button>
        <?php if ($search): ?>
          <a href="index.php?page=songs" class="btn btn-sm" style="background:#f3f4f6;color:#374151">Clear</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="table-wrap">
      <?php if (empty($songs)): ?>
        <p style="text-align:center;padding:40px;color:var(--color-muted)">No songs found.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>#</th><th>Title</th><th>Artist</th>
              <th>Album</th><th>Genre</th><th>Duration</th><th>Year</th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($songs as $i => $song): ?>
            <tr>
              <td style="color:var(--color-muted)"><?= $i + 1 ?></td>
              <td><strong><?= htmlspecialchars($song['title']) ?></strong></td>
              <td><?= htmlspecialchars($song['artist_name'] ?? '—') ?></td>
              <td><?= htmlspecialchars($song['album'] ?? '—') ?></td>
              <td>
                <?php if ($song['genre']): ?>
                  <span class="badge"><?= htmlspecialchars($song['genre']) ?></span>
                <?php else: ?>—<?php endif; ?>
              </td>
              <td>
                <?php if ($song['duration_seconds']): ?>
                  <?= sprintf('%d:%02d', intdiv($song['duration_seconds'], 60), $song['duration_seconds'] % 60) ?>
                <?php else: ?>—<?php endif; ?>
              </td>
              <td><?= $song['release_year'] ?? '—' ?></td>
              <td>
                <a href="index.php?page=songs&action=delete&id=<?= $song['id'] ?>"
                   class="btn btn-danger"
                   onclick="return confirm('Delete \'<?= htmlspecialchars(addslashes($song['title'])) ?>\'?')">
                  Delete
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/views/layout.php';
