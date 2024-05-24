<?php
include 'db_connect.php';

function generateAdjustedTimeSlots($start_time, $end_time, $interval, $lunch_start, $lunch_end) {
    $slots = array();
    $current_time = strtotime($start_time);
    $end_time = strtotime($end_time);
    $lunch_start = strtotime($lunch_start);
    $lunch_end = strtotime($lunch_end);

    while ($current_time < $end_time) {
        $slot_start = date('H:i', $current_time);
        // Skip time slots falling within lunch break
        if ($current_time < $lunch_start || $current_time >= $lunch_end) {
            $current_time = strtotime('+' . $interval . ' minutes', $current_time);  
            $slot_end = date('H:i', $current_time);
            $slots[] = array('start' => $slot_start, 'end' => $slot_end);
        } else {
            // Move the current time to the end of the lunch break
            $current_time = $lunch_end;
        }
    }

    return $slots;
}

if(isset($_POST['doctor_id']) && isset($_POST['booking_date'])) {
    $doctorId = $_POST['doctor_id'];
    $bookingDate = $_POST['booking_date'];

    // Fetch doctor's information including their working hours and lunch break
    $sql = "SELECT start_time, end_time, lunch_start, lunch_end FROM doctors WHERE doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctorInfo = $result->fetch_assoc();
    $stmt->close();

    // Generate available time slots based on the doctor's schedule
    $slots = generateAdjustedTimeSlots($doctorInfo['start_time'], $doctorInfo['end_time'], 15, $doctorInfo['lunch_start'], $doctorInfo['lunch_end']);

    // Fetch already booked time slots for the selected doctor and booking date
    $sqlBooked = "SELECT start_time FROM appointments WHERE doctor_id = ? AND booking_date = ?";
    $stmtBooked = $conn->prepare($sqlBooked);
    $stmtBooked->bind_param("is", $doctorId, $bookingDate);
    $stmtBooked->execute();
    $resultBooked = $stmtBooked->get_result();
    $bookedSlots = array();
    while ($rowBooked = $resultBooked->fetch_assoc()) {
        $bookedSlots[] = $rowBooked['start_time'];
    }
    $stmtBooked->close();

    // Filter out booked slots from available slots
    $availableSlots = array_filter($slots, function($slot) use ($bookedSlots) {
        return !in_array($slot['start'], $bookedSlots);
    });

    // Return available time slots as JSON
    echo json_encode($availableSlots);
}
?>
