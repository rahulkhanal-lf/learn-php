<?php
/**
 * PAGE: Events
 *
 * PHP CONCEPTS HERE:
 *  - Multiple model queries on one page
 *  - Filtering data server-side (?year= query param)
 *  - (int) cast for safe type coercion from user input
 *  - Separation: Model queries data, View renders it
 */

use App\Models\Event;

$eventModel = new Event();
$years      = $eventModel->findYears();     // list of past event years
$upcoming   = $eventModel->findUpcoming();  // upcoming event posters

// Read ?year= from URL, cast to int for safety
// CONCEPT: Never trust $_GET directly — always sanitize.
// (int) cast: '2019abc' → 2019, '' → 0, null → 0
$selectedYear = (int) ($_GET['year'] ?? ($years[0] ?? 0));
$pastEvents   = $selectedYear ? $eventModel->findByYear($selectedYear) : [];

$pageTitle   = 'Events';
$currentPage = 'events';

ob_start();
?>

<div class="page-banner">
  <div class="container">
    <h1>Events</h1>
  </div>
</div>

<section class="section">
  <div class="container">

    <div class="events-layout">

      <!-- Left: Past events by year -->
      <div>
        <h2>Events Till Date</h2>

        <!--
          CONCEPT: Building links dynamically.
          Each year button links to ?page=events&year=2019 etc.
          The server reads $_GET['year'] and queries the DB accordingly.
          This is server-side filtering — no JS needed.
        -->
        <div style="margin-bottom:24px">
          <?php foreach ($years as $year): ?>
            <a
              href="index.php?page=events&year=<?= (int)$year ?>"
              class="year-btn <?= $selectedYear === (int)$year ? 'year-btn--active' : '' ?>"
            >
              <?= (int)$year ?>
            </a>
          <?php endforeach; ?>
        </div>

        <?php if (!empty($pastEvents)): ?>
          <div style="background:var(--bg-2);border:1px solid var(--border);padding:16px">
            <h3 style="color:var(--orange);margin-bottom:12px;font-size:.9rem;text-transform:uppercase">
              <?= $selectedYear ?> Events
            </h3>
            <?php foreach ($pastEvents as $event): ?>
              <div style="padding:8px 0;border-bottom:1px solid var(--border)">
                <strong style="color:var(--text);font-size:.85rem">
                  <?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>
                </strong>
                <?php if ($event['date_text']): ?>
                  <p style="font-size:.78rem;color:var(--muted)">
                    <?= htmlspecialchars($event['date_text'], ENT_QUOTES, 'UTF-8') ?>
                  </p>
                <?php endif; ?>
                <?php if ($event['location']): ?>
                  <p style="font-size:.78rem;color:var(--muted)">
                    📍 <?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8') ?>
                  </p>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Right: Upcoming events -->
      <div>
        <h2>Upcoming Events</h2>

        <?php if (empty($upcoming)): ?>
          <p style="color:var(--muted);font-size:.9rem">No upcoming events at this time.</p>
        <?php else: ?>
          <?php foreach ($upcoming as $event): ?>
            <div class="upcoming-event">
              <?php if ($event['image']): ?>
                <img
                  src="images/gallery/<?= htmlspecialchars($event['image'], ENT_QUOTES, 'UTF-8') ?>"
                  alt="<?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>"
                  loading="lazy"
                >
              <?php endif; ?>
              <div class="upcoming-event__body">
                <p class="upcoming-event__title">
                  <?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>
                </p>
                <?php if ($event['date_text']): ?>
                  <p class="upcoming-event__meta"><?= htmlspecialchars($event['date_text'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
require BASE_PATH . '/views/layout.php';
