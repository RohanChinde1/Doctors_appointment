<?php
include 'db_connect.php'; // Include database connection

// Function to sanitize user input
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, $input);
}

// Check if doctor ID is set in POST data
if (isset($_POST['id'])) {
    $id = sanitize($conn, $_POST['id']);

    // Delete doctor from the database
    $sql = "DELETE FROM doctors WHERE doctor_id = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo "Doctor deleted successfully";
    } else {
        echo "Error deleting doctor: " . $conn->error;
    }
} else {
    echo "Doctor ID not provided";
}
?>
