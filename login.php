//login


<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "medicine";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check if the email exists
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['user'] = $row['name'];
            $_SESSION['user_id'] = $row['id']; // Store user_id in session
            header("Location: Dashboard.php");
        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}

$conn->close();
?>

















<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="css/login.css">
  <style>
    /* Universal Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Arial', sans-serif;
    }

    body {
      background-image: url('images/loginbackground.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      width: 100vw;
      overflow: hidden;
      color: #fff;
    }

    .full-screen-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
      width: 100%;
      backdrop-filter: blur(10px);
    }

    .login-container {
      background-color: rgba(0, 0, 0, 0.7);
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
      width: 100%;
      max-width: 400px;
      text-align: center;
      transition: all 0.3s ease;
    }

    .login-container:hover {
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.7);
    }

    .login-title {
      font-size: 2rem;
      margin-bottom: 1.5rem;
      color: #f5ba13;
    }

    .form {
      display: flex;
      flex-direction: column;
    }

    .input-group {
      margin-bottom: 1rem;
      position: relative;
    }

    .input-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }

    .input-group input {
      width: 100%;
      padding: 0.75rem;
      border-radius: 5px;
      border: none;
      outline: none;
      font-size: 1rem;
      background-color: rgba(255, 255, 255, 0.1);
      color: #fff;
      transition: background-color 0.3s ease;
    }

    .input-group input:focus {
      background-color: rgba(255, 255, 255, 0.2);
    }

    .input-group.success input {
      border: 2px solid #28a745;
    }

    .input-group.error input {
      border: 2px solid #dc3545;
    }

    .input-group .msg {
      font-size: 0.8rem;
      position: absolute;
      bottom: -20px;
      left: 0;
      color: #f5ba13;
      visibility: hidden;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .input-group.success .msg {
      visibility: visible;
      opacity: 1;
      color: #28a745;
    }

    .input-group.error .msg {
      visibility: visible;
      opacity: 1;
      color: #dc3545;
    }

    .login-button {
      display: inline-block;
      padding: 0.75rem 1.5rem;
      background-color: #f5ba13;
      color: #000;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      font-size: 1rem;
      margin-top: 1rem;
      transition: background-color 0.3s ease;
    }

    .login-button:hover {
      background-color: #e0a800;
    }

    p {
      margin-top: 1rem;
      font-size: 0.9rem;
    }

    p a {
      color: #f5ba13;
      text-decoration: none;
      font-weight: bold;
      transition: color 0.3s ease;
    }

    p a:hover {
      color: #e0a800;
    }
  </style>
</head>
<body>
  <div class="full-screen-container">
    <div class="login-container">
      <h1 class="login-title">Welcome</h1>
      <!-- Updated the form to POST method and removed the anchor from the button -->
      <form method="POST" action="login.php" class="form">
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" required>
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" required>
        </div>

        <!-- Changed this to a submit button -->
        <button type="submit" class="login-button">Log In</button>
        
        <p>Don't have an account? <a href="SignUp.php">Sign Up</a></p>
      </form>
    </div>
  </div>
</body>
</html>
