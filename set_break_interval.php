<?php
include 'db_connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $doctorId = $_POST['doctor_id'];
    $date = $_POST['date'];
    $breakStart = $_POST['break_start']; 
    $breakEnd = $_POST['break_end'];

    try {
       // Check if the new break interval overlaps with existing appointments for the same doctor on the same date
$sql_check_overlap = "SELECT * FROM appointments WHERE doctor_id = ? AND booking_date = ? AND ((start_time < ? AND end_time > ?) OR (start_time >= ? AND start_time < ?) OR (end_time > ? AND end_time <= ?) OR (start_time >= ? AND end_time <= ?))";
$stmt_check_overlap = $conn->prepare($sql_check_overlap);
$stmt_check_overlap->bind_param("ssssssssss", $doctorId, $date, $breakEnd, $breakStart, $breakStart, $breakEnd, $breakStart, $breakEnd, $breakStart, $breakEnd);
$stmt_check_overlap->execute();
$result_check_overlap = $stmt_check_overlap->get_result();

if ($result_check_overlap->num_rows > 0) {
    // If there is an overlap, delete the overlapping appointments
    $sql_delete_overlap = "DELETE FROM appointments WHERE doctor_id = ? AND booking_date = ? AND ((start_time < ? AND end_time > ?) OR (start_time >= ? AND start_time < ?) OR (end_time > ? AND end_time <= ?) OR (start_time >= ? AND end_time <= ?))";
    $stmt_delete_overlap = $conn->prepare($sql_delete_overlap);
    $stmt_delete_overlap->bind_param("ssssssssss", $doctorId, $date, $breakEnd, $breakStart, $breakStart, $breakEnd, $breakStart, $breakEnd, $breakStart, $breakEnd);
    $stmt_delete_overlap->execute();
    
    // Insert the new break interval into the database
    $sql_insert = "INSERT INTO breaks (doctor_id, date, break_start, break_end) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssss", $doctorId, $date, $breakStart, $breakEnd);

    if ($stmt_insert->execute()) {
        echo "Break interval added successfully.";
    } else {
        echo "Failed to add break interval.";
    }
} else {
    // Insert the new break interval into the database without deleting any appointments
    $sql_insert = "INSERT INTO breaks (doctor_id, date, break_start, break_end) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssss", $doctorId, $date, $breakStart, $breakEnd);

    if ($stmt_insert->execute()) {
        echo "Break interval added successfully.";
    } else {
        echo "Failed to add break interval.";
    }
}

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close database connection
    $conn->close();
}
?>
