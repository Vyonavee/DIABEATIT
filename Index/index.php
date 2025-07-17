<!-- File: Index/index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome to Diabeatit</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <style>
    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: #f9f9f9;
    }

    header {
      background: #4CAF50;
      padding: 20px 30px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      margin: 0;
      font-size: 26px;
    }

    header button {
      background: #FF9800;
      color: white;
      border: none;
      padding: 10px 16px;
      border-radius: 20px;
      font-weight: bold;
      cursor: pointer;
    }

    .hero {
      height: 400px;
      color: white;
      text-align: center;
      background: url('images/healthy food.jpg') center/cover no-repeat;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .hero h2 {
      font-size: 42px;
      margin-bottom: 10px;
      text-shadow: 0 1px 3px rgba(0,0,0,0.5);
    }

    .hero p {
      font-size: 20px;
      text-shadow: 0 1px 2px rgba(0,0,0,0.4);
    }

    .section {
      padding: 60px 30px;
      text-align: center;
    }

    .section h3 {
      color: #4CAF50;
      margin-bottom: 20px;
    }

    .card-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      max-width: 300px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .card p {
      color: #444;
      font-style: italic;
    }

    .image-banner {
      width: 100%;
      height: 350px;
      background: url('images/Veggiepic.jpg') center/cover no-repeat;
      margin: 60px 0;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: #252525;
      padding: 30px;
      border-radius: 10px;
      color: white;
      width: 320px;
      position: relative;
    }

    .modal-content h4 {
      margin-top: 0;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      color: white;
      font-size: 20px;
      cursor: pointer;
    }

    .input-field {
      width: 100%;
      padding: 10px;
      background: transparent;
      border: none;
      border-left: 2px solid #57AAB4;
      border-bottom: 2px solid #57AAB4;
      color: #fff;
      margin: 10px 0;
    }

    .submit-btn {
      padding: 10px;
      border: none;
      background: #57AAB4;
      color: #fff;
      font-weight: bold;
      cursor: pointer;
      border-radius: 30px;
      width: 100%;
    }

    footer {
      text-align: center;
      padding: 20px;
      background: #eee;
      color: #666;
    }

    @media(max-width: 768px) {
      .hero h2 { font-size: 28px; }
      .hero p { font-size: 16px; }
    }
  </style>
</head>
<body>

<header>
  <h1>🍀 Diabeatit</h1>
  <button onclick="document.getElementById('loginModal').style.display='flex'">Login / Signup</button>
</header>

<div class="hero">
  <h2>Take Charge of Your Diabetes Today</h2>
  <p>Tools for Type 1, Type 2, and Prediabetes in Kenya</p>
</div>

<div class="section">
  <h3>🌟 Why Choose Diabeatit?</h3>
  <div class="card-container">
    <div class="card">
      <p>“Meal planning on a budget has never been easier.”</p>
      <strong>- Jane, Nairobi</strong>
    </div>
    <div class="card">
      <p>“I finally understand what I eat. So detailed!”</p>
      <strong>- Kevin, Kisumu</strong>
    </div>
    <div class="card">
      <p>“I love the weekly tips. They keep me focused!”</p>
      <strong>- Mercy, Mombasa</strong>
    </div>
  </div>
</div>

<div class="image-banner"></div>

<!-- Login Modal -->
<div class="modal" id="loginModal">
  <div class="modal-content">
    <span class="close-btn" onclick="document.getElementById('loginModal').style.display='none'">&times;</span>
    <h4>Login</h4>
    <form action="auth.php" method="POST">
      <input type="hidden" name="action" value="login">
      <input type="text" name="name" class="input-field" placeholder="Username" required>
      <input type="password" name="password" class="input-field" placeholder="Password" required>
      <input type="submit" class="submit-btn" value="Login">
    </form>
    <hr style="margin: 20px 0; border-color: #444">
    <h4>Register</h4>
    <form action="auth.php" method="POST">
      <input type="hidden" name="action" value="register">
      <input type="text" name="name" class="input-field" placeholder="Username" required>
      <input type="email" name="email" class="input-field" placeholder="Email" required>
      <input type="password" name="password" class="input-field" placeholder="Password" required>
      <input type="submit" class="submit-btn" value="Register">
    </form>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> Diabeatit. All rights reserved.
</footer>

<script>
  window.onclick = function(e) {
    const modal = document.getElementById('loginModal');
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  };
</script>

</body>
</html>
