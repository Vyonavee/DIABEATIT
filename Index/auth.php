<?php
// File: auth.php
session_start();

// Database config
$servername = "localhost";
$username = "root";
$password = "";
$database = "diabeatit";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Get POST values
$action = $_POST['action'] ?? '';
$name = trim($_POST['name'] ?? '');
$password_input = $_POST['password'] ?? '';
$email = trim($_POST['email'] ?? ''); // only used for registration

// === REGISTER FLOW ===
if ($action === "register") {
    if (empty($name) || empty($email) || empty($password_input)) {
        $_SESSION['flash'] = "⚠️ Please fill all fields.";
        header("Location: login.html");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['flash'] = "⚠️ Email already registered.";
        $stmt->close();
        header("Location: login.html");
        exit();
    }

    $stmt->close();

    // Register new user with default role = 'user'
    $hashed = password_hash($password_input, PASSWORD_DEFAULT);
    $default_role = 'user';

    $insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssss", $name, $email, $hashed, $default_role);

    if ($insert->execute()) {
        $_SESSION['flash'] = "✅ Registration successful. You can now log in.";
    } else {
        $_SESSION['flash'] = "❌ Registration failed.";
    }

    $insert->close();
    header("Location: login.html");
    exit();
}

// === LOGIN FLOW ===
elseif ($action === "login") {
    if (empty($name) || empty($password_input)) {
        $_SESSION['flash'] = "⚠️ Please enter username and password.";
        header("Location: login.html");
        exit();
    }

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password_input, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['flash'] = "✅ Login successful. Welcome, {$user['name']}!";
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['flash'] = "❌ Incorrect password.";
            header("Location: login.html");
            exit();
        }
    } else {
        $_SESSION['flash'] = "❌ User not found.";
        header("Location: login.html");
        exit();
    }

    $stmt->close();
}

// === Invalid Action ===
else {
    $_SESSION['flash'] = "❌ Invalid action.";
    header("Location: login.html");
    exit();
}

$conn->close();
?>
