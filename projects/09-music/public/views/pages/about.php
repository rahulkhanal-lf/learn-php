<?php
/**
 * PAGE: About Us
 *
 * PHP CONCEPTS HERE:
 *  - Model instantiation and method calls
 *  - foreach with index ($i => $member)
 *  - Null coalescing operator (??) for optional fields
 *  - String interpolation vs concatenation
 */

use App\Models\Member;

$members     = (new Member())->findAll();
$pageTitle   = 'About Us';
$currentPage = 'about';

ob_start();
?>

<div class="page-banner">
  <div class="container">
    <h1>About Us</h1>
  </div>
</div>

<section class="section">
  <div class="container">

    <h2 class="section-title">Kutumba, More Than Just Performers</h2>

    <p class="about-intro">
      Kutumba is a folk instrumental ensemble committed to the research, preservation and celebration of
      the diversity that exists in indigenous Nepali music. Kutumba firmly believes that the richness in
      Nepali music is directly significant of the rich diversity that exists in the Nepali people. As we
      become more aware of the multiculturalism in Nepali society, be it in Kathmandu or other parts of the
      country, political, social and developmental attention is trained on recognizing differences and
      ensuring rights for our diverse groups of people.
    </p>

    <!-- Band Members -->
    <h2 class="section-title" style="margin-bottom:40px">Kutumba Band Members</h2>

    <!--
      CONCEPT: foreach with key ($i => $member)
      $i = 0-based index (useful for numbering or CSS nth-child tricks)
      $member = associative array from DB row
    -->
    <div class="members-grid">
      <?php foreach ($members as $i => $member): ?>

        <div class="member-card">

          <!--
            CONCEPT: Null coalescing (??) for default values.
            If $member['photo'] is null or not set, use 'placeholder.jpg'.
            Cleaner than: isset($member['photo']) ? $member['photo'] : 'placeholder.jpg'
          -->
          <img
            class="member-card__photo"
            src="images/members/<?= htmlspecialchars($member['photo'] ?? 'placeholder.jpg', ENT_QUOTES, 'UTF-8') ?>"
            alt="<?= htmlspecialchars($member['alt_text'] ?: ($member['name'] . ', ' . ($member['instrument'] ?? 'Musician') . ' of Kutumba'), ENT_QUOTES, 'UTF-8') ?>"
            loading="lazy"
          >

          <div class="member-card__body">
            <p class="member-card__name">
              <?= htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p class="member-card__instrument">
              <?= htmlspecialchars($member['instrument'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <?php if (!empty($member['bio'])): ?>
              <p style="font-size:.78rem;color:var(--muted);margin-top:8px">
                <?= htmlspecialchars($member['bio'], ENT_QUOTES, 'UTF-8') ?>
              </p>
            <?php endif; ?>
          </div>

        </div>

      <?php endforeach; ?>
    </div>

  </div>
</section>

<?php
$content = ob_get_clean();
require BASE_PATH . '/views/layout.php';
