<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

$name = $_SESSION['name'] ?? 'User';
$role = $_SESSION['role'] ?? 'user';
$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "diabeatit"); // adjust credentials
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Delete meal if requested
if (isset($_GET['delete'])) {
  $delete_id = intval($_GET['delete']);
  $delete_query = ($role === 'admin') ?
    "DELETE FROM meal_plans WHERE id = $delete_id" :
    "DELETE FROM meal_plans WHERE id = $delete_id AND user_id = $user_id";
  $conn->query($delete_query);
  $_SESSION['flash'] = "Meal deleted!";
  header("Location: meal_planner.php");
  exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $meal_date = $_POST['meal_date'];
  $meal_type = $_POST['meal_type'];
  $food_id = $_POST['food_id'];
  $quantity = floatval($_POST['quantity']);

  $stmt = $conn->prepare("INSERT INTO meal_plans (user_id, meal_date, meal_type, food_id, quantity) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issid", $user_id, $meal_date, $meal_type, $food_id, $quantity);
  $stmt->execute();
  $_SESSION['flash'] = "Meal added successfully!";
  header("Location: meal_planner.php");
  exit();
}

// Get food options
$foods = $conn->query("SELECT id, name FROM foods ORDER BY name");

// Get meals to display
$query = "
  SELECT m.id, m.meal_date, m.meal_type, m.quantity,
         f.name AS food_name, f.calories_min, f.calories_max,
         f.sugars_min, f.sugars_max, f.carbs_min, f.carbs_max,
         u.name AS username
  FROM meal_plans m
  JOIN foods f ON m.food_id = f.id
  JOIN users u ON m.user_id = u.id
";

if ($role !== 'admin') {
  $query .= " WHERE m.user_id = $user_id";
}

$query .= " ORDER BY m.meal_date DESC, m.meal_type";
$meals = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Meal Planner | Diabeatit</title>
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      background-color: #f4f6f9;
      margin: 0;
    }

    .topbar {
      background-color: #4CAF50;
      color: white;
      padding: 15px 20px;
      font-size: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .topbar a {
      color: white;
      background: #ff5722;
      padding: 8px 14px;
      text-decoration: none;
      border-radius: 20px;
    }

    .container {
      padding: 30px;
    }

    .card {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .highlight { color: #FF9800; font-weight: bold; }

    .btn {
      background-color: #FFEB3B;
      color: #333;
      padding: 10px 20px;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      font-weight: bold;
    }

    .btn:hover {
      background-color: #FDD835;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #eaf5ea;
    }

    .flash {
      background: #d4edda;
      color: #155724;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 5px;
    }

    .delete-link {
      color: red;
      font-weight: bold;
      text-decoration: none;
    }

    .totals {
      margin-top: 10px;
      font-weight: bold;
      color: #4CAF50;
    }
  </style>
</head>
<body>

<div class="topbar">
  🍽️ Meal Planner
  <a href="dashboard.php">← Back to Dashboard</a>
</div>

<div class="container">
  <?php if (isset($_SESSION['flash'])): ?>
    <div class="flash"><?= $_SESSION['flash']; unset($_SESSION['flash']); ?></div>
  <?php endif; ?>

  <div class="card">
    <h2>Hello <span class="highlight"><?= htmlspecialchars($name) ?></span>! Plan your meals below 🌿</h2>
    <form method="POST">
      <label>Date:</label><br/>
      <input type="date" name="meal_date" required><br/><br/>

      <label>Meal Type:</label><br/>
      <select name="meal_type" required>
        <option value="breakfast">Breakfast</option>
        <option value="lunch">Lunch</option>
        <option value="dinner">Dinner</option>
      </select><br/><br/>

      <label>Food:</label><br/>
      <select name="food_id" required>
        <?php while ($f = $foods->fetch_assoc()): ?>
          <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
        <?php endwhile; ?>
      </select><br/><br/>

      <label>Quantity (grams):</label><br/>
      <input type="number" name="quantity" step="0.1" required><br/><br/>

      <button class="btn" type="submit">➕ Add Meal</button>
    </form>
  </div>

  <div class="card">
    <h3>📋 Your Meals</h3>
    <table>
      <tr>
        <?php if ($role === 'admin') echo "<th>User</th>"; ?>
        <th>Date</th>
        <th>Meal</th>
        <th>Food</th>
        <th>Quantity (g)</th>
        <th>Calories (est)</th>
        <th>Carbs (g)</th>
        <th>Sugars (g)</th>
        <th>Action</th>
      </tr>
      <?php
        $total_cal = $total_carbs = $total_sugar = 0;
        while ($row = $meals->fetch_assoc()):
          $calories = ($row['calories_min'] + $row['calories_max']) / 2;
          $carbs = ($row['carbs_min'] + $row['carbs_max']) / 2;
          $sugars = ($row['sugars_min'] + $row['sugars_max']) / 2;

          $total_cal += $calories * ($row['quantity'] / 100);
          $total_carbs += $carbs * ($row['quantity'] / 100);
          $total_sugar += $sugars * ($row['quantity'] / 100);
      ?>
        <tr>
          <?php if ($role === 'admin') echo "<td>{$row['username']}</td>"; ?>
          <td><?= $row['meal_date'] ?></td>
          <td><?= ucfirst($row['meal_type']) ?></td>
          <td><?= htmlspecialchars($row['food_name']) ?></td>
          <td><?= $row['quantity'] ?></td>
          <td><?= round($calories * ($row['quantity'] / 100), 1) ?></td>
          <td><?= round($carbs * ($row['quantity'] / 100), 1) ?></td>
          <td><?= round($sugars * ($row['quantity'] / 100), 1) ?></td>
          <td><a class="delete-link" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this meal?')">🗑 Delete</a></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <div class="totals">
      🔢 Daily Totals: <?= round($total_cal) ?> kcal |
      <?= round($total_carbs, 1) ?>g carbs |
      <?= round($total_sugar, 1) ?>g sugars
    </div>
  </div>
</div>

</body>
</html>
