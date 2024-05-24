<?php
include 'db_connect.php';

function generateAdjustedTimeSlots($start_time, $end_time, $interval, $bookedSlots, $breaks) {
    $slots = array();

    // Convert start and end times to timestamps
    $start_timestamp = strtotime($start_time);
    $end_timestamp = strtotime($end_time);

    // Iterate over each 15-minute interval within the working hours
    for ($current_slot = $start_timestamp; $current_slot < $end_timestamp; $current_slot += ($interval * 60)) {
        // Calculate the slot end time based on the interval
        $slot_end = $current_slot + ($interval * 60);

        // Check for overlapping booked slots or breaks
        $is_booked = false;
        foreach ($bookedSlots as $bookedSlot) {
            if ($current_slot < strtotime($bookedSlot['end_time']) && $slot_end > strtotime($bookedSlot['start_time'])) {
                $is_booked = true;
                break;
            }
        }

        $is_break = false;
        foreach ($breaks as $break) {
            if ($current_slot < strtotime($break['break_end']) && $slot_end > strtotime($break['break_start'])) {
                $is_break = true;
                break;
            }
        }

        // If the slot is available, add it to the slots array
        if (!$is_booked && !$is_break) {
            $slots[] = array(
                'start' => date('H:i', $current_slot),
                'end' => date('H:i', $slot_end)
            );
        }
    }

    return $slots;
}


if(isset($_POST['doctor_id']) && isset($_POST['booking_date'])) {
    $doctorId = $_POST['doctor_id'];
    $bookingDate = $_POST['booking_date'];

    // Fetch doctor's information including their working hours
    $sql = "SELECT start_time, end_time FROM doctors WHERE doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctorInfo = $result->fetch_assoc();
    $stmt->close();

    // Fetch booked slots for the selected doctor and booking date
    $sqlBooked = "SELECT start_time, end_time FROM appointments WHERE doctor_id = ? AND booking_date = ?";
    $stmtBooked = $conn->prepare($sqlBooked);
    $stmtBooked->bind_param("is", $doctorId, $bookingDate);
    $stmtBooked->execute();
    $resultBooked = $stmtBooked->get_result();
    $bookedSlots = array();
    while ($rowBooked = $resultBooked->fetch_assoc()) {
        $bookedSlots[] = $rowBooked;
    }
    $stmtBooked->close();

    // Fetch doctor's breaks for the selected date
    $sqlBreaks = "SELECT break_start, break_end FROM breaks WHERE doctor_id = ? AND date = ?";
    $stmtBreaks = $conn->prepare($sqlBreaks);
    $stmtBreaks->bind_param("is", $doctorId, $bookingDate);
    $stmtBreaks->execute();
    $resultBreaks = $stmtBreaks->get_result();
    $breaks = array();
    while ($rowBreaks = $resultBreaks->fetch_assoc()) {
        $breaks[] = $rowBreaks;
    }
    $stmtBreaks->close();

    // Generate adjusted time slots
    $allTimeSlots = generateAdjustedTimeSlots($doctorInfo['start_time'], $doctorInfo['end_time'], 15, $bookedSlots, $breaks);

    // Return available time slots as JSON
    echo json_encode($allTimeSlots);
}
?>
