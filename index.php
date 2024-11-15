<?php
// Start session to manage login status
session_start();

// Database connection
$host = 'localhost'; // Your database host
$db = 'junessa'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Registration Logic
if (isset($_POST['signup'])) {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if password and confirm password match
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT); // Hash password

        // Check if the email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already exists!";
        } else {
            // Insert new user into the database
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $email, $password]);
            $success = "Registration Successful!";
        }
    }
}

// Login Logic
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Start session and store user info
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        $_SESSION['email'] = $user['email']; // Store user email

        // Redirect to home page after login
        header("Location: home.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Login and Signup Form</title>
  <link rel="stylesheet" href="index.css">
  <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<section class="container forms">
  <!-- Login Form -->
  <div class="form login">
    <div class="form-content">
      <header>Login</header>
      <form method="POST" action="">
        <div class="field input-field">
          <input type="email" name="email" placeholder="Email" class="input" required>
        </div>
        <div class="field input-field">
          <input type="password" name="password" placeholder="Password" class="password" required>
          <i class='bx bx-hide eye-icon'></i>
        </div>

        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

        <div class="form-link">
          <a href="#" class="forgot-pass">Forgot password?</a>
        </div>

        <div class="field button-field">
          <button type="submit" name="login">Login</button>
        </div>
      </form>

      <div class="form-link">
        <span>Don't have an account? <a href="#" class="link signup-link">Signup</a></span>
      </div>
    </div>
  </div>

  <!-- Signup Form -->
  <div class="form signup">
    <div class="form-content">
      <header>Register</header>
      <form method="POST" action="">
        <div class="field input-field">
          <input type="text" name="first_name" placeholder="First Name" class="input" required>
        </div>
        <div class="field input-field">
          <input type="text" name="last_name" placeholder="Last Name" class="input" required>
        </div>
        <div class="field input-field">
          <input type="email" name="email" placeholder="Email" class="input" required>
        </div>
        <div class="field input-field">
          <input type="password" name="password" placeholder="Create Password" class="password" required>
        </div>
        <div class="field input-field">
          <input type="password" name="confirm_password" placeholder="Confirm Password" class="password" required>
        </div>

        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>
        <?php if (isset($success)) { echo "<div class='success'>$success</div>"; } ?>

        <div class="field button-field">
          <button type="submit" name="signup">Signup</button>
        </div>
      </form>

      <div class="form-link">
        <span>Already have an account? <a href="#" class="link login-link">Login</a></span>
      </div>
    </div>
  </div>
</section>

<script src="index.js"></script>
</body>
</html>
