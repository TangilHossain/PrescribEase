<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            max-width: 500px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: center;
        }

        input[type="text"] {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: #fff;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:last-child td {
            border-bottom: none;
        }

        td {
            font-size: 14px;
            color: #333;
        }

        hr {
            border: 0;
            height: 1px;
            background: #ddd;
            margin: 30px 0;
        }

        p {
            text-align: center;
            color: #888;
        }

        h2 {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>

    <h1>Search Medicines</h1>
    <form method="GET" action="search.php">
        <input type="text" name="q" placeholder="Search by Brand Name or Generic..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        <button type="submit">Search</button>
    </form>

    <hr>

    <?php
// Connect to the medicine database
$conn = new mysqli("localhost", "root", "", "medicine");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search term from the query string
$searchTerm = isset($_GET['q']) ? $_GET['q'] : '';

if (!empty($searchTerm)) {
    // Perform a fuzzy search using the LIKE operator and SOUNDEX on brand_name and generic columns
    $sql = "SELECT * FROM medicine_table 
            WHERE brand_name LIKE ? 
            OR generic LIKE ?
            OR SOUNDEX(brand_name) = SOUNDEX(?)
            OR SOUNDEX(generic) = SOUNDEX(?)";

    $stmt = $conn->prepare($sql);
    $likeTerm = "%" . $searchTerm . "%";
    $stmt->bind_param("ssss", $likeTerm, $likeTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display the results in a table
    if ($result->num_rows > 0) {
        echo "<h2>Search Results for '" . htmlspecialchars($searchTerm) . "':</h2>";
        echo "<table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Brand Name</th>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Type</th>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Slug</th>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Dosage Form</th>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Generic</th>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Strength</th>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Manufacturer</th>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Package Container</th>
                    <th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Package Size</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['brand_name']) . "</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['type']) . "</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['slug']) . "</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['dosage_form']) . "</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['generic']) . "</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['strength']) . "</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['manufacturer']) . "</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['package_container']) . "</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['Package_Size']) . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No results found for '" . htmlspecialchars($searchTerm) . "'.</p>";
    }

    // Close the statement and connection
    $stmt->close();
} else {
    echo "<p>Please enter a search term.</p>";
}

$conn->close();
?>


</body>
</html>
