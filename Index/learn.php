<?php
session_start();
$conn = new mysqli("localhost", "root", "", "diabeatit");
if ($conn->connect_error) die("DB connection failed");

// Filters
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');

$where = "WHERE 1=1";
if ($search !== '') {
  $searchSafe = $conn->real_escape_string($search);
  $where .= " AND title LIKE '%$searchSafe%'";
}
if ($category !== '') {
  $categorySafe = $conn->real_escape_string($category);
  $where .= " AND category LIKE '%$categorySafe%'";
}

// Queries with error check
$recent = $conn->query("SELECT * FROM educational_content ORDER BY created_at DESC LIMIT 4") ?: [];
$tips = $conn->query("SELECT * FROM educational_content WHERE content_type = 'tip' ORDER BY created_at DESC LIMIT 1") ?: false;
$articles = $conn->query("SELECT * FROM educational_content WHERE content_type = 'article' $where ORDER BY created_at DESC") ?: false;
$videos = $conn->query("SELECT * FROM educational_content WHERE content_type = 'video' $where ORDER BY created_at DESC") ?: false;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Learn – Diabeatit</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background: #f4f6f9; padding: 40px; }
    h2, h3 { color: #4CAF50; }
    .section { margin-bottom: 40px; }
    .card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
    .card { background: white; border-radius: 10px; padding: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); text-decoration: none; color: inherit; }
    .card img { width: 100%; height: 140px; object-fit: cover; border-radius: 5px; margin-bottom: 10px; }
    .video { width: 100%; height: 160px; border: none; border-radius: 6px; margin-bottom: 10px; }
    .tip-box { background: #E3F2FD; padding: 20px; border-radius: 8px; font-size: 18px; color: #0D47A1; }
    .filter { margin-bottom: 30px; }
    .filter input, .filter select { padding: 10px; border-radius: 5px; border: 1px solid #ccc; margin-right: 10px; }
    .filter button { background: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
  </style>
</head>
<body>

<div class="section">
  <h3>🌟 Weekly Tip</h3>
  <div class="tip-box">
    <?php if ($tips && $tips->num_rows > 0): ?>
      <?= nl2br(htmlspecialchars($tips->fetch_assoc()['tip_text'])) ?>
    <?php else: ?>
     WANTAM
    <?php endif; ?>
  </div>
</div>
<h2>📘 Learn with Diabeatit</h2>

<!-- Filter -->
<form method="GET" class="filter">
  <input type="text" name="search" placeholder="Search title..." value="<?= htmlspecialchars($search) ?>">
  <select name="category">
    <option value="">All Categories</option>
    <option value="nutrition" <?= $category == 'nutrition' ? 'selected' : '' ?>>Nutrition</option>
    <option value="exercise" <?= $category == 'exercise' ? 'selected' : '' ?>>Exercise</option>
    <option value="mindset" <?= $category == 'mindset' ? 'selected' : '' ?>>Mindset</option>
    <option value="lifestyle" <?= $category == 'lifestyle' ? 'selected' : '' ?>>Lifestyle</option>
    <option value="pinned" <?= $category == 'pinned' ? 'selected' : '' ?>>📌 Pinned</option>
  </select>
  <button type="submit">Apply</button>
</form>

<!-- Weekly Tip -->


<!-- Recently Uploaded -->
<div class="section">
  <h3>🆕 Recently Uploaded</h3>
  <div class="card-grid">
    <?php if ($recent instanceof mysqli_result): ?>
      <?php while ($r = $recent->fetch_assoc()): ?>
        <a href="learn_view.php?id=<?= $r['id'] ?>" class="card">
          <?php if (!empty($r['image_path'])): ?>
            <img src="<?= htmlspecialchars($r['image_path']) ?>" alt="">
          <?php endif; ?>
          <strong><?= htmlspecialchars($r['title']) ?></strong><br>
          <small><?= htmlspecialchars($r['category']) ?></small>
          <p><?= date("M j, Y", strtotime($r['created_at'])) ?></p>
        </a>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Articles -->
<div class="section">
  <h3>📖 Articles</h3>
  <div class="card-grid">
    <?php if ($articles && $articles instanceof mysqli_result): ?>
      <?php while ($a = $articles->fetch_assoc()): ?>
        <a href="learn_view.php?id=<?= $a['id'] ?>" class="card">
          <?php if (!empty($a['image_path'])): ?>
            <img src="<?= htmlspecialchars($a['image_path']) ?>" alt="">
          <?php endif; ?>
          <strong><?= htmlspecialchars($a['title']) ?></strong>
          <p><?= substr(strip_tags($a['body']), 0, 100) ?>...</p>
        </a>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No articles found.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Videos -->
<div class="section">
  <h3>🎥 Videos</h3>
  <div class="card-grid">
    <?php if ($videos && $videos instanceof mysqli_result): ?>
      <?php while ($v = $videos->fetch_assoc()): ?>
        <div class="card">
          <iframe class="video" src="<?= htmlspecialchars($v['link']) ?>" allowfullscreen></iframe>
          <strong><?= htmlspecialchars($v['title']) ?></strong><br>
          <small><?= htmlspecialchars($v['category']) ?></small>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No videos available.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
