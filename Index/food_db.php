<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: view_foods.php");
  exit();
}

$conn = new mysqli("localhost", "root", "", "diabeatit");
if ($conn->connect_error) {
  die("DB connection failed: " . $conn->connect_error);
}

$flash = "";

// 🔥 Handle Delete
if (isset($_POST['delete_id'])) {
  $id = (int)$_POST['delete_id'];
  $stmt = $conn->prepare("DELETE FROM foods WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->close();
  $flash = "🗑️ Food deleted.";
}

// ✅ Add or Edit Food
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
  $is_edit = isset($_POST['edit_id']);
  $name = trim($_POST['name']);
  $local = trim($_POST['local_name']);
  $cal_min = (int)$_POST['calories_min'];
  $cal_max = (int)$_POST['calories_max'];
  $sugar_min = (float)$_POST['sugars_min'];
  $sugar_max = (float)$_POST['sugars_max'];
  $carb_min = (float)$_POST['carbs_min'];
  $carb_max = (float)$_POST['carbs_max'];

  $image_path = null;
  if (!empty($_FILES['image']['name'])) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image_path = 'images/' . uniqid() . "." . strtolower($ext);
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
  }

  if ($is_edit) {
    $id = (int)$_POST['edit_id'];
    if ($image_path) {
      $stmt = $conn->prepare("UPDATE foods SET name=?, local_name=?, calories_min=?, calories_max=?, sugars_min=?, sugars_max=?, carbs_min=?, carbs_max=?, image_path=? WHERE id=?");
      $stmt->bind_param("ssiiiddssi", $name, $local, $cal_min, $cal_max, $sugar_min, $sugar_max, $carb_min, $carb_max, $image_path, $id);
    } else {
      $stmt = $conn->prepare("UPDATE foods SET name=?, local_name=?, calories_min=?, calories_max=?, sugars_min=?, sugars_max=?, carbs_min=?, carbs_max=? WHERE id=?");
      $stmt->bind_param("ssiiiddssi", $name, $local, $cal_min, $cal_max, $sugar_min, $sugar_max, $carb_min, $carb_max, $id);
    }
    $stmt->execute();
    $stmt->close();
    $flash = "✅ Food updated.";
  } else {
    $stmt = $conn->prepare("INSERT INTO foods (name, local_name, calories_min, calories_max, sugars_min, sugars_max, carbs_min, carbs_max, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiddds", $name, $local, $cal_min, $cal_max, $sugar_min, $sugar_max, $carb_min, $carb_max, $image_path);
    $stmt->execute();
    $stmt->close();
    $flash = "✅ Food added.";
  }
}

// Load all foods
$foods = [];
$result = $conn->query("SELECT * FROM foods ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
  $foods[] = $row;
}
$conn->close();
?>

<!-- 👉 Below this, your HTML layout for displaying, adding, editing, and deleting would go -->

<!DOCTYPE html>
<html>
<head>
  <title>Food Database</title>
  <style>
    body { font-family: sans-serif; padding: 40px; background: #f4f6f9; }
    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .topbar h2 { color: #4CAF50; }
    .btn-back { background: #FF9800; color: white; padding: 8px 16px; border: none; border-radius: 20px; text-decoration: none; }
    .flash { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
    form { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    input, button, label { display: block; width: 100%; margin-bottom: 12px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
    .btn-submit { background: #4CAF50; color: white; font-weight: bold; cursor: pointer; }
    .food-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
    .food-card { background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: relative; }
    .food-card img { width: 100%; height: 150px; object-fit: cover; border-radius: 6px; }
    .food-card h4 { margin: 5px 0; color: #4CAF50; }
    .edit-form { margin-top: 10px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 14px; }
  </style>
</head>
<body>

<div class="topbar">
  <h2>🥗 Food Database (Admin)</h2>
  <a class="btn-back" href="dashboard.php">← Back</a>
</div>

<?php if ($flash): ?>
  <div class="flash"><?= $flash ?></div>
<?php endif; ?>

<!-- Add Food -->
<form method="POST" enctype="multipart/form-data">
  <h3>Add New Food</h3>
  <input type="text" name="name" placeholder="Name (English)" required>
  <input type="text" name="local_name" placeholder="Local Name (optional)">
  <label>Calories Range (min-max)</label>
  <input type="number" name="calories_min" required>
  <input type="number" name="calories_max" required>
  <label>Sugars Range (g)</label>
  <input type="number" step="0.01" name="sugars_min" required>
  <input type="number" step="0.01" name="sugars_max" required>
  <label>Carbs Range (g)</label>
  <input type="number" step="0.01" name="carbs_min" required>
  <input type="number" step="0.01" name="carbs_max" required>
  <label>Image (optional)</label>
  <input type="file" name="image" accept="image/*">
  <button type="submit" class="btn-submit">Add Food</button>
</form>

<!-- Food Grid -->
<div class="food-grid">
  <?php foreach ($foods as $f): ?>
    <div class="food-card">
      <img src="<?= $f['image_path'] && file_exists($f['image_path']) ? $f['image_path'] : 'https://via.placeholder.com/240x150?text=No+Image' ?>" alt="">
      <h4><?= htmlspecialchars($f['name']) ?></h4>
      <?php if ($f['local_name']): ?><small>Local: <?= htmlspecialchars($f['local_name']) ?></small><?php endif; ?>
      <p>
        🍛 Calories: <?= $f['calories_min'] ?>–<?= $f['calories_max'] ?> kcal<br>
        🍬 Sugars: <?= $f['sugars_min'] ?>–<?= $f['sugars_max'] ?> g<br>
        🍞 Carbs: <?= $f['carbs_min'] ?>–<?= $f['carbs_max'] ?> g
      </p>
      <form class="edit-form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" value="<?= $f['id'] ?>">
        <input type="text" name="name" value="<?= htmlspecialchars($f['name']) ?>" required>
        <input type="text" name="local_name" value="<?= htmlspecialchars($f['local_name']) ?>">
        <input type="number" name="calories_min" value="<?= $f['calories_min'] ?>" required>
        <input type="number" name="calories_max" value="<?= $f['calories_max'] ?>" required>
        <input type="number" step="0.01" name="sugars_min" value="<?= $f['sugars_min'] ?>" required>
        <input type="number" step="0.01" name="sugars_max" value="<?= $f['sugars_max'] ?>" required>
        <input type="number" step="0.01" name="carbs_min" value="<?= $f['carbs_min'] ?>" required>
        <input type="number" step="0.01" name="carbs_max" value="<?= $f['carbs_max'] ?>" required>
        <input type="file" name="image" accept="image/*">
        <button type="submit" class="btn-submit">Update</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>

</body>
</html>
