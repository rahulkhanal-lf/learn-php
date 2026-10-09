<?php
/**
 * PAGE: Projects
 *
 * PHP CONCEPTS HERE:
 *  - Model query (same pattern as every other DB page)
 *  - CSS Grid via inline style (quick one-off layout)
 *  - htmlspecialchars() XSS prevention
 */

use App\Models\Project;

$projects    = (new Project())->findAll();
$pageTitle   = 'Projects';
$currentPage = 'projects';
ob_start();
?>

<div class="page-banner">
  <div class="container"><h1>Projects</h1></div>
</div>

<section class="section">
  <div class="container">
    <h2 class="section-title">Our Projects</h2>
    <p class="section-sub">Kutumba's ongoing musical and cultural initiatives</p>

    <?php if (empty($projects)): ?>
      <p style="text-align:center;color:var(--muted)">No projects found.</p>
    <?php else: ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px">
        <?php foreach ($projects as $project): ?>
          <div class="project-card">
            <span class="project-card__year"><?= (int)$project['year'] ?></span>
            <h3 class="project-card__title">
              <?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>
            </h3>
            <p class="project-card__desc">
              <?= htmlspecialchars($project['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php $content = ob_get_clean(); require BASE_PATH . '/views/layout.php';
