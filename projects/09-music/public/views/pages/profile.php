<?php
/**
 * PAGE: Profile — Social initiatives timeline
 *
 * PHP CONCEPTS HERE:
 *  - Multiple model usage in one page
 *  - array_chunk() to split data into columns
 *  - Date formatting (if storing real dates)
 */

use App\Models\Initiative;

$initiatives = (new Initiative())->findAll();
$pageTitle   = 'Profile';
$currentPage = 'profile';

ob_start();
?>

<div class="page-banner">
  <div class="container">
    <h1>Profile</h1>
  </div>
</div>

<section class="section">
  <div class="container">

    <h2 class="section-title">Kutumba Taking Part in Social Initiatives</h2>
    <p class="section-sub">Brief Profile</p>

    <div class="profile-layout">

      <!-- Left: Bio + performance photos -->
      <div class="profile-text">
        <h2>Social Responsibility</h2>
        <p>
          From the very beginning of the band origin, it has been putting up its efforts on the social causes.
          They have been active part of the fund raisers as well as the community development process.
          Combining with their musical talent they have raised funds for the flood disasters, for the
          awareness projects for different social institutions, raising fund for other numerous causes as well
          as the recent biggest earthquake in Nepal. Band has been highly appreciated for its social
          initiative effort.
        </p>

        <!-- Performance photo grid -->
        <div class="profile-photos" style="margin-top:24px">
          <img src="images/gallery/perf1.jpg" alt="Kutumba performance" loading="lazy">
          <img src="images/gallery/perf2.jpg" alt="Kutumba performance" loading="lazy">
          <img src="images/gallery/perf3.jpg" alt="Kutumba performance" loading="lazy">
          <img src="images/gallery/perf4.jpg" alt="Kutumba performance" loading="lazy">
        </div>
      </div>

      <!-- Right: Initiatives timeline -->
      <div>
        <h2 style="color:var(--orange);font-size:1.1rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:20px">
          Kutumba Initiatives
        </h2>

        <!--
          CONCEPT: foreach over DB rows to build a timeline UI.
          The same PHP loop renders each timeline entry — the HTML
          structure repeats, but the data changes each iteration.
        -->
        <div class="timeline">
          <?php foreach ($initiatives as $init): ?>
            <div class="timeline-item">

              <div class="timeline-year">
                <?= htmlspecialchars((string)$init['year'], ENT_QUOTES, 'UTF-8') ?>
              </div>

              <div class="timeline-content">
                <strong><?= htmlspecialchars($init['title'], ENT_QUOTES, 'UTF-8') ?></strong>

                <!--
                  CONCEPT: !empty() check.
                  empty() returns true for: null, '', '0', 0, [], false.
                  Safer than isset() alone when the value might be an empty string.
                -->
                <?php if (!empty($init['date_text'])): ?>
                  <p><?= htmlspecialchars($init['date_text'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <?php if (!empty($init['locations'])): ?>
                  <p><?= htmlspecialchars($init['locations'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
              </div>

            </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
require BASE_PATH . '/views/layout.php';
