<?php
/**
 * COMPONENT: Navbar
 *
 * PHP equivalent of a reusable React component.
 * Included on every page via layout.php.
 *
 * Expects: $currentPage string — used to add 'active' class.
 *
 * CONCEPT: $navLinks is defined here (single source of truth).
 * If you add a page, add one line here — every nav auto-updates.
 */

$currentPage ??= 'home';

$navLinks = [
    'home'     => ['label' => 'Home',     'href' => 'index.php'],
    'about'    => ['label' => 'About Us', 'href' => 'index.php?page=about'],
    'profile'  => ['label' => 'Profile',  'href' => 'index.php?page=profile'],
    'projects' => ['label' => 'Projects', 'href' => 'index.php?page=projects'],
    'gallery'  => ['label' => 'Gallery',  'href' => 'index.php?page=gallery'],
    'events'   => ['label' => 'Events',   'href' => 'index.php?page=events'],
    'albums'   => ['label' => 'Albums',   'href' => 'index.php?page=albums'],
    'archives' => ['label' => 'Archives', 'href' => 'index.php?page=archives'],
    'contact'  => ['label' => 'Contacts', 'href' => 'index.php?page=contact'],
];
?>

<header class="navbar" role="banner">
  <div class="container">
    <nav class="navbar__inner" aria-label="Main navigation">

      <!-- Brand / Logo -->
      <a href="index.php" class="navbar__brand" aria-label="Kutumba home">
        <!--
          CONCEPT: Static image.
          Path is relative to public/ (the web root).
          Apache serves images/* directly — no PHP involved.
          Always set width/height to prevent layout shift.
        -->
        <img src="images/logo.svg" alt="Kutumba" width="120" height="44">
      </a>

      <!-- Desktop nav links -->
      <ul class="navbar__links" role="list">
        <?php foreach ($navLinks as $key => $link): ?>
          <li>
            <a
              href="<?= $link['href'] ?>"
              class="<?= $currentPage === $key ? 'active' : '' ?>"
              <?= $currentPage === $key ? 'aria-current="page"' : '' ?>
            ><?= $link['label'] ?></a>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- Mobile hamburger button -->
      <button
        class="navbar__hamburger"
        id="nav-toggle"
        aria-label="Toggle navigation"
        aria-expanded="false"
        aria-controls="nav-drawer"
      >
        <span></span><span></span><span></span>
      </button>

    </nav>
  </div>

  <!-- Mobile drawer — hidden until hamburger clicked -->
  <div class="navbar__drawer" id="nav-drawer" role="navigation">
    <?php foreach ($navLinks as $key => $link): ?>
      <a href="<?= $link['href'] ?>"
         class="<?= $currentPage === $key ? 'active' : '' ?>"
      ><?= $link['label'] ?></a>
    <?php endforeach; ?>
  </div>
</header>

<!--
  Social sidebar — fixed to right side of screen.
  CONCEPT: position:fixed in CSS — stays visible while scrolling.
  Uses inline icon text since we have no icon library installed.
-->
<aside class="social-sidebar" aria-label="Social media links">
  <a href="https://www.facebook.com/KutumbaNepal/" target="_blank" rel="noopener" aria-label="Facebook">f</a>
  <a href="https://twitter.com/KutumbaOfficial" target="_blank" rel="noopener" aria-label="Twitter">t</a>
  <a href="https://www.instagram.com/kutumba_nepal/" target="_blank" rel="noopener" aria-label="Instagram">in</a>
  <a href="https://www.youtube.com/user/kutumbaofficialvideo" target="_blank" rel="noopener" aria-label="YouTube">▶</a>
</aside>

<script>
  // Minimal vanilla JS — toggles mobile drawer
  const toggle = document.getElementById('nav-toggle');
  const drawer = document.getElementById('nav-drawer');
  toggle.addEventListener('click', () => {
    const open = drawer.classList.toggle('open');
    toggle.setAttribute('aria-expanded', open);
  });
</script>
