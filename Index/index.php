<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome to Diabeatit</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: "Segoe UI", sans-serif;
      background: #f9f9f9;
      color: #333;
    }

    header {
      background: #4CAF50;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
    }

    header .btns {
      display: flex;
      gap: 10px;
    }

    .btns button {
      background: #FF9800;
      border: none;
      padding: 10px 16px;
      color: white;
      border-radius: 20px;
      font-weight: bold;
      cursor: pointer;
    }

    .hero {
      height: 100vh;
      background: url('images/healthy food.jpg') center center/cover no-repeat;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      padding: 0 20px;
    }

    .hero h2 {
      font-size: 48px;
      text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
    }

    .hero p {
      font-size: 22px;
      margin-top: 10px;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
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
      padding: 20px;
      max-width: 280px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .card p {
      font-style: italic;
      margin-bottom: 8px;
    }

    .image-feature {
      background: url('images/balanced diet.jpg') center center/cover no-repeat;
      height: 450px;
      margin: 60px 0;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .image-feature h2 {
      background: rgba(0,0,0,0.5);
      color: white;
      padding: 20px 30px;
      border-radius: 10px;
      font-size: 36px;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
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

    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 20px;
      cursor: pointer;
      color: white;
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
      border-radius: 30px;
      width: 100%;
      cursor: pointer;
    }

    footer {
      background: #eee;
      padding: 20px;
      text-align: center;
      color: #666;
    }

    @media (max-width: 768px) {
      .hero h2 { font-size: 32px; }
      .hero p { font-size: 18px; }
    }
  </style>
</head>
<body>

<header>
  <h1>🍀 Diabeatit</h1>
  <div class="btns">
    <button onclick="document.getElementById('loginModal').style.display='flex'">Login / Signup</button>
    <button onclick="document.getElementById('contact').scrollIntoView({behavior: 'smooth'})">Contact Us</button>
  </div>
</header>

<section class="hero">
  <h2>Take Charge of Your Diabetes Today</h2>
  <p>Personalized support for Type 1, Type 2 & Prediabetes in Kenya</p>
</section>

<section class="section">
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
</section>

<section class="image-feature">
  <h2>Eat Better. Feel Better. Live Better.</h2>
</section>

<section class="section" id="contact">
  <h3>📞 Contact Us</h3>
  <p>Email: support@diabeatit.co.ke<br>Phone: +254 712 345 678</p>
</section>

<footer>
  &copy; <?= date('Y') ?> Diabeatit. All rights reserved.
</footer>

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
