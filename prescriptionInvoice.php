<?php
// Include TCPDF Library
session_start();
$user_id = $_SESSION['user_id'];
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

// Assuming you have the patient ID from the last inserted record
$patient_id = $_POST['patient_id'];

// Fetch Patient Details
$patient_sql = "SELECT * FROM prescriptions WHERE id = ?";
$stmt = $conn->prepare($patient_sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result_patient = $stmt->get_result();
$patient_data = $result_patient->fetch_assoc();

// Fetch Medicines
$medicine_sql = "SELECT * FROM prescription_medicines WHERE prescription_id = ?";
$stmt = $conn->prepare($medicine_sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result_medicines = $stmt->get_result();

// Fetch Tests
$test_sql = "SELECT * FROM prescription_tests WHERE prescription_id = ?";
$stmt = $conn->prepare($test_sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result_tests = $stmt->get_result();

// Create new PDF document
$pdf = new TCPDF();
$pdf->AddPage();

// Set document information
$pdf->SetTitle('Doctor\'s Prescription');
$pdf->SetAuthor('Dr. John Doe');

// Add User ID to the top right corner
$pdf->SetFont('helvetica', 'I', 12);
$pdf->Cell(0, 10, 'User ID: ' . $user_id, 0, 1, 'R');

// Generate HTML for PDF
$html = '
<h1>Doctor\'s Prescription</h1>
<h2>Patient Details</h2>
<table border="1" cellpadding="4">
    <tr><th>Name</th><td>' . htmlspecialchars($patient_data['name']) . '</td></tr>
    <tr><th>Age</th><td>' . htmlspecialchars($patient_data['age']) . '</td></tr>
    <tr><th>Contact</th><td>' . htmlspecialchars($patient_data['contact']) . '</td></tr>
    <tr><th>Note</th><td>' . htmlspecialchars($patient_data['note']) . '</td></tr>
</table>

<h2>Prescription Details</h2>
<table border="1" cellpadding="4">
    <thead>
        <tr>
            <th>Medicine</th>
            <th>Before/After Meal</th>
            <th>Times a day</th>
            <th>Duration</th>
        </tr>
    </thead>
    <tbody>';

while ($row = $result_medicines->fetch_assoc()) {
    $html .= '<tr>
        <td>' . htmlspecialchars($row['medicine_name']) . '</td>
        <td>' . htmlspecialchars($row['before_after_meal']) . '</td>
        <td>' . htmlspecialchars($row['times_a_day']) . '</td>
        <td>' . htmlspecialchars($row['duration_days']) . ' days</td>
    </tr>';
}

$html .= '
    </tbody>
</table>

<h2>Tests to be Done</h2>
<table border="1" cellpadding="4">
    <thead>
        <tr><th>Test</th><th>Notes</th></tr>
    </thead>
    <tbody>';

while ($row = $result_tests->fetch_assoc()) {
    $html .= '<tr>
        <td>' . htmlspecialchars($row['test_name']) . '</td>
        <td>' . htmlspecialchars($row['test_notes']) . '</td>
    </tr>';
}

$html .= '
    </tbody>
</table>

<h2>Special Instructions</h2>
<p>Follow the prescription as advised and consult if you have any questions.</p>

<p>Thank you for your visit!</p>';

// Write HTML to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF to browser
$pdf->Output('prescription.pdf', 'D');

// Close connection
$conn->close();

?>
