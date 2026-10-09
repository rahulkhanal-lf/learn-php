<?php
/**
 * LAYOUT — master template, wraps every page.
 *
 * CONCEPT: ob_start / ob_get_clean pattern
 * ─────────────────────────────────────────
 * PHP has no JSX or template slots, but we can simulate them:
 *
 *   Page file does:
 *     ob_start();         ← start buffering output into memory
 *     ?>  <h1>Hello</h1>  <?php
 *     $content = ob_get_clean();  ← capture buffer into $content
 *     require 'layout.php';       ← layout outputs $content in <main>
 *
 * This is how PHP frameworks (Laravel, Symfony) implement @yield / {slot}.
 *
 * Variables expected:
 *   $pageTitle   string  — <title> tag
 *   $currentPage string  — passed to navbar for active link
 *   $content     string  — captured HTML from the page file
 */

$pageTitle   ??= 'Kutumba Band';
$currentPage ??= 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Kutumba — Nepal's premier folk instrumental ensemble preserving traditional music.">

  <title><?= htmlspecialchars($pageTitle) ?> — Kutumba Band</title>

  <!--
    CONCEPT: Linking a static CSS file.
    Apache serves public/css/app.css directly — no PHP processing.
    Path is relative to the current URL base (public/).
  -->
  <link rel="stylesheet" href="css/app.css">
</head>
<body>

  <?php
  /*
   * CONCEPT: require vs include
   * require  → PHP halts with fatal error if file not found (use for critical files)
   * include  → PHP continues with a warning (use for optional parts)
   *
   * BASE_PATH is defined in index.php so require paths always resolve
   * from the same root, no matter which page is loaded.
   */
  require BASE_PATH . '/views/partials/navbar.php';
  ?>

  <main id="main-content" tabindex="-1">
    <?= $content ?>
  </main>

  <?php require BASE_PATH . '/views/partials/footer.php'; ?>

</body>
</html>
