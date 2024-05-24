<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "doctors_appointment";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    // If connection fails, display an error message and terminate the script
    die("Connection failed: " . $conn->connect_error);
} else {
    // If connection succeeds, you can optionally display a success message
   // echo "Connected successfully";
}

// Set charset to UTF-8
$conn->set_charset("utf8");
?>
