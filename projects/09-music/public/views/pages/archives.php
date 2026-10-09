<?php
/**
 * PAGE: Archives
 *
 * PHP CONCEPTS HERE:
 *  - Nested DB data (archive + its entries in one query)
 *  - foreach inside foreach for nested arrays
 *  - Model method returning structured nested array
 */

use App\Models\Archive;

$archives    = (new Archive())->findAllWithEntries();
$pageTitle   = 'Archives';
$currentPage = 'archives';
ob_start();
?>

<div class="page-banner">
  <div class="container"><h1>Archives</h1></div>
</div>

<section class="section">
  <div class="container">
    <h2 class="section-title">Archives</h2>
    <p class="section-sub">Historical records of Kutumba's journey</p>

    <div style="max-width:800px;margin-inline:auto">
      <?php foreach ($archives as $arch): ?>
        <div class="timeline-item" style="margin-bottom:10px">
          <div class="timeline-year"><?= (int)$arch['year'] ?></div>
          <div class="timeline-content">
            <!--
              CONCEPT: Nested foreach.
              $arch['entries'] is an array of DB rows (archive_entries table).
              We loop inside the outer foreach to render each entry.
            -->
            <?php foreach ($arch['entries'] as $entry): ?>
              <p style="font-size:.85rem;color:var(--text);padding:2px 0">
                — <?= htmlspecialchars($entry['entry'], ENT_QUOTES, 'UTF-8') ?>
              </p>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php $content = ob_get_clean(); require BASE_PATH . '/views/layout.php';
