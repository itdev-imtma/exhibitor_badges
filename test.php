<?php
// Database connection details
$host = 'pms2k19.imtma.in'; // Example: 'localhost' or 'yourdomain.com' if remote
$username = 'pms2k19i_powerBI';
$password = 'powerBI#2k21';
$dbname = 'pms2k19i_pms'; // The name of your PMS database

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM DELEGATELIST";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Create an array to hold the result data
    $data = [];

    // Fetch all rows and store them in the $data array
    while($row = $result->fetch_assoc()) {
        $data[] = $row;  // Add each row to the $data array
    }

    // Use print_r to output the $data array in a human-readable format
    echo "<pre>";  // Optional: Wrap in <pre> tags for better readability of the array
    print_r($data);
    echo "</pre>";
} else {
    echo "0 results";
}

// Close the connection
$conn->close();
?>