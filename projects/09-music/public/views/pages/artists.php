<?php
/**
 * PAGE: Artists
 */

use App\Models\Artist;

$artists     = (new Artist())->findAll();
$pageTitle   = 'Artists';
$currentPage = 'artists';

ob_start();
?>

<div class="container">

  <div class="page-header">
    <h1>Artists</h1>
    <p><?= count($artists) ?> artists in the library.</p>
  </div>

  <div class="card-grid">
    <?php foreach ($artists as $artist): ?>
      <div class="card artist-card">

        <!-- Avatar: first letter of name (no image upload needed) -->
        <div class="artist-card__avatar">
          <?= htmlspecialchars(mb_strtoupper(mb_substr($artist['name'], 0, 1))) ?>
        </div>

        <h3 style="font-size:1.05rem;margin-bottom:4px">
          <?= htmlspecialchars($artist['name']) ?>
        </h3>

        <?php if ($artist['genre']): ?>
          <span class="badge" style="margin-bottom:8px"><?= htmlspecialchars($artist['genre']) ?></span>
        <?php endif; ?>

        <?php if ($artist['country']): ?>
          <p style="font-size:.85rem;color:var(--color-muted)">
            📍 <?= htmlspecialchars($artist['country']) ?>
          </p>
        <?php endif; ?>

      </div>
    <?php endforeach; ?>
  </div>

</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/views/layout.php';
