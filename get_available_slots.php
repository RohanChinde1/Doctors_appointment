<?php
include 'db_connect.php'; // Include database connection

// Get the doctor ID and booking date from the AJAX request
$doctorId = $_POST['doctor_id'];
$bookingDate = $_POST['booking_date'];

// Fetch doctor's working hours and lunch break from the doctors table
$sqlDoctor = "SELECT start_time, end_time, lunch_start, lunch_end FROM doctors WHERE doctor_id = ?";
$stmtDoctor = $conn->prepare($sqlDoctor);
$stmtDoctor->bind_param("i", $doctorId);
$stmtDoctor->execute();
$resultDoctor = $stmtDoctor->get_result();

// Initialize variables
$availableSlots = array();
$interval = 15; // Time slot interval in minutes

if ($resultDoctor->num_rows > 0) {
    $doctor = $resultDoctor->fetch_assoc();
    $start_time = $doctor['start_time'];
    $end_time = $doctor['end_time'];
    $lunch_start = $doctor['lunch_start'];
    $lunch_end = $doctor['lunch_end'];

    // Generate available time slots excluding lunch break
    $current_time = strtotime($start_time);
    $end_time = strtotime($end_time);
    while ($current_time < $end_time) {
        $start = date('H:i', $current_time);
        $current_time = strtotime("+" . $interval . " minutes", $current_time);
        if ($current_time < strtotime($lunch_start) || $current_time >= strtotime($lunch_end)) {
            $end = date('H:i', $current_time);
            $availableSlots[] = array('start' => $start, 'end' => $end);
        }
    }
}

// Output the available time slots as HTML options
foreach ($availableSlots as $slot) {
    echo '<option value="' . $slot['start'] . '-' . $slot['end'] . '">' . $slot['start'] . ' - ' . $slot['end'] . '</option>';
}

// Close prepared statements and database connection
$stmtDoctor->close();
$conn->close();
?>
