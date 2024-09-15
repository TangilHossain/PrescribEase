<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user_id =  $_SESSION['user_id'];

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // Update this with your database password
$dbname = "medicine"; // Update this with your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the number of medicines in the medicine_table
$medicines_query = "SELECT COUNT(*) AS medicine_count FROM medicine_table";
$medicines_result = $conn->query($medicines_query);
$medicines_row = $medicines_result->fetch_assoc();
$medicine_count = $medicines_row['medicine_count'];

// Get the number of rows in the prescription table where user_id matches the logged-in user
$prescriptions_query = "SELECT COUNT(*) AS prescription_count FROM prescriptions WHERE user_id='$user_id'";
$prescriptions_result = $conn->query($prescriptions_query);
$prescriptions_row = $prescriptions_result->fetch_assoc();
$prescription_count = $prescriptions_row['prescription_count'];

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="css/Dashboard.css">

    <title>Dashboard</title>
    <style>
        /* Custom styles for the user name display */
        .user-info {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 1rem;
            font-weight: bold;
            color: #f5ba13;
            border: 2px solid #f5ba13;
            padding: 5px 15px;
            border-radius: 25px;
            transition: background-color 0.3s ease;
        }

        .user-info:hover {
            background-color: #f5ba13;
            color: #fff;
        }

        /* Light/Dark Mode Toggle styles */
        .switch-mode {
            width: 50px;
            height: 25px;
            background-color: #ddd;
            border-radius: 25px;
            cursor: pointer;
            position: relative;
            display: inline-block;
        }

        .switch-mode::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background-color: #fff;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        /* Dark mode styling */
        body.dark {
            background-color: #333;
            color: #fff;
        }

        body.dark .switch-mode {
            background-color: #333;
        }

        body.dark .switch-mode::before {
            left: calc(100% - 25px);
        }

        body.dark .user-info {
            color: #fff;
            border-color: #f5ba13;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='logo'><img src="img/logo.png" alt=""></i>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="#">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class='bx bxs-doughnut-chart'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="MakePrescription.php">
                    <i class='bx bxs-shopping-bag-alt'></i>
                    <span class="text">New Prescription</span>
                </a>
            </li>
            <li>
                <a href="MyPatient.php">
                    <i class='bx bxs-message-dots'></i>
                    <span class="text">My Patients</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class='bx bxs-group'></i>
                    <span class="text">Team</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="#">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Settings</span>
                </a>
            </li>
            <li>
                <a href="logout.php" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode" onclick="toggleDarkMode()"></label>
            
            <!-- Display the logged-in user's name -->
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['user']; ?>!</span>
            </div>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Dashboard</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Home</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div>
                <ul class="box-info">
                    <li>
                        <i class='bx bxs-calendar-check'></i>
                        <span class="text">
                            <h3><?php echo $medicine_count; ?></h3>
                            <p>Number of Medicines</p>
                        </span>
                    </li>
                    <li>
                        <i class='bx bxs-group'></i>
                        <span class="text">
                            <h3><?php echo $prescription_count; ?></h3>
                            <p>Total Patients Encountered</p>
                        </span>
                    </li>
                </ul>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
    <script>
        // Toggle Dark Mode
        function toggleDarkMode() {
            document.body.classList.toggle('dark');
        }
    </script>
    <script src="../js/Dashboard.js"></script>
</body>
</html>
