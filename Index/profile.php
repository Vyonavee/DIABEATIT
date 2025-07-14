<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

$conn = new mysqli("localhost", "root", "", "diabeatit");
if ($conn->connect_error) {
  die("DB error: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$flash = '';

// Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $type = $_POST['diabetes_type'] ?? '';
  if (in_array($type, ['prediabetes', 'type1', 'type2'])) {
    $stmt = $conn->prepare("UPDATE users SET diabetes_type = ? WHERE id = ?");
    $stmt->bind_param("si", $type, $user_id);
    if ($stmt->execute()) {
      $flash = "✅ Diabetes type updated successfully.";
    } else {
      $flash = "❌ Update failed.";
    }
    $stmt->close();
  }
}

// Fetch current value
$result = $conn->query("SELECT name, diabetes_type FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Profile – Diabeatit</title>
  <style>
    body {
      font-family: sans-serif;
      padding: 40px;
      background: #f4f6f9;
    }
    .box {
      max-width: 500px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
    }
    select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
    }
    .btn {
      padding: 10px 20px;
      background: #4CAF50;
      color: white;
      border: none;
      border-radius: 20px;
      cursor: pointer;
    }
    .flash {
      background: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>👤 Edit Profile: <?= htmlspecialchars($user['name']) ?></h2>
    
    <?php if ($flash): ?>
      <div class="flash"><?= $flash ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Diabetes Type:</label>
      <select name="diabetes_type" required>
        <option value="prediabetes" <?= $user['diabetes_type'] === 'prediabetes' ? 'selected' : '' ?>>Prediabetes</option>
        <option value="type1" <?= $user['diabetes_type'] === 'type1' ? 'selected' : '' ?>>Type 1</option>
        <option value="type2" <?= $user['diabetes_type'] === 'type2' ? 'selected' : '' ?>>Type 2</option>
      </select>
      <button type="submit" class="btn">Save Changes</button>
    </form>
  </div>
</body>
</html>
