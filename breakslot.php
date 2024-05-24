<?php
include 'db_connect.php';

if(isset($_POST['doctor_id']) && isset($_POST['booking_date'])) {
    $doctorId = $_POST['doctor_id'];
    $bookingDate = $_POST['booking_date'];

    try {
        // Fetch doctor's working hours
        $sql = "SELECT start_time, end_time FROM doctors WHERE doctor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctorInfo = $result->fetch_assoc();
        $stmt->close();

        // Generate available time slots based on the doctor's schedule
        $allTimeSlots = generateTimeSlots($doctorInfo['start_time'], $doctorInfo['end_time'], 15);

        // Fetch already booked time slots for the selected doctor and booking date
        $sqlBooked = "SELECT start_time, end_time FROM appointments WHERE doctor_id = ? AND booking_date = ?";
        $stmtBooked = $conn->prepare($sqlBooked);
        $stmtBooked->bind_param("is", $doctorId, $bookingDate);
        $stmtBooked->execute();
        $resultBooked = $stmtBooked->get_result();
        $bookedSlots = array();
        while ($rowBooked = $resultBooked->fetch_assoc()) {
            $bookedSlots[] = array('start' => $rowBooked['start_time'], 'end' => $rowBooked['end_time']);
        }
        $stmtBooked->close();

        // Remove booked slots from available slots
        foreach ($bookedSlots as $bookedSlot) {
            $start = strtotime($bookedSlot['start']);
            $end = strtotime($bookedSlot['end']);

            // Iterate through all time slots for the day
            foreach ($allTimeSlots as $key => $timeSlot) {
                $slotStart = strtotime($timeSlot['start']);
                $slotEnd = strtotime($timeSlot['end']);

                // Check if the booked slot overlaps with the current time slot
                if (($start >= $slotStart && $start < $slotEnd) || ($end > $slotStart && $end <= $slotEnd)) {
                    // Remove the current time slot from the available slots array
                    unset($allTimeSlots[$key]);
                }
            }
        }

        // Re-index the array after removing booked slots
        $allTimeSlots = array_values($allTimeSlots);

        // Return available time slots as JSON
        echo json_encode($allTimeSlots);
    } catch (Exception $e) {
        echo json_encode(array('error' => 'An error occurred: ' . $e->getMessage()));
    }
} else {
    echo json_encode(array('error' => 'Incomplete data provided'));
}

// Function to generate time slots
function generateTimeSlots($start_time, $end_time, $interval) {
    $slots = array();
    $current_time = strtotime($start_time);
    $end_time = strtotime($end_time);

    while ($current_time < $end_time) {
        $slot_start = date('H:i', $current_time);
        $current_time = strtotime('+' . $interval . ' minutes', $current_time);
        $slot_end = date('H:i', $current_time);
        $slots[] = array('start' => $slot_start, 'end' => $slot_end);
    }

    return $slots;
}
?>
