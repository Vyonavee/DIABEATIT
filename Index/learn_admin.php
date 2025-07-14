<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: learn.php");
  exit();
}
$conn = new mysqli("localhost", "root", "", "diabeatit");
if ($conn->connect_error) die("DB failed");

$flash = "";

// Delete
if (isset($_GET['delete'])) {
  $id = (int)$_GET['delete'];
  $conn->query("DELETE FROM educational_content WHERE id = $id");
  $flash = "🗑️ Content deleted.";
}

// Pin
if (isset($_GET['pin'])) {
  $conn->query("UPDATE educational_content SET category = REPLACE(category, 'pinned', '')");
  $id = (int)$_GET['pin'];
  $conn->query("UPDATE educational_content SET category = CONCAT(category, ',pinned') WHERE id = $id");
  $flash = "📌 Pinned!";
}

// Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $type = $_POST['content_type'] ?? '';
  $body = $_POST['body'] ?? '';
  $tip_text = $_POST['tip_text'] ?? '';
  $video_url = $_POST['video_url'] ?? '';
  $category = implode(',', $_POST['category'] ?? []);
  $image_path = '';

  if (!empty($_FILES['image']['name'])) {
    if ($_FILES['image']['size'] > 3 * 1024 * 1024) {
      $flash = "⚠️ Image too large (max 3MB)";
    } else {
      $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $image_path = 'images/' . uniqid() . "." . strtolower($ext);
      move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }
  }

  if (!$flash && $title && $type) {
    $stmt = $conn->prepare("INSERT INTO educational_content (title, body, image_path, content_type, link, tip_text, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $title, $body, $image_path, $type, $video_url, $tip_text, $category);
    $stmt->execute();
    $stmt->close();
    $flash = "✅ Uploaded successfully.";
  }
}

$all = $conn->query("SELECT * FROM educational_content ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Learn Upload</title>
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
  <style>
    body { font-family: Inter, sans-serif; padding: 30px; background: #f9f9f9; }
    .form { max-width: 700px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
    input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; }
    button { background: #4CAF50; color: white; border: none; padding: 10px 20px; margin-top: 10px; cursor: pointer; }
    .flash { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    .list { max-width: 700px; margin: 40px auto; }
    .item { background: #fff; padding: 15px; border-radius: 6px; margin-bottom: 10px; }
    .actions a { margin-right: 10px; color: #FF5722; text-decoration: none; }
  </style>
</head>
<body>

<h2>🧠 Learn Admin Panel</h2>

<?php if ($flash): ?>
  <div class="flash"><?= $flash ?></div>
<?php endif; ?>

<div class="form">
  <form method="POST" enctype="multipart/form-data">
    <label>Title</label>
    <input name="title" required>

    <label>Type</label>
    <select name="content_type" onchange="toggleType(this.value)">
      <option value="article">Article</option>
      <option value="video">Video</option>
      <option value="tip">Tip</option>
    </select>

    <div id="article">
      <label>Body</label>
      <textarea name="body" id="ckeditor"></textarea>

      <label>Image (max 3MB)</label>
      <input type="file" name="image" accept="image/*">
    </div>

    <div id="video" style="display:none;">
      <label>Video Link</label>
      <input name="video_url">
    </div>

    <div id="tip" style="display:none;">
      <label>Tip Text</label>
      <textarea name="tip_text" rows="4"></textarea>
    </div>

    <label>Categories (select multiple)</label>
    <select name="category[]" multiple>
      <option value="NUTRITION">Nutrition</option>
      <option value="EXERCISE">Exercise</option>
      <option value="MINDSET">Mindset</option>
      <option value="LIFESTYLE">Lifestyle</option>
      <option value="Pinned">📌 Pinned</option>
    </select>

    <button type="submit">Upload</button>
  </form>
</div>

<div class="list">
  <h3>📚 All Content</h3>
  <?php while ($c = $all->fetch_assoc()): ?>
    <div class="item">
      <strong><?= $c['title'] ?></strong> (<?= $c['content_type'] ?>)
      <div class="actions">
        <a href="learn_view.php?id=<?= $c['id'] ?>" target="_blank">View</a>
        <a href="learn_edit.php?id=<?= $c['id'] ?>">✏️ Edit</a>

        <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('Delete this content?')">Delete</a>
        <a href="?pin=<?= $c['id'] ?>" onclick="return confirm('Pin this content?')">📌 Pin</a>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<script>
  function toggleType(type) {
    document.getElementById("article").style.display = type === 'article' ? 'block' : 'none';
    document.getElementById("video").style.display = type === 'video' ? 'block' : 'none';
    document.getElementById("tip").style.display = type === 'tip' ? 'block' : 'none';
  }

  document.addEventListener("DOMContentLoaded", function () {
    ClassicEditor
      .create(document.querySelector('#ckeditor'))
      .catch(error => console.error(error));
  });
</script>

</body>
</html>
