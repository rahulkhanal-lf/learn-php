<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($_ENV['APP_NAME'] ?? 'Music App') ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #0f0f0f; color: #e0e0e0; min-height: 100vh; }
        header { background: #1a1a2e; padding: 16px 32px; display: flex; align-items: center; gap: 16px; }
        header h1 { color: #a78bfa; font-size: 1.4rem; }
        .container { max-width: 1000px; margin: 0 auto; padding: 24px 16px; }
        .card { background: #1e1e1e; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        h2 { color: #a78bfa; margin-bottom: 12px; font-size: 1rem; text-transform: uppercase; letter-spacing: .05em; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        input, select { width: 100%; padding: 8px 10px; background: #2a2a2a; border: 1px solid #444; color: #e0e0e0; border-radius: 4px; }
        input:focus, select:focus { outline: none; border-color: #a78bfa; }
        .btn { padding: 8px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: .9rem; }
        .btn-primary { background: #7c3aed; color: #fff; }
        .btn-primary:hover { background: #6d28d9; }
        .btn-danger { background: transparent; color: #f87171; border: 1px solid #f87171; padding: 4px 10px; font-size: .8rem; border-radius: 4px; cursor: pointer; }
        .btn-danger:hover { background: #f87171; color: #fff; }
        .search-bar { display: flex; gap: 8px; margin-bottom: 16px; }
        .search-bar input { flex: 1; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        th { text-align: left; padding: 8px 10px; border-bottom: 2px solid #333; color: #888; font-weight: 600; }
        td { padding: 10px; border-bottom: 1px solid #2a2a2a; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: .75rem; background: #2d1f5e; color: #a78bfa; }
        .msg { padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; font-size: .9rem; }
        .msg-success { background: #14532d; color: #86efac; }
        .errors { background: #450a0a; color: #fca5a5; padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; font-size: .9rem; }
        .empty { text-align: center; padding: 40px; color: #555; }
    </style>
</head>
<body>

<header>
    <h1>🎵 <?= htmlspecialchars($_ENV['APP_NAME'] ?? 'Music App') ?></h1>
    <span style="color:#555;font-size:.8rem">env: <?= htmlspecialchars($_ENV['APP_ENV'] ?? '') ?></span>
</header>

<div class="container">

    <?php if (!empty($msg)): ?>
        <div class="msg msg-success">
            <?= $msg === 'created' ? 'Song added!' : 'Song deleted.' ?>
        </div>
    <?php endif; ?>

    <!-- Add Song Form -->
    <div class="card">
        <h2>Add Song</h2>

        <?php if (!empty($errors)): ?>
            <div class="errors"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=store">
            <div class="grid">
                <div>
                    <label style="font-size:.85rem;color:#888">Title *</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                </div>
                <div>
                    <label style="font-size:.85rem;color:#888">Artist *</label>
                    <select name="artist_id" required>
                        <option value="">— select artist —</option>
                        <?php foreach ($artists as $artist): ?>
                            <option value="<?= $artist['id'] ?>"
                                <?= (($_POST['artist_id'] ?? '') == $artist['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($artist['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="font-size:.85rem;color:#888">Album</label>
                    <input type="text" name="album" value="<?= htmlspecialchars($_POST['album'] ?? '') ?>">
                </div>
                <div>
                    <label style="font-size:.85rem;color:#888">Genre</label>
                    <input type="text" name="genre" value="<?= htmlspecialchars($_POST['genre'] ?? '') ?>">
                </div>
                <div>
                    <label style="font-size:.85rem;color:#888">Duration (seconds)</label>
                    <input type="number" name="duration_seconds" value="<?= htmlspecialchars($_POST['duration_seconds'] ?? '') ?>">
                </div>
                <div>
                    <label style="font-size:.85rem;color:#888">Release Year</label>
                    <input type="number" name="release_year" min="1900" max="<?= date('Y') ?>"
                           value="<?= htmlspecialchars($_POST['release_year'] ?? '') ?>">
                </div>
            </div>
            <br>
            <button type="submit" class="btn btn-primary">Add Song</button>
        </form>
    </div>

    <!-- Song List -->
    <div class="card">
        <h2>Songs (<?= count($songs) ?>)</h2>

        <form method="GET" class="search-bar">
            <input type="text" name="q" placeholder="Search by title, artist, album, genre…"
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if (!empty($_GET['q'])): ?>
                <a href="index.php" class="btn" style="background:#333;color:#aaa">Clear</a>
            <?php endif; ?>
        </form>

        <?php if (empty($songs)): ?>
            <div class="empty">No songs found.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Artist</th>
                        <th>Album</th>
                        <th>Genre</th>
                        <th>Duration</th>
                        <th>Year</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($songs as $i => $song): ?>
                    <tr>
                        <td style="color:#555"><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($song['title']) ?></strong></td>
                        <td><?= htmlspecialchars($song['artist_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($song['album'] ?? '—') ?></td>
                        <td>
                            <?php if ($song['genre']): ?>
                                <span class="badge"><?= htmlspecialchars($song['genre']) ?></span>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td>
                            <?php if ($song['duration_seconds']): ?>
                                <?= sprintf('%d:%02d', intdiv($song['duration_seconds'], 60), $song['duration_seconds'] % 60) ?>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td><?= $song['release_year'] ?? '—' ?></td>
                        <td>
                            <a href="index.php?action=delete&id=<?= $song['id'] ?>"
                               class="btn-danger"
                               onclick="return confirm('Delete \'<?= htmlspecialchars(addslashes($song['title'])) ?>\'?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
