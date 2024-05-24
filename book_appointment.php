<?php include'index.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor's Appointment</title>
    <link rel="icon" type="image/x-icon" href="images/download.jpg">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <h2 class="mt-3 mb-3 text-center">Book Appointment</h2> 
        <form id="appointmentForm" action="slots.php" method="POST">
            <div class="form-group">
                <label for="doctor">Select Doctor:</label>
                <select class="form-control select2" id="doctor" name="doctor_id" onchange="loadDoctorInfo()" required>
                    <option value="">Select Doctor</option>
                    <?php
                        include 'db_connect.php';
                        // Fetching doctors from the database
                        $sql = "SELECT doctor_id, name, specialty FROM doctors";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Embedding specialty in the option value
                               echo "<option value='" . $row["doctor_id"] . "' data-specialty='" . $row["specialty"] . "'>" . $row["name"] . " - " . $row["specialty"] . "</option>";
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="patient">Select Patient:</label>
                <select class="form-control select2" id="patient" name="patient_id" required>
                    <option value="">Select Patient</option>
                    <?php
                        // Fetching patients from the database
                        $sql = "SELECT patient_id, name FROM patients";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                               echo "<option value='" . $row["patient_id"] . "'>" . $row["name"] . " (ID: " . $row["patient_id"] . ")</option>";
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="booking_date">Booking Date:</label>
                <input type="date" class="form-control" id="booking_date" name="booking_date" onchange="loadDoctorslots()" required min="<?php echo date('Y-m-d'); ?>">   
            </div>
            <div class="form-group">
                <label for="time_slot">Select Time Slot:</label>
                <select class="form-control" id="time_slot" name="time_slot" required>
                    <!-- Time slots will be loaded dynamically using JavaScript -->
                </select>
            </div>
            <button type="submit" id="bookAppointmentBtn" class="btn btn-primary">Book Appointment</button>
            <div id="bookingStatus"></div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- JavaScript for booking appointment -->
    <script src="dummy.js"></script>

    <script>
        // Function to update doctor's specialty field
        function loadDoctorInfo() { 
            var selectedDoctor = $("#doctor option:selected");
            var specialty = selectedDoctor.data("specialty");
            $("#doctor_specialty").val(specialty);
        }

        // Call loadDoctorInfo() initially to set the default doctor's specialty
        $(document).ready(function () {
            loadDoctorInfo();

            // Update doctor's specialty on doctor selection change
            $("#doctor").change(function () {
                loadDoctorInfo();
                loadDoctorslots();
            });

            // Initialize select2 plugin for doctor and patient select elements
            $('.select2').select2();
        });
    </script>
</body>
</html>
