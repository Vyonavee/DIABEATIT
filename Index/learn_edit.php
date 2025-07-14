<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: learn.php");
  exit();
}

$conn = new mysqli("localhost", "root", "", "diabeatit");
if ($conn->connect_error) die("DB failed");

$id = (int)($_GET['id'] ?? 0);
$flash = "";

// Fetch existing entry
$stmt = $conn->prepare("SELECT * FROM educational_content WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  die("❌ Content not found.");
}
$content = $result->fetch_assoc();
$stmt->close();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title']);
  $type = $_POST['content_type'];
  $body = $_POST['body'] ?? '';
  $tip_text = $_POST['tip_text'] ?? '';
  $video_url = $_POST['video_url'] ?? '';
  $category = implode(',', $_POST['category'] ?? []);
  $image_path = $content['image_path'];

  if (!empty($_FILES['image']['name'])) {
    if ($_FILES['image']['size'] > 3 * 1024 * 1024) {
      $flash = "⚠️ Image too large (max 3MB)";
    } else {
      $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $image_path = 'images/' . uniqid() . "." . strtolower($ext);
      move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }
  }

  if (!$flash) {
    $stmt = $conn->prepare("UPDATE educational_content SET title=?, body=?, image_path=?, content_type=?, link=?, tip_text=?, category=? WHERE id=?");
    $stmt->bind_param("sssssssi", $title, $body, $image_path, $type, $video_url, $tip_text, $category, $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['flash'] = "✅ Content updated.";
    header("Location: learn_admin.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Content</title>
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
  <style>
    body { font-family: Inter, sans-serif; padding: 30px; background: #f9f9f9; }
    .form { max-width: 700px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
    input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; }
    button { background: #4CAF50; color: white; border: none; padding: 10px 20px; margin-top: 10px; cursor: pointer; }
    .flash { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
  </style>
</head>
<body>

<h2>✏️ Edit Content</h2>

<?php if ($flash): ?>
  <div class="flash"><?= $flash ?></div>
<?php endif; ?>

<div class="form">
  <form method="POST" enctype="multipart/form-data">
    <label>Title</label>
    <input name="title" value="<?= htmlspecialchars($content['title']) ?>" required>

    <label>Type</label>
    <select name="content_type" onchange="toggleType(this.value)">
      <option value="article" <?= $content['content_type'] === 'article' ? 'selected' : '' ?>>Article</option>
      <option value="video" <?= $content['content_type'] === 'video' ? 'selected' : '' ?>>Video</option>
      <option value="tip" <?= $content['content_type'] === 'tip' ? 'selected' : '' ?>>Tip</option>
    </select>

    <div id="article">
      <label>Body</label>
      <textarea name="body" id="ckeditor"><?= htmlspecialchars($content['body']) ?></textarea>

      <?php if (!empty($content['image_path'])): ?>
        <p>Current image:</p>
        <img src="<?= $content['image_path'] ?>" width="150">
      <?php endif; ?>

      <label>Replace Image (optional)</label>
      <input type="file" name="image" accept="image/*">
    </div>

    <div id="video" style="display:none;">
      <label>Video Link</label>
      <input name="video_url" value="<?= htmlspecialchars($content['link']) ?>">
    </div>

    <div id="tip" style="display:none;">
      <label>Tip Text</label>
      <textarea name="tip_text" rows="4"><?= htmlspecialchars($content['tip_text']) ?></textarea>
    </div>

    <label>Categories (select multiple)</label>
    <select name="category[]" multiple>
      <?php
      $selected = explode(',', $content['category']);
      $options = ['NUTRITION','exercise','mindset','lifestyle','pinned'];
      foreach ($options as $opt) {
        $isSelected = in_array($opt, $selected) ? 'selected' : '';
        echo "<option value='$opt' $isSelected>$opt</option>";
      }
      ?>
    </select>

    <button type="submit">Update</button>
  </form>
</div>

<script>
  function toggleType(type) {
    document.getElementById("article").style.display = type === 'article' ? 'block' : 'none';
    document.getElementById("video").style.display = type === 'video' ? 'block' : 'none';
    document.getElementById("tip").style.display = type === 'tip' ? 'block' : 'none';
  }

  document.addEventListener("DOMContentLoaded", function () {
    toggleType("<?= $content['content_type'] ?>");
    ClassicEditor
      .create(document.querySelector('#ckeditor'))
      .catch(error => console.error(error));
  });
</script>

</body>
</html>
