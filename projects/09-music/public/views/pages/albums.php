<?php
/**
 * PAGE: Albums
 *
 * PHP CONCEPTS HERE:
 *  - Simple model->findAll() with ORDER BY
 *  - Rendering a responsive CSS grid from DB data
 */

use App\Models\Album;

$albums      = (new Album())->findAll();
$pageTitle   = 'Albums';
$currentPage = 'albums';

ob_start();
?>

<div class="page-banner">
  <div class="container">
    <h1>Released Albums</h1>
    <p style="text-align:center;color:var(--muted);margin-top:8px;font-size:.9rem">
      These are the released albums of KUTUMBA
    </p>
  </div>
</div>

<section class="section">
  <div class="container">

    <?php if (empty($albums)): ?>
      <p style="text-align:center;color:var(--muted)">No albums yet.</p>
    <?php else: ?>

      <!--
        CONCEPT: CSS Grid via class + PHP data rendering.
        albums-grid is defined in app.css:
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        PHP just outputs the HTML — layout is pure CSS.
      -->
      <div class="albums-grid">
        <?php foreach ($albums as $album): ?>

          <div class="album-card">

            <img
              src="images/albums/<?= htmlspecialchars($album['cover_image'] ?? 'placeholder.jpg', ENT_QUOTES, 'UTF-8') ?>"
              alt="<?= htmlspecialchars($album['alt_text'] ?: ($album['title'] . ($album['year'] ? ', ' . $album['year'] : '') . ' — Kutumba album cover'), ENT_QUOTES, 'UTF-8') ?>"
              loading="lazy"
            >

            <div class="album-card__info">
              <p class="album-card__title">
                <?= htmlspecialchars($album['title'], ENT_QUOTES, 'UTF-8') ?>
              </p>
              <p class="album-card__year">
                <?= htmlspecialchars((string)$album['year'], ENT_QUOTES, 'UTF-8') ?>
              </p>
              <?php if (!empty($album['description'])): ?>
                <p style="font-size:.75rem;color:var(--muted);margin-top:4px">
                  <?= htmlspecialchars($album['description'], ENT_QUOTES, 'UTF-8') ?>
                </p>
              <?php endif; ?>
            </div>

          </div>

        <?php endforeach; ?>
      </div>

    <?php endif; ?>
  </div>
</section>

<?php
$content = ob_get_clean();
require BASE_PATH . '/views/layout.php';
