<?php include 'index.php'; ?>
<body>

<div class="container">
    <div class="section">
        <h2 class="section-heading text-center">Choose Break Interval</h2>
        <form id="breakIntervalForm" action="set_break_interval.php" method="POST">
            <div class="form-group">
                <label for="doctor">Select Doctor:</label>
                <select class="form-control select2" id="doctor" name="doctor_id" required>
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
                <label for="date">Select Date:</label>
                <input type="date" class="form-control" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="break_start">Break Start Time:</label>
                <input type="time" class="form-control" id="break_start" name="break_start" required>
            </div>
            <div class="form-group">
                <label for="break_end">Break End Time:</label>
                <input type="time" class="form-control" id="break_end" name="break_end" required>
            </div>

            <button type="submit" class="btn btn-primary">Set Break Interval</button>
        </form>
    </div>
</div>

 <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>

    <script>
    $(document).ready(function () {
        // Initialize Select2 on the doctor select dropdown
        $('.select2').select2({
            placeholder: "Search for a doctor",
            allowClear: true
        });

        // Add event listener for form submission
        $("#breakIntervalForm").submit(function (event) {
            // Prevent default form submission behavior
            event.preventDefault();

            // Perform break interval setting
            $.ajax({
                url: "set_break_interval.php",
                type: "POST",
                data: $(this).serialize(), // Serialize form data
                success: function (response) {
                    // Handle success response
                    if (response.startsWith("Error")) {
                        // Use SweetAlert for error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response
                        });
                    } else {
                        // Use SweetAlert for success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to a new page or do something else
                                window.location.href = "get_breaks_list.php";
                            }
                        });
                    }
                },
                error: function (xhr, status, error) {
                    // Use SweetAlert for error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Failed to set break interval. ' + xhr.responseText
                    });
                }
            });
        });
    });
    </script>
</body>
</html>
