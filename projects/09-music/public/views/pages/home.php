<?php
/**
 * PAGE: Home
 *
 * PHP CONCEPTS HERE:
 *  - ob_start / ob_get_clean (layout pattern)
 *  - foreach loop over DB data
 *  - htmlspecialchars() for XSS prevention
 *  - Ternary operator for optional fields
 *  - PHP inside HTML (mixing modes with <?php ?> tags)
 */

use App\Models\Instrument;

use App\Database\Connection;

$instruments = (new Instrument())->findAll();
$pageTitle   = 'Home';
$currentPage = 'home';

// ── JSON-LD: MusicGroup structured data ───────────────────────────────────────
// CONCEPT: JSON-LD (JavaScript Object Notation for Linked Data)
//   We embed machine-readable data about this band using Schema.org vocabulary.
//   Google uses this to build Knowledge Panels, rich results, and understand
//   what type of entity this site represents — without parsing prose text.
//
//   @context → defines the vocabulary (Schema.org)
//   @type    → the kind of thing (MusicGroup, Person, Event, Product, etc.)
//   member   → list of people in the group
//
$pdo_seo   = Connection::getInstance();
$seoMembers = $pdo_seo->query("SELECT name, instrument FROM members ORDER BY display_order ASC")->fetchAll();

$jsonLd = [
    '@context' => 'https://schema.org',
    '@type'    => 'MusicGroup',
    'name'     => 'Kutumba',
    'description' => 'Nepal\'s premier folk instrumental ensemble committed to the research, preservation and celebration of indigenous Nepali music.',
    'genre'    => ['Folk music', 'Traditional music', 'World music'],
    'foundingLocation' => [
        '@type' => 'Place',
        'name'  => 'Kathmandu, Nepal',
    ],
    'member' => array_map(fn($m) => [
        '@type'             => 'OrganizationRole',
        'roleName'          => $m['instrument'] ?? 'Musician',
        'member'            => ['@type' => 'Person', 'name' => $m['name']],
    ], $seoMembers),
];

// ob_start() buffers all output until ob_get_clean().
// Nothing is sent to the browser yet — it goes into memory.
ob_start();
?>

<!-- ─── Hero Slider ─────────────────────────────────────────────── -->
<!--
  CONCEPT: Vanilla JS slider — no jQuery, no framework.
  Hero images are in public/images/hero/ — Apache serves them statically.
  Slides auto-advance every 5s and support prev/next buttons + dot nav.
-->
<section class="hero" aria-label="Hero slideshow">

  <div class="hero__slides" id="hero-slides">
    <div class="hero__slide active" style="background-image:url('images/hero/slide1.jpg')">
      <div class="hero__overlay"></div>
    </div>
    <div class="hero__slide" style="background-image:url('images/hero/slide2.jpg')">
      <div class="hero__overlay"></div>
    </div>
    <div class="hero__slide" style="background-image:url('images/hero/slide3.jpg')">
      <div class="hero__overlay"></div>
    </div>
  </div>

  <button class="hero__btn hero__btn--prev" id="hero-prev" aria-label="Previous slide">&#8249;</button>
  <button class="hero__btn hero__btn--next" id="hero-next" aria-label="Next slide">&#8250;</button>

  <div class="hero__dots" id="hero-dots">
    <button class="hero__dot active" data-index="0" aria-label="Slide 1"></button>
    <button class="hero__dot"        data-index="1" aria-label="Slide 2"></button>
    <button class="hero__dot"        data-index="2" aria-label="Slide 3"></button>
  </div>

</section>

<script>
/*
 * CONCEPT: Self-contained JS module for the slider.
 * Pure vanilla JS — no dependency on jQuery or any library.
 * The script tag goes right after the HTML it controls.
 */
(function() {
  const slides = document.querySelectorAll('.hero__slide');
  const dots   = document.querySelectorAll('.hero__dot');
  let current  = 0;
  let timer;

  function goTo(n) {
    slides[current].classList.remove('active');
    dots[current].classList.remove('active');
    current = (n + slides.length) % slides.length;
    slides[current].classList.add('active');
    dots[current].classList.add('active');
  }

  function startAuto() {
    timer = setInterval(() => goTo(current + 1), 5000);
  }

  function reset() { clearInterval(timer); startAuto(); }

  document.getElementById('hero-prev').addEventListener('click', () => { goTo(current - 1); reset(); });
  document.getElementById('hero-next').addEventListener('click', () => { goTo(current + 1); reset(); });
  dots.forEach(d => d.addEventListener('click', () => { goTo(+d.dataset.index); reset(); }));

  startAuto();
})();
</script>

<!-- ─── Introduction ─────────────────────────────────────────────── -->
<section class="section intro">
  <div class="container">
    <h2>KUTUMBA</h2>
    <h3 style="color:var(--orange);font-size:.9rem;text-transform:uppercase;letter-spacing:.15em;margin-bottom:24px">Introduction</h3>
    <p>
      Kutumba is a folk instrumental ensemble committed to the research, preservation and celebration of
      the diversity that exists in indigenous Nepali music. The word 'Kutumba' holds a special meaning in
      the Nepali language. It stands for a unique bond amongst community members. As their name, Kutumba
      is all about bringing together traditional folk tunes and instruments with new and improvised sounds
      and ideas. Kutumba is a folk instrumental ensemble, group of six professionals from Kathmandu. Having
      come together for the preservation of their culture and art, Kutumba wishes to spread love and joy of
      Nepali folk music throughout the world. Self motivated and self driven, Kutumba is a group with their
      own unique sound and vision. The seven members have their roots and backgrounds in music. Kutumba is
      the harmony of traditional roots, culture and new sounds.
    </p>
  </div>
</section>

<!-- ─── Instruments ──────────────────────────────────────────────── -->
<!--
  CONCEPT: Rendering DB data with foreach.
  $instruments comes from the Instrument model (PDO query).
  Each $instrument is an associative array: $instrument['name'], etc.
-->
<section class="section instruments">
  <div class="container">
    <h2 class="section-title">Kutumba Instruments</h2>
    <p class="section-sub">Traditional folk instruments at the heart of our sound</p>

    <?php foreach ($instruments as $inst): ?>
      <div class="instrument">

        <!--
          CONCEPT: htmlspecialchars() — always escape user/DB data when outputting to HTML.
          Prevents XSS: if the DB had <script>alert(1)</script>, it becomes harmless text.
          ENT_QUOTES escapes both single and double quotes.
          'UTF-8' is required for multi-byte characters (Nepali text, etc.)
        -->
        <img
          class="instrument__img"
          src="images/instruments/<?= htmlspecialchars($inst['photo'] ?? 'placeholder.svg', ENT_QUOTES, 'UTF-8') ?>"
          alt="<?= htmlspecialchars($inst['name'], ENT_QUOTES, 'UTF-8') ?>"
        >

        <div>
          <h3 class="instrument__name"><?= htmlspecialchars($inst['name'], ENT_QUOTES, 'UTF-8') ?></h3>
          <!--
            CONCEPT: nl2br() — converts \n newlines in DB text to <br> tags
            so multi-paragraph descriptions render correctly.
          -->
          <p class="instrument__desc">
            <?= nl2br(htmlspecialchars($inst['description'], ENT_QUOTES, 'UTF-8')) ?>
          </p>
        </div>

      </div>
    <?php endforeach; ?>

    <div style="text-align:center;margin-top:40px">
      <a href="index.php?page=about" class="btn btn-outline">Read More</a>
    </div>
  </div>
</section>

<!-- ─── Location ─────────────────────────────────────────────────── -->
<section class="section location">
  <div class="container">
    <div class="location__grid">

      <div class="location__map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3533.4!2d85.3178!3d27.6669!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjfCsDQwJzAwLjgiTiA4NcKwMTknMDQuMSJF!5e0!3m2!1sen!2snp!4v1234567890"
          width="100%"
          height="300"
          style="border:0"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          title="Kutumba location map"
        ></iframe>
      </div>

      <div class="location__info">
        <h2>Kutumba Location</h2>

        <div class="location__item">
          <div class="location__icon">&#9679;</div>
          <div>
            <strong>Dhaugal Lalitpur,</strong><br>
            <span style="color:var(--muted)">Bagmati (Nepal)</span>
          </div>
        </div>

        <div class="location__item">
          <div class="location__icon">&#9742;</div>
          <div>
            +977 9841239647<br>
            +977 9851058101
          </div>
        </div>

        <div class="location__item">
          <div class="location__icon">&#9993;</div>
          <div>mail@kutumba.com.np</div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php
// ob_get_clean() stops buffering and returns everything captured since ob_start().
// $content now holds the full HTML of this page — layout.php will echo it.
$content = ob_get_clean();

// require layout.php — it wraps $content in <html>, navbar, footer.
// BASE_PATH is defined in index.php (the front controller).
require BASE_PATH . '/views/layout.php';
