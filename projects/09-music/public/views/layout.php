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
$metaDesc    ??= 'Kutumba — Nepal\'s premier folk instrumental ensemble preserving traditional music.';
$ogImage     ??= 'images/og-default.jpg';
$jsonLd      ??= null;

// Build absolute canonical URL using clean URLs (/about, /albums, etc.)
$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl   = $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
           . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/') . '/';
$canonical = $currentPage === 'home' ? $baseUrl : $baseUrl . rawurlencode($currentPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ═══ PRIMARY META ═══════════════════════════════════════════════════
       <title>     — shown in browser tab + Google search headline
       description — shown as the snippet text below the title in search
       robots      — tells crawlers what they're allowed to do
  ══════════════════════════════════════════════════════════════════════ -->
  <title><?= htmlspecialchars($pageTitle . ' — Kutumba Band', ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($metaDesc, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="robots" content="index, follow">

  <!-- ═══ CANONICAL ══════════════════════════════════════════════════════
       Tells Google the "preferred" URL for this page.
       Prevents duplicate content penalties if the same page is
       reachable via multiple URLs (e.g. with/without trailing slash).
  ══════════════════════════════════════════════════════════════════════ -->
  <link rel="canonical" href="<?= htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') ?>">

  <!-- ═══ OPEN GRAPH ═════════════════════════════════════════════════════
       Used by Facebook, WhatsApp, LinkedIn when someone shares a link.
       og:image should be at least 1200×630px for best display.
  ══════════════════════════════════════════════════════════════════════ -->
  <meta property="og:type"        content="website">
  <meta property="og:site_name"   content="Kutumba Band">
  <meta property="og:title"       content="<?= htmlspecialchars($pageTitle . ' — Kutumba Band', ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:description" content="<?= htmlspecialchars($metaDesc, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:image"       content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:url"         content="<?= htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') ?>">

  <!-- Twitter Card (works on X / Twitter) -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= htmlspecialchars($pageTitle . ' — Kutumba Band', ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($metaDesc, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:image"       content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">

  <!--
    CONCEPT: Linking a static CSS file.
    Apache serves public/css/app.css directly — no PHP processing.
    Path is relative to the current URL base (public/).
  -->
  <link rel="stylesheet" href="css/app.css">

  <!-- ═══ STRUCTURED DATA (JSON-LD) ══════════════════════════════════════
       Schema.org markup in JSON-LD format.
       Google reads this to understand what this page IS — not just
       what it says. Enables rich results (knowledge panels, etc.).
       Set $jsonLd = [...] in any page file to inject structured data.
  ══════════════════════════════════════════════════════════════════════ -->
  <?php if ($jsonLd): ?>
  <script type="application/ld+json">
  <?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
  </script>
  <?php endif; ?>
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
