 <?php include 'index.php'; ?>
<?php
include 'db_connect.php'; // Include database connection

// Function to sanitize user input
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, $input);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Get form data
    $id = $_POST['id'];
    $new_time_slot = sanitize($conn, $_POST['new_time_slot']);
    $doctorId = sanitize($conn, $_POST['doctor_id']);
    $bookingDate = sanitize($conn, $_POST['booking_date']);

    // Extract start and end time from selected time slot
    $times = explode(' - ', $new_time_slot);
    $new_start_time = $times[0];
    $new_end_time = $times[1];

    // Update appointment time in the database
    $sql = "UPDATE appointments SET start_time=?, end_time=?, doctor_id=?, booking_date=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $new_start_time, $new_end_time, $doctorId, $bookingDate, $id);
    
    if ($stmt->execute()) {
        // Success message with SweetAlert
        echo "<script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Appointment information updated successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Redirect to the appointments list page after successful update
                        window.location.href = 'appointments.php';
                    });
                });
              </script>";
    } else {
        // Error message with SweetAlert
        echo "<script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error updating appointment information: " . $stmt->error . "',
                    });
                });
              </script>";
    }
}

// Check if the appointment ID is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve appointment information from the database
    $sql = "SELECT * FROM appointments WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $current_start_time = $row['start_time'];
        $current_end_time = $row['end_time'];
        $doctorId = $row['doctor_id'];
        $bookingDate = $row['booking_date'];

        // Retrieve booked slots for the selected doctor and booking date
        $sqlBreaks = "SELECT break_start, break_end FROM breaks WHERE doctor_id = ? AND date = ?";
        $stmtBreaks = $conn->prepare($sqlBreaks);
        $stmtBreaks->bind_param("is", $doctorId, $bookingDate);
        $stmtBreaks->execute();
        $resultBreaks = $stmtBreaks->get_result();
        $breaks = array();
        while ($rowBreak = $resultBreaks->fetch_assoc()) {
            $breaks[] = array('start' => $rowBreak['break_start'], 'end' => $rowBreak['break_end']);
        }
        $stmtBreaks->close();

        // Generate available time slots based on the doctor's schedule and exclude break times
        function generateAvailableTimeSlots($start_time, $end_time, $breaks) {
            $slots = array();
            $current_time = strtotime($start_time);
            $end_time = strtotime($end_time);

            while ($current_time < $end_time) {
                $slot_start = date('H:i', $current_time);
                $current_time = strtotime('+15 minutes', $current_time);
                $slot_end = date('H:i', $current_time);

                // Check if the slot falls within any break time
                $isBreak = false;
                foreach ($breaks as $break) {
                    if ($slot_start >= $break['start'] && $slot_end <= $break['end']) {
                        $isBreak = true;
                        break;
                    }
                }

                if (!$isBreak) {
                    $slots[] = array('start' => $slot_start, 'end' => $slot_end);
                }
            }

            return $slots;
        }

        // Generate available time slots
        $availableSlots = generateAvailableTimeSlots($row['start_time'], $row['end_time'], $breaks);

    } else {
        echo "<div class='alert alert-danger mt-3' role='alert'>Appointment not found.</div>";
        exit();
    }
} else {
    // echo "<div class='alert alert-danger mt-3' role='alert'>Invalid request.</div>";
    exit();
}

// Retrieve list of doctors
$sqlDoctors = "SELECT doctor_id, name FROM doctors";
$resultDoctors = $conn->query($sqlDoctors);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <h2>Edit Appointment</h2>
        <form action="edit_appointment.php" method="POST" id="editAppointmentForm">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <!-- Choose Doctor Option -->
            <div class="form-group">
                <label for="doctor_id">Choose Doctor:</label>
                <select class="form-control select2" id="doctor_id" name="doctor_id" required>
                    <?php while ($rowDoctor = $resultDoctors->fetch_assoc()): ?>
                        <option value="<?php echo $rowDoctor['doctor_id']; ?>" <?php echo ($doctorId == $rowDoctor['doctor_id']) ? 'selected' : ''; ?>><?php echo $rowDoctor['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <!-- Choose Date Option -->
            <div class="form-group">
                <label for="booking_date">Choose Date:</label>
                <input type="date" class="form-control" id="booking_date" name="booking_date" value="<?php echo $bookingDate; ?>" required>
            </div>
            
            <!-- Available Time Slots -->
            <div class="form-group">
                <label for="new_time_slot">Choose Slot:</label>
                <select class="form-control" id="new_time_slot" name="new_time_slot" required>
                    <?php foreach ($availableSlots as $slot): ?>
                        <option value="<?php echo $slot['start'] . ' - ' . $slot['end']; ?>" <?php echo ($current_start_time == $slot['start'] && $current_end_time == $slot['end']) ? 'selected' : ''; ?>><?php echo $slot['start'] . ' - ' . $slot['end']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary" name="submit">Update</button>
            <a href="appointments.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <!-- Include jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    
    <!-- Include SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2 on the doctor selection dropdown
            $('.select2').select2({
                placeholder: "Search for a doctor",
                allowClear: true
            });

            // Function to update available time slots based on the selected doctor and booking date
            function updateAvailableTimeSlots() {
                var doctorId = $('#doctor_id').val();
                var bookingDate = $('#booking_date').val();
                
                $.ajax({
                    url: 'slots.php',
                    type: 'POST',
                    data: { doctor_id: doctorId, booking_date: bookingDate },
                    dataType: 'json',
                    success: function(response) {
                        var availableSlots = response;
                        var options = '';
                        var currentDate = new Date();
                        var currentHour = currentDate.getHours();
                        var currentMinute = currentDate.getMinutes();

                        // Populate time slots, filtering out past time slots for the current date
                        availableSlots.forEach(function(slot) {
                            var startTimeParts = slot.start.split(":");
                            var slotHour = parseInt(startTimeParts[0]);
                            var slotMinute = parseInt(startTimeParts[1]);

                            // Check if the slot is in the future
                            if (
                                currentDate < new Date(bookingDate + "T" + slot.start) || 
                                (currentDate.getDate() === new Date(bookingDate).getDate() && 
                                currentHour < slotHour) ||
                                (currentDate.getDate() === new Date(bookingDate).getDate() && 
                                currentHour === slotHour && 
                                currentMinute < slotMinute)
                            ) {
                                options += '<option value="' + slot.start + ' - ' + slot.end + '">' + slot.start + ' - ' + slot.end + '</option>';
                            }
                        });

                        $('#new_time_slot').html(options);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }

            // Event listeners for doctor selection change and booking date change
            $('#doctor_id, #booking_date').change(function() {
                updateAvailableTimeSlots();
            });

            // Initial population of available time slots based on the selected doctor and booking date
            updateAvailableTimeSlots();
        });
    </script>
</body>
</html>
