<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($adminTitle ?? 'Admin', ENT_QUOTES, 'UTF-8') ?> — Kutumba Admin</title>
  <link rel="stylesheet" href="../css/app.css">
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/Sortable.min.js"></script>
  <style>
    body { background: #f4f4f5; display: flex; flex-direction: column; min-height: 100vh; }

    /* ── Admin navbar ── */
    .admin-nav {
      background: #111;
      height: 56px;
      display: flex;
      align-items: center;
      padding: 0 28px;
      gap: 24px;
      position: sticky;
      top: 0;
      z-index: 200;
    }
    .admin-nav__brand { display: flex; align-items: center; gap: 10px; }
    .admin-nav__brand img { height: 28px; filter: brightness(0) invert(1); }
    .admin-nav__brand span { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: #71717a; }
    .admin-nav__user { margin-left: auto; font-size: .8rem; color: #71717a; }
    .admin-nav__logout { font-size: .78rem; font-weight: 600; color: var(--orange); margin-left: 16px; text-decoration: none; }
    .admin-nav__logout:hover { color: var(--orange-dark); }

    /* ── Layout ── */
    .admin-wrap { display: flex; flex: 1; }

    /* ── Sidebar ── */
    .admin-sidebar {
      width: 220px;
      background: #fff;
      border-right: 1px solid var(--border);
      padding: 24px 0;
      flex-shrink: 0;
    }
    .admin-sidebar__section {
      font-size: .68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .12em;
      color: #a1a1aa;
      padding: 0 20px;
      margin: 20px 0 8px;
    }
    .admin-sidebar__section:first-child { margin-top: 0; }
    .admin-sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 20px;
      font-size: .84rem;
      font-weight: 500;
      color: #52525b;
      transition: background var(--transition), color var(--transition);
    }
    .admin-sidebar a:hover { background: var(--orange-light); color: var(--orange); }
    .admin-sidebar a.active { background: var(--orange-light); color: var(--orange); font-weight: 700; border-right: 3px solid var(--orange); }

    /* ── Main content ── */
    .admin-main { flex: 1; padding: 32px 36px; overflow: auto; }

    /* ── Page header ── */
    .admin-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; }
    .admin-header h1 { font-size: 1.4rem; font-weight: 800; color: var(--text); }

    /* ── Cards / stats ── */
    .admin-stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; margin-bottom: 32px; }
    .stat-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 20px; }
    .stat-card__num { font-size: 2rem; font-weight: 900; color: var(--orange); }
    .stat-card__label { font-size: .78rem; color: var(--muted); margin-top: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }

    /* ── Table ── */
    .admin-table-wrap { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; }
    .admin-table { width: 100%; border-collapse: collapse; font-size: .88rem; }
    .admin-table th { background: #f4f4f5; padding: 12px 16px; text-align: left; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); border-bottom: 1px solid var(--border); }
    .admin-table td { padding: 12px 16px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-table tr:hover td { background: #fafafa; }

    /* ── Form panel ── */
    .admin-panel { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 28px; margin-bottom: 28px; }
    .admin-panel h2 { font-size: 1rem; font-weight: 700; color: var(--text); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border); }

    /* ── Action links ── */
    .action-link { font-size: .78rem; font-weight: 600; padding: 4px 10px; border-radius: var(--radius-sm); border: 1px solid; display: inline-block; }
    .action-link--edit  { color: var(--orange); border-color: var(--orange); }
    .action-link--edit:hover  { background: var(--orange); color: #fff; }
    .action-link--delete { color: #dc2626; border-color: #dc2626; margin-left: 6px; }
    .action-link--delete:hover { background: #dc2626; color: #fff; }

    /* ── Flash ── */
    .admin-flash { padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: .88rem; font-weight: 500; }
    .admin-flash--ok  { background: rgba(34,197,94,.1); color: #15803d; border: 1px solid rgba(34,197,94,.3); }
    .admin-flash--err { background: rgba(239,68,68,.1);  color: #b91c1c; border: 1px solid rgba(239,68,68,.3); }

    /* ── Drag-and-drop ── */
    .drag-handle { cursor: grab; color: #c4c4cc; font-size: 1rem; padding: 0 2px; user-select: none; display: inline-block; }
    .drag-handle:hover { color: #71717a; }
    .drag-handle:active { cursor: grabbing; }
    .sortable-ghost td { background: #fff7ed !important; opacity: .5; }
    .sortable-drag { background: #fff; box-shadow: 0 6px 24px rgba(0,0,0,.12); }
    #reorder-saved { display:none; position:fixed; bottom:24px; right:24px; background:#15803d; color:#fff; padding:10px 18px; border-radius:8px; font-size:.84rem; font-weight:600; z-index:999; box-shadow:0 4px 12px rgba(0,0,0,.2); }
  </style>
</head>
<body>

<!-- Top navbar -->
<nav class="admin-nav">
  <div class="admin-nav__brand">
    <img src="../images/logo.svg" alt="Kutumba">
    <span>Admin</span>
  </div>
  <span class="admin-nav__user">👤 <?= htmlspecialchars($_SESSION['admin_user'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
  <a href="index.php?page=logout" class="admin-nav__logout">Sign out</a>
</nav>

<div class="admin-wrap">

  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-sidebar__section">Overview</div>
    <a href="index.php?page=dashboard" <?= ($activePage??'')==='dashboard'?'class="active"':'' ?>>📊 Dashboard</a>

    <div class="admin-sidebar__section">Content</div>
    <a href="index.php?page=members"     <?= ($activePage??'')==='members'     ?'class="active"':'' ?>>👥 Members</a>
    <a href="index.php?page=instruments" <?= ($activePage??'')==='instruments' ?'class="active"':'' ?>>🎵 Instruments</a>
    <a href="index.php?page=events"      <?= ($activePage??'')==='events'      ?'class="active"':'' ?>>📅 Events</a>
    <a href="index.php?page=albums"      <?= ($activePage??'')==='albums'      ?'class="active"':'' ?>>💿 Albums</a>
    <a href="index.php?page=projects"    <?= ($activePage??'')==='projects'    ?'class="active"':'' ?>>🎯 Projects</a>
    <a href="index.php?page=initiatives" <?= ($activePage??'')==='initiatives' ?'class="active"':'' ?>>🌱 Initiatives</a>
    <a href="index.php?page=archives"    <?= ($activePage??'')==='archives'    ?'class="active"':'' ?>>📁 Archives</a>
    <a href="index.php?page=gallery"     <?= ($activePage??'')==='gallery'     ?'class="active"':'' ?>>🖼️ Gallery</a>
    <a href="index.php?page=hero"        <?= ($activePage??'')==='hero'        ?'class="active"':'' ?>>🖼️ Hero Slides</a>

    <div class="admin-sidebar__section">Messages</div>
    <?php
    // Unread badge — small DB query on every admin page load
    try {
        $unreadCount = (new \App\Database\Connection)::getInstance()
            ->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")
            ->fetchColumn();
    } catch (\Exception $e) { $unreadCount = 0; }
    ?>
    <a href="index.php?page=contacts" <?= ($activePage??'')==='contacts' ?'class="active"':'' ?>>
      ✉️ Contacts
      <?php if ($unreadCount > 0): ?>
        <span style="margin-left:auto;background:var(--orange);color:#fff;font-size:.65rem;font-weight:700;padding:2px 7px;border-radius:20px;line-height:1.6">
          <?= $unreadCount ?>
        </span>
      <?php endif; ?>
    </a>

    <div class="admin-sidebar__section">Account</div>
    <a href="index.php?page=password" <?= ($activePage??'')==='password' ?'class="active"':'' ?>>🔑 Change Password</a>

    <div class="admin-sidebar__section">Site</div>
    <a href="../index.php" target="_blank">↗ View Site</a>
  </aside>

  <!-- Main -->
  <main class="admin-main">
    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="admin-flash admin-flash--ok"><?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_err'])): ?>
      <div class="admin-flash admin-flash--err"><?= htmlspecialchars($_SESSION['flash_err'], ENT_QUOTES, 'UTF-8') ?></div>
      <?php unset($_SESSION['flash_err']); ?>
    <?php endif; ?>

    <?= $adminContent ?? '' ?>
  </main>

</div>

<div id="reorder-saved">Order saved ✓</div>

<script>
function initSortable(tbodyId, pageParam) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody || typeof Sortable === 'undefined') return;
    Sortable.create(tbody, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        onEnd: function () {
            const ids = [...tbody.querySelectorAll('tr[data-id]')].map(r => r.dataset.id);
            const form = new FormData();
            form.append('_action', 'reorder');
            ids.forEach(id => form.append('ids[]', id));
            fetch('index.php?page=' + pageParam, { method: 'POST', body: form })
                .then(r => r.json())
                .then(data => {
                    if (data.ok) {
                        const toast = document.getElementById('reorder-saved');
                        toast.style.display = 'block';
                        setTimeout(() => toast.style.display = 'none', 2000);
                    }
                });
        }
    });
}
</script>
</body>
</html>
