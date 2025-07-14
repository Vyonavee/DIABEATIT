<?php
session_start();
$conn = new mysqli("localhost", "root", "", "diabeatit");
if ($conn->connect_error) die("DB failed");

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM educational_content WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  die("❌ Content not found.");
}

$row = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title><?= htmlspecialchars($row['title']) ?></title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; padding: 40px; }
    .container { background: white; max-width: 800px; margin: auto; padding: 30px; border-radius: 8px; }
    img { max-width: 100%; height: auto; margin-bottom: 20px; border-radius: 6px; }
    h1 { color: #4CAF50; }
    .meta { color: #777; font-size: 14px; margin-bottom: 20px; }
  </style>
</head>
<body>
<div class="container">
  <h1><?= htmlspecialchars($row['title']) ?></h1>
  <div class="meta">
    Category: <?= htmlspecialchars($row['category']) ?> |
    Posted: <?= date("M j, Y", strtotime($row['created_at'])) ?>
  </div>
  <?php if (!empty($row['image_path'])): ?>
    <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="">
  <?php endif; ?>
  <div>
    <?= $row['body'] ?>
  </div>
</div>
</body>
</html>
