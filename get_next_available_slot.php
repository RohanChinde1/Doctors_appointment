<?php

// Connect to your database (replace these with your actual database credentials)
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get doctor ID and booking date from POST request
$doctorId = $_POST['doctor_id'];
$bookingDate = $_POST['booking_date'];

// Get current time in HH:MM format
$currentTime = date("H:i");

// Prepare SQL query to find the next available slot
$sql = "SELECT start_time, end_time FROM slots WHERE doctor_id = ? AND booking_date = ? AND start_time > ? ORDER BY start_time ASC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $doctorId, $bookingDate, $currentTime);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Prepare the response object
$response = array();

if ($result->num_rows > 0) {
    // Fetch the next available slot
    $row = $result->fetch_assoc();
    $response['start_time'] = $row['start_time'];
    $response['end_time'] = $row['end_time'];
} else {
    // No available slots for today
    $response['start_time'] = null;
    $response['end_time'] = null;
}

// Close statement and database connection
$stmt->close();
$conn->close();

// Return the response as JSON
echo json_encode($response);

?>
