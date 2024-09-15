<?php
// Start the session (if not already started)
session_start();

// Assuming the user is logged in and user ID is stored in the session
$user_id = $_SESSION['user_id']; // Replace 'user_id' with the actual session variable if different

// Database connection
$conn = new mysqli("localhost", "root", "", "medicine");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert or update prescription into the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_prescription'])) {
        $name = $_POST['name'];
        $age = $_POST['age'];
        $contact = $_POST['contact'];
        $blood_group = $_POST['blood_group'];
        $note = $_POST['note'];

        // Insert prescription with the logged-in user's ID
        $sql = "INSERT INTO prescriptions (user_id, name, age, contact, blood_group, note) 
                VALUES ('$user_id', '$name', '$age', '$contact', '$blood_group', '$note')";
        if ($conn->query($sql) === TRUE) {
            echo "New prescription added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['edit_prescription'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $age = $_POST['age'];
        $contact = $_POST['contact'];
        $blood_group = $_POST['blood_group'];
        $note = $_POST['note'];

        // Update prescription only if it belongs to the logged-in user
        $sql = "UPDATE prescriptions 
                SET name='$name', age='$age', contact='$contact', blood_group='$blood_group', note='$note' 
                WHERE id='$id' AND user_id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            echo "Prescription updated successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Delete prescription from the database
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    // Only delete if the prescription belongs to the logged-in user
    $sql = "DELETE FROM prescriptions WHERE id='$id' AND user_id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Prescription deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch prescription data for editing
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $result = $conn->query("SELECT * FROM prescriptions WHERE id='$id' AND user_id='$user_id'");
    $edit_data = $result->fetch_assoc();
}

// Fetch detailed data if details button is clicked
$details_data = null;
if (isset($_GET['details_id'])) {
    $id = $_GET['details_id'];
    $details_query = "
        SELECT p.id, p.name, p.age, p.contact, p.blood_group, p.note,
               pm.medicine_name, pt.test_name
        FROM prescriptions p
        LEFT JOIN prescription_medicines pm ON p.id = pm.prescription_id
        LEFT JOIN prescription_tests pt ON p.id = pt.prescription_id
        WHERE p.id = '$id' AND p.user_id='$user_id'
    ";
    $details_result = $conn->query($details_query);
    $details_data = $details_result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- My CSS -->
    <link rel="stylesheet" href="css/dashboard.css">
    <title>My Patient</title>
    <style>
        /* Table Styling */
        #PatientTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        #PatientTable th,
        #PatientTable td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        #PatientTable th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
        }

        #PatientTable tr:hover {
            background-color: #f1f1f1;
        }

        #PatientTable tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        #PatientTable td button {
            padding: 5px 10px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        #PatientTable td button:hover {
            background-color: #0056b3;
        }

        /* Form Styling */
        form {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        form input[type="text"],
        form input[type="number"],
        form button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        form input[type="text"]:focus,
        form input[type="number"]:focus {
            border-color: #007bff;
            outline: none;
        }

        form button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        /* Details Table Styling */
        #DetailsTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        #DetailsTable th,
        #DetailsTable td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        #DetailsTable th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
        }

        #DetailsTable tr:nth-child(even) {
            background-color: #f9f9f9;
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
            <li><a href="Dashboard.php"><i class='bx bxs-shopping-bag-alt'></i><span class="text">Dashboard</span></a></li>
            <li><a href="MakePrescription.php"><i class='bx bxs-shopping-bag-alt'></i><span class="text">New Prescription</span></a></li>
            <li class="active"><a href="#"><i class='bx bxs-dashboard'></i><span class="text">My Patient</span></a></li>
            <li><a href="#"><i class='bx bxs-doughnut-chart'></i><span class="text">My Patient</span></a></li>
            <li><a href="#"><i class='bx bxs-group'></i><span class="text">Team</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="#"><i class='bx bxs-cog'></i><span class="text">Settings</span></a></li>
            <li><a href="logout.php" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
        </ul>
    </section>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Categories</a>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Patient Name...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Prescriptions</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Prescriptions</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Home</a></li>
                    </ul>
                </div>
            </div>
        </main>
        <!-- MAIN -->

        <table id='PatientTable'>
    <tr>
        <th>Patient Name</th>
        <th>Age</th>
        <th>Contact No.</th>
        <th>Blood Group</th>
        <th>Note</th>
        <th>Action</th>
    </tr>
    <?php
    // Fetch prescriptions for the logged-in user
    $result = $conn->query("SELECT * FROM prescriptions WHERE user_id='$user_id'");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['name']}</td>
            <td>{$row['age']}</td>
            <td>{$row['contact']}</td>
            <td>{$row['blood_group']}</td>
            <td>{$row['note']}</td>
            <td>
                <a href='?edit_id={$row['id']}'><button>Edit</button></a>
                <a href='?delete_id={$row['id']}'><button>Delete</button></a>
                <a href='?details_id={$row['id']}'><button>Details</button></a>
            </td>
        </tr>";
    }
    ?>
</table>

<!-- The rest of the HTML and closing PHP tags remain unchanged -->
<?php
// Close the database connection
$conn->close();
?>

        <!-- Add/Edit Prescription Form -->
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? ''; ?>">
            <input type="text" name="name" placeholder="Name" value="<?php echo $edit_data['name'] ?? ''; ?>" required>
            <input type="number" name="age" placeholder="Age" value="<?php echo $edit_data['age'] ?? ''; ?>" required>
            <input type="text" name="contact" placeholder="Contact" value="<?php echo $edit_data['contact'] ?? ''; ?>" required>
            <input type="text" name="blood_group" placeholder="Blood Group" value="<?php echo $edit_data['blood_group'] ?? ''; ?>" required>
            <textarea name="note" placeholder="Note"><?php echo $edit_data['note'] ?? ''; ?></textarea>
            <button type="submit" name="<?php echo isset($edit_data) ? 'edit_prescription' : 'add_prescription'; ?>">
                <?php echo isset($edit_data) ? 'Update Prescription' : 'Add Prescription'; ?>
            </button>
        </form>

        <!-- Details Table -->
        <?php if ($details_data): ?>
        <table id='DetailsTable'>
            <tr>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Age</th>
                <th>Contact No.</th>
                <th>Blood Group</th>
                <th>Note</th>
                <th>Medicine</th>
                <th>Test</th>
            </tr>
            <?php foreach ($details_data as $detail): ?>
            <tr>
                <td><?php echo $detail['id']; ?></td>
                <td><?php echo $detail['name']; ?></td>
                <td><?php echo $detail['age']; ?></td>
                <td><?php echo $detail['contact']; ?></td>
                <td><?php echo $detail['blood_group']; ?></td>
                <td><?php echo $detail['note']; ?></td>
                <td><?php echo $detail['medicine_name'] ?? ''; ?></td>
                <td><?php echo $detail['test_name'] ?? ''; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>

        <script src="js/MyPatients.js"></script>
        <script src="js/signupScript.js"></script>
    </section>
    <!-- CONTENT -->

</body>
</html>

