<?php
/**
 * PAGE: Gallery
 *
 * PHP CONCEPTS HERE:
 *  - glob() — PHP function to list files from a directory
 *  - basename() — extract filename from a full path
 *  - array_map() to transform array values
 *  - Alternative: could load from DB (gallery table) instead of filesystem
 *
 * TWO APPROACHES SHOWN:
 *  1. Filesystem (glob) — simple, no DB needed, used here
 *  2. DB-backed — more flexible, allows captions/categories
 */

$pageTitle   = 'Gallery';
$currentPage = 'gallery';

/*
 * CONCEPT: glob() scans the filesystem for matching files.
 * glob('path/*.jpg') returns an array of file paths.
 * BASE_PATH is the absolute path to public/ (defined in index.php).
 *
 * In production you'd store gallery entries in the DB (with caption, category)
 * and just reference the filename. Here we keep it simple with filesystem scanning.
 */
$imageDir   = BASE_PATH . '/images/gallery/';
$imagePaths = glob($imageDir . '*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];

// array_map: transform each full path → just the filename for the <img src>
// basename('/var/www/public/images/gallery/photo1.jpg') → 'photo1.jpg'
$images = array_map('basename', $imagePaths);

ob_start();
?>

<div class="page-banner">
  <div class="container">
    <h1>Gallery</h1>
  </div>
</div>

<section class="section">
  <div class="container">

    <?php if (empty($images)): ?>
      <p style="text-align:center;color:var(--muted);padding:48px 0">
        Gallery images will appear here. Add photos to <code>public/images/gallery/</code>.
      </p>
    <?php else: ?>

      <div class="gallery-grid">
        <?php foreach ($images as $img): ?>
          <img
            src="images/gallery/<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>"
            alt="Kutumba performance"
            loading="lazy"
          >
        <?php endforeach; ?>
      </div>

    <?php endif; ?>

  </div>
</section>

<?php
$content = ob_get_clean();
require BASE_PATH . '/views/layout.php';
