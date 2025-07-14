<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

$conn = new mysqli("localhost", "root", "", "diabeatit");
if ($conn->connect_error) {
  die("DB connection failed: " . $conn->connect_error);
}

$search = trim($_GET['search'] ?? '');
$calorie_limit = $_GET['calories'] ?? '';
$foods = [];

$query = "SELECT * FROM foods WHERE 1=1";
$params = [];
$types = '';

if ($search !== '') {
  $query .= " AND (name LIKE ? OR local_name LIKE ?)";
  $term = "%$search%";
  $params[] = $term;
  $params[] = $term;
  $types .= 'ss';
}

if ($calorie_limit !== '') {
  $query .= " AND calories_max <= ?";
  $params[] = (int)$calorie_limit;
  $types .= 'i';
}

$stmt = $conn->prepare($query);
if ($params) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $foods[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Food Info – Diabeatit</title>
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      background: #f4f6f9;
      padding: 40px;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .topbar h2 {
      color: #4CAF50;
    }

    .btn-back {
      background: #FF9800;
      color: white;
      border: none;
      padding: 8px 18px;
      border-radius: 20px;
      cursor: pointer;
      font-weight: bold;
    }

    .search-bar {
      margin-bottom: 30px;
    }

    .search-bar input, .search-bar select {
      padding: 10px;
      border-radius: 20px;
      border: 1px solid #ccc;
      margin-right: 10px;
    }

    .btn-submit {
      background: #4CAF50;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 20px;
      cursor: pointer;
    }

    .food-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 20px;
    }

    .food-card {
      background: white;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .food-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 6px;
      margin-bottom: 10px;
    }

    .food-card h4 {
      margin: 5px 0;
      color: #4CAF50;
    }

    .food-card small {
      display: block;
      color: #888;
      margin-bottom: 8px;
    }

    .food-card p {
      font-size: 14px;
      line-height: 1.4;
    }
  </style>
</head>
<body>

  <div class="topbar">
    <h2>🍽️ Food Info</h2>
    <a class="btn-back" href="dashboard.php">← Back to Dashboard</a>
  </div>

  <!-- Search and Filter -->
  <div class="search-bar">
    <form method="GET">
      <input type="text" name="search" placeholder="Search for food..." value="<?= htmlspecialchars($search) ?>" />
      <select name="calories">
        <option value="">-- Max Calories --</option>
        <option value="100" <?= $_GET['calories'] == '100' ? 'selected' : '' ?>>Under 100</option>
        <option value="200" <?= $_GET['calories'] == '200' ? 'selected' : '' ?>>Under 200</option>
        <option value="300" <?= $_GET['calories'] == '300' ? 'selected' : '' ?>>Under 300</option>
        <option value="400" <?= $_GET['calories'] == '400' ? 'selected' : '' ?>>Under 400</option>
        <option value="500" <?= $_GET['calories'] == '500' ? 'selected' : '' ?>>Under 500</option>
        <option value="1000" <?= $_GET['calories'] == '1000' ? 'selected' : '' ?>>Under 1000</option>
      </select>
      <button type="submit" class="btn-submit">Search</button>
    </form>
  </div>

  <!-- Food Results -->
 <div class="food-grid">
  <?php foreach ($foods as $f): ?>
    <div class="food-card">
      <img src="<?= file_exists($f['image_path']) ? $f['image_path'] : 'https://via.placeholder.com/240x150?text=No+Image' ?>" alt="">
      <h4><?= htmlspecialchars($f['name']) ?></h4>
      <?php if ($f['local_name']): ?>
        <small>Local: <?= htmlspecialchars($f['local_name']) ?></small>
      <?php endif; ?>
      <p>
        🍛 <strong>Calories:</strong> <?= $f['calories_min'] ?>–<?= $f['calories_max'] ?> kcal<br>
        🍬 <strong>Sugars:</strong> <?= $f['sugars_min'] ?>–<?= $f['sugars_max'] ?> g<br>
        🍞 <strong>Carbs:</strong> <?= $f['carbs_min'] ?>–<?= $f['carbs_max'] ?> g
      </p>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>
