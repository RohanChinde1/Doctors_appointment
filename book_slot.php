<?php
include 'db_connect.php';

if(isset($_POST['doctor_id']) && isset($_POST['booking_date']) && isset($_POST['start_time']) && isset($_POST['end_time']) && isset($_POST['patient_id'])) {
    // Retrieve values from POST data
    $doctorId = $_POST['doctor_id'];
    $bookingDate = $_POST['booking_date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $patientId = $_POST['patient_id'];

    try {
        // Check if the patient has already booked the same slot with another doctor
        $sqlCheck = "SELECT COUNT(*) as count FROM appointments WHERE patient_id = ? AND booking_date = ? AND ((start_time <= ? AND end_time >= ?) OR (start_time <= ? AND end_time >= ?))";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("isssss", $patientId, $bookingDate, $startTime, $startTime, $endTime, $endTime);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $rowCheck = $resultCheck->fetch_assoc();
        
        if ($rowCheck['count'] > 0) {
            // Slot already booked with another doctor
            echo "Cannot book the same slot with another doctor.";
        } else {
            // Insert booked slot into the database
            $sqlInsert = "INSERT INTO appointments (doctor_id, patient_id, booking_date, start_time, end_time) VALUES (?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            // $isBooked = 1; // Set the flag 'is_booked' to 1 for booked slot
            $stmtInsert->bind_param("iisss", $doctorId, $patientId, $bookingDate, $startTime, $endTime);
            
            if ($stmtInsert->execute()) {
                echo "Booking Successful";
            } else {
                // Return error message if insertion fails
                echo "Booking Failed: An error occurred while booking.";
            }
            // $stmtInsert->close();
        }
        // $stmtCheck->close();
    } catch (mysqli_sql_exception $e) {
        // Return error message if any exception occurs
        echo "Booking Failed: " . $e->getMessage();
    }
} else {
    // Return error message if any required parameter is missing
    echo "Booking Failed: Required parameters missing";
}
?>
