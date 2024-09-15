
//signup



<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Update with your MySQL root password
$dbname = "medicine"; // Update if your database name is different

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $qualification = mysqli_real_escape_string($conn, $_POST['qualification']); // New field

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if the email already exists
        $checkEmail = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($checkEmail);

        if ($result->num_rows > 0) {
            echo "<script>alert('Email already exists!');</script>";
        } else {
            // Insert new user into database
            $sql = "INSERT INTO users (name, email, password, mobile, qualification) 
                    VALUES ('$name', '$email', '$hashed_password', '$mobile', '$qualification')";
            
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Registration successful! Please log in.'); window.location.href = 'login.php';</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

$conn->close();
?>