<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}
$name = $_SESSION['name'] ?? 'User';
$role = $_SESSION['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Diabeatit Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background-color: #f9f9f9;
    }

    .topbar {
      background-color: #4CAF50;
      color: white;
      padding: 15px 20px;
      font-size: 20px;
      font-weight: bold;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .topbar a {
      color: white;
      text-decoration: none;
      font-size: 14px;
      padding: 8px 14px;
      background: #ff5722;
      border-radius: 20px;
    }

    .container {
      display: flex;
      height: 100vh;
    }

    .sidebar {
      width: 230px;
      background-color: #fff;
      border-right: 1px solid #e0e0e0;
      padding-top: 20px;
    }

    .sidebar a {
      display: block;
      padding: 12px 20px;
      color: #333;
      text-decoration: none;
      font-weight: 500;
      border-left: 4px solid transparent;
      transition: 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #eaf5ea;
      border-left: 4px solid #4CAF50;
      color: #4CAF50;
    }

    .content {
      flex: 1;
      padding: 30px;
      background-color: #f4f6f9;
    }

    .card {
      background: #ffffff;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .highlight {
      color: #FF9800;
      font-weight: bold;
    }

    .btn-action {
      background-color: #FFEB3B;
      color: #333;
      padding: 8px 16px;
      border: none;
      border-radius: 20px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn-action:hover {
      background-color: #FDD835;
    }
  </style>
</head>
<body>

  <div class="topbar">
    🍀 Diabeatit Dashboard
    <a href="logout.php">Logout</a>
  </div>

  <div class="container">
    <div class="sidebar">
   <a href="profile.php" class="active">👤 Profile</a>
   <a href="learn.php" class="<?= basename($_SERVER['PHP_SELF']) === 'learn.php' ? 'active' : '' ?>">📚 Learn</a>
   <a href="#">🍽️ Meal Planner</a>
   <?php if ($role === 'admin'): ?>
     <a href="view_foods.php">📊 Food Info (Preview)</a>
     <a href="food_db.php">🥗 Food Database</a>
     <a href="learn_admin.php">📤 Upload Content</a> <!-- ✅ New Upload Link -->
   <?php else: ?>
     <a href="view_foods.php">🥗 Food Info</a>
   <?php endif; ?>
   <a href="#">🎯 Goals & Progress</a>
   <a href="#">⚙️ Settings</a>
</div>


   
 

     

    <div class="content">
      <?php if (isset($_SESSION['flash'])): ?>
        <div id="flash-message" style="background: #d4edda; color: #155724; padding: 10px 20px; margin-bottom: 15px; border-radius: 5px;">
          <?= $_SESSION['flash']; unset($_SESSION['flash']); ?>
        </div>
      <?php endif; ?>

      <div class="card">
        <h2>Welcome back, <span class="highlight"><?= htmlspecialchars($name) ?></span> 👋</h2>
        <p>Your dashboard is ready ma'am. Plan meals, track progress,update content and stay informed too!!.</p>
        <button class="btn-action">Plan My Day</button>
      </div>

      <div class="card">
        <h3>Today's Quick Stats</h3>
        <ul>
          <li>✔️ Breakfast logged</li>
          <li>💧 Water: 3/8 glasses</li>
          <li>🎯 Goal: Walk 5000 steps — <strong>In progress</strong></li>
        </ul>
      </div>
    </div>
  </div>

  <script>
    const flash = document.getElementById('flash-message');
    if (flash) {
      setTimeout(() => {
        flash.style.transition = 'opacity 0.8s ease';
        flash.style.opacity = '0';
        setTimeout(() => flash.remove(), 800);
      }, 3500);
    }
  </script>

</body>
</html>
