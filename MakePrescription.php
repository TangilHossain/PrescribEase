<?php
// Start session to get user ID
session_start();
// Include TCPDF Library
require_once('tcpdf.php');

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "medicine";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted with POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Patient Information
  $name = $_POST['name'];
  $age = $_POST['age'];
  $contact = $_POST['contact'];
  $blood_group = $_POST['blood_group'];
  $note = $_POST['note'];

  // Get logged-in user ID from session
  if (!isset($_SESSION['user_id'])) {
    die("User not logged in or user_id not set in session.");
  }
  $user_id = $_SESSION['user_id'];

  // Insert into 'prescriptions' table
  $stmt = $conn->prepare("INSERT INTO prescriptions (user_id, name, age, contact, blood_group, note) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isisss", $user_id, $name, $age, $contact, $blood_group, $note);
  $stmt->execute();
  $prescription_id = $stmt->insert_id;

  // Insert Medicines
  if (!empty($_POST['medicine_name'])) {
    $medicine_names = $_POST['medicine_name'];
    $before_after_meal = $_POST['before_after_meal'];
    $times_a_day = $_POST['times_a_day'];
    $duration_days = $_POST['duration_days'];

    for ($i = 0; $i < count($medicine_names); $i++) {
      $stmt = $conn->prepare("INSERT INTO prescription_medicines (prescription_id, medicine_name, before_after_meal, times_a_day, duration_days) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("issii", $prescription_id, $medicine_names[$i], $before_after_meal[$i], $times_a_day[$i], $duration_days[$i]);
      $stmt->execute();
    }
  }

  // Insert Tests
  if (!empty($_POST['test_name'])) {
    $test_names = $_POST['test_name'];
    $test_notes = $_POST['test_notes'];

    for ($i = 0; $i < count($test_names); $i++) {
      $stmt = $conn->prepare("INSERT INTO prescription_tests (prescription_id, test_name, test_notes) VALUES (?, ?, ?)");
      $stmt->bind_param("iss", $prescription_id, $test_names[$i], $test_notes[$i]);
      $stmt->execute();
    }
  }

  // Generate PDF
  // Fetch Patient Details
  $patient_sql = "SELECT * FROM prescriptions WHERE id = ?";
  $stmt = $conn->prepare($patient_sql);
  $stmt->bind_param("i", $prescription_id);
  $stmt->execute();
  $result_patient = $stmt->get_result();
  $patient_data = $result_patient->fetch_assoc();

  // Fetch Medicines and Tests
  $medicine_sql = "SELECT * FROM prescription_medicines WHERE prescription_id = ?";
  $stmt = $conn->prepare($medicine_sql);
  $stmt->bind_param("i", $prescription_id);
  $stmt->execute();
  $result_medicines = $stmt->get_result();

  $test_sql = "SELECT * FROM prescription_tests WHERE prescription_id = ?";
  $stmt = $conn->prepare($test_sql);
  $stmt->bind_param("i", $prescription_id);
  $stmt->execute();
  $result_tests = $stmt->get_result();

  // Create PDF
  $pdf = new TCPDF();
  $pdf->AddPage();
  $pdf->SetTitle('Doctor\'s Prescription');
  $pdf->SetAuthor('Dr. John Doe');
  $user_id = $_SESSION['user_id'];
  // Generate HTML for PDF
  $html = '<div style="text-align: right;"><strong>User ID: ' . htmlspecialchars($user_id) . '</strong></div>';
  $html .= '<h1>Doctor\'s Prescription</h1>';
  $html .= '<h2>Patient Details</h2>';
  $html .= '<table border="1" cellpadding="4"><tr><th>Name</th><td>' . htmlspecialchars($patient_data['name']) . '</td></tr>';
  $html .= '<tr><th>Age</th><td>' . htmlspecialchars($patient_data['age']) . '</td></tr>';
  $html .= '<tr><th>Contact</th><td>' . htmlspecialchars($patient_data['contact']) . '</td></tr>';
  $html .= '<tr><th>Note</th><td>' . htmlspecialchars($patient_data['note']) . '</td></tr></table>';

  $html .= '<h2>Prescription Details</h2>';
  $html .= '<table border="1" cellpadding="4"><thead><tr><th>Medicine</th><th>Before/After Meal</th><th>Times a day</th><th>Duration</th></tr></thead><tbody>';
  while ($row = $result_medicines->fetch_assoc()) {
    $html .= '<tr><td>' . htmlspecialchars($row['medicine_name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['before_after_meal']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['times_a_day']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['duration_days']) . ' days</td></tr>';
  }
  $html .= '</tbody></table>';

  $html .= '<h2>Tests to be Done</h2>';
  $html .= '<table border="1" cellpadding="4"><thead><tr><th>Test</th><th>Notes</th></tr></thead><tbody>';
  while ($row = $result_tests->fetch_assoc()) {
    $html .= '<tr><td>' . htmlspecialchars($row['test_name']) . '</td><td>' . htmlspecialchars($row['test_notes']) . '</td></tr>';
  }
  $html .= '</tbody></table>';

  // Write HTML to PDF
  $pdf->writeHTML($html, true, false, true, false, '');

  // Output PDF to browser
  $pdf->Output('prescription.pdf', 'I');

  // Close connection
  $stmt->close();
  $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link
    href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="css/MakePrescription.css" />
  <title>New Prescription</title>
</head>

<body>
  <!-- Sidebar Section -->
  <section id="sidebar">
    <a href="#" class="brand">
      <i class="logo"><img src="img/logo.png" alt="Logo" /></i>
    </a>
    <ul class="side-menu top">
      <li>
        <a href="Dashboard.php">
          <i class="bx bxs-shopping-bag-alt"></i>
          <span class="text">Dashboard</span>
        </a>
      </li>
      <li class="active">
        <a href="myprescription.php">
          <i class="bx bxs-dashboard"></i>
          <span class="text">New Prescription</span>
        </a>
      </li>
      <li>
        <a href="MakePrescription.php">
          <i class="bx bxs-doughnut-chart"></i>
          <span class="text">My Prescriptions</span>
        </a>
      </li>
      <li>
        <a href="MyPatient.php">
          <i class="bx bxs-message-dots"></i>
          <span class="text">My Patients</span>
        </a>
      </li>
      <li>
        <a href="#">
          <i class="bx bxs-group"></i>
          <span class="text">Team</span>
        </a>
      </li>
    </ul>
    <ul class="side-menu">
      <li>
        <a href="#">
          <i class="bx bxs-cog"></i>
          <span class="text">Settings</span>
        </a>
      </li>
      <li>
        <a href="logout.php" class="logout">
          <i class="bx bxs-log-out-circle"></i>
          <span class="text">Logout</span>
        </a>
      </li>
    </ul>
  </section>

  <!-- Content Section -->
  <section id="content">
    <nav>

      <!-- <form action="#">
          <div class="form-input">
            <input type="search" placeholder="Medicine Search..." />
            <button type="submit" class="search-btn">
              <i class="bx bx-search"></i>
            </button>
          </div>
        </form> -->
      <input type="checkbox" id="switch-mode" hidden />
      <label for="switch-mode" class="switch-mode"></label>
    </nav>
    <main>
      <div class="head-title">
        <div class="left">
          <h1>New Prescription</h1>
          <ul class="breadcrumb">
            <li>
              <a href="#">New Prescription</a>
            </li>
            <li><i class="bx bx-chevron-right"></i></li>
            <li>
              <a class="active" href="#">Home</a>
            </li>
          </ul>
        </div>
      </div>

      <h1>Search Medicines</h1>
      <form method="GET" action="search.php">
        <input type="text" name="q" placeholder="Search by Brand Name or Generic..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        <button type="submit">Search</button>
      </form>

      <?php
      // Search results
      if (isset($_GET['q'])) {
        $q = htmlspecialchars($_GET['q']);
        $sql = "SELECT * FROM medicines WHERE brand_name LIKE ? OR generic_name LIKE ?";
        $stmt = $conn->prepare($sql);
        $like_q = '%' . $q . '%';
        $stmt->bind_param("ss", $like_q, $like_q);
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<table><thead><tr><th>Brand Name</th><th>Generic Name</th><th>Strength</th></tr></thead><tbody>';
        while ($row = $result->fetch_assoc()) {
          echo '<tr><td>' . htmlspecialchars($row['brand_name']) . '</td>';
          echo '<td>' . htmlspecialchars($row['generic_name']) . '</td>';
          echo '<td>' . htmlspecialchars($row['strength']) . '</td></tr>';
        }
        echo '</tbody></table>';
      } ?>
      <!-- Form Section -->
      <form action="MakePrescription.php" method="POST">
        <h2>Patient Information</h2>
        <div class="patient-information">
          <table>
            <tr>
              <td>Patient Name:</td>
              <td><input type="text" name="name" required /></td>
            </tr>
            <tr>
              <td>Age:</td>
              <td><input type="number" name="age" required /></td>
            </tr>
            <tr>
              <td>Contact:</td>
              <td><input type="text" name="contact" required /></td>
            </tr>
            <tr>
              <td>Blood Group:</td>
              <td>
                <select name="blood_group" required>
                  <option value="A+">A+</option>
                  <option value="A-">A-</option>
                  <option value="B+">B+</option>
                  <option value="B-">B-</option>
                  <option value="AB+">AB+</option>
                  <option value="AB-">AB-</option>
                  <option value="O+">O+</option>
                  <option value="O-">O-</option>
                </select>
              </td>
            </tr>
            <tr>
              <td>Short Note:</td>
              <td><textarea name="note" rows="5"></textarea></td>
            </tr>
          </table>

        </div>

        <h2>About Medicine</h2>
        <table id="medicineTable">
          <tr>
            <th>No.</th>
            <th>Medicine Name</th>
            <th>Before/After Meal</th>
            <th>Times a day</th>
            <th>Dose duration</th>
          </tr>
          <tr>
            <td>1</td>
            <td><input type="text" name="medicine_name[]" required /></td>
            <td>
              <select name="before_after_meal[]">
                <option value="before">Before</option>
                <option value="after">After</option>
              </select>
            </td>
            <td><input type="text" name="times_a_day[]" required /></td>
            <td><input type="number" name="duration_days[]" required /></td>
          </tr>
        </table>
        <button type="button" class="add-button" onclick="addMedicine()">
          Add Medicine
        </button>

        <h2>About Testing</h2>
        <table id="testTable">
          <tr>
            <th>No.</th>
            <th>Testing Name</th>
            <th>Notes</th>
          </tr>
          <tr>
            <td>1</td>
            <td><input type="text" name="test_name[]" required /></td>
            <td><input type="text" name="test_notes[]" required /></td>
          </tr>
        </table>
        <button type="button" class="add-button" onclick="addTest()">
          Add Testing
        </button>

        <button type="submit">Save Prescription</button>
      </form>

    </main>
  </section>

  <style>
    body {
      font-family: "Arial", sans-serif;
      background-color: #f4f4f9;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    main {
      margin-left: 100px;
    }

    form {
      background-color: #fff;
      padding: 10px;
      border-radius: 8px;
      box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
      width: 50%;
      max-width: 600px;
      /* margin-left: -100px; */
    }

    h2 {
      color: #333;
      margin-bottom: 20px;
      font-size: 24px;
      text-align: center;
      border-bottom: 2px solid #5a67d8;
      padding-bottom: 10px;
    }

    .patient-information,
    table {
      width: 100%;
      margin-bottom: 20px;
    }

    table {
      border-collapse: collapse;
    }

    td {
      padding: 10px;
      font-size: 16px;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
      font-size: 16px;
    }

    textarea {
      resize: vertical;
    }

    th {
      background-color: #5a67d8;
      color: #fff;
      padding: 12px;
      text-align: left;
      font-size: 16px;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:nth-child(odd) {
      background-color: #fff;
    }

    button[type="submit"] {
      width: 100%;
      background-color: #5a67d8;
      color: #fff;
      padding: 12px;
      border: none;
      border-radius: 4px;
      font-size: 18px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #434190;
    }

    .add-button {
      background-color: #48bb78;
      color: #fff;
      padding: 10px;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
      display: block;
      width: 100%;
      text-align: center;
    }

    .add-button:hover {
      background-color: #38a169;
    }
  </style>
</body>

<script>
  function addMedicine() {
    const table = document.getElementById("medicineTable");
    const rowCount = table.rows.length;
    const row = table.insertRow(rowCount);

    const cell1 = row.insertCell(0);
    const cell2 = row.insertCell(1);
    const cell3 = row.insertCell(2);
    const cell4 = row.insertCell(3);
    const cell5 = row.insertCell(4);

    cell1.innerHTML = rowCount;
    cell2.innerHTML = '<input type="text" name="medicine_name[]" required>';
    cell3.innerHTML =
      '<select name="before_after_meal[]"><option value="before">Before</option><option value="after">After</option></select>';
    cell4.innerHTML = '<input type="number" name="times_a_day[]" required>';
    cell5.innerHTML = '<input type="number" name="duration_days[]" required>';
  }

  function addTest() {
    const table = document.getElementById("testTable");
    const rowCount = table.rows.length;
    const row = table.insertRow(rowCount);

    const cell1 = row.insertCell(0);
    const cell2 = row.insertCell(1);
    const cell3 = row.insertCell(2);

    cell1.innerHTML = rowCount;
    cell2.innerHTML = '<input type="text" name="test_name[]" required>';
    cell3.innerHTML = '<input type="text" name="test_notes[]" required>';
  }
</script>

</html>