<?php
include 'index.php';
include 'db_connect.php'; // Include database connection

// Function to sanitize user input
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, $input);
}

// Function to check for overlapping appointments
function checkForOverlappingAppointments($conn, $doctor_id, $date, $start_time, $end_time) {
    $sql = "SELECT * FROM appointments WHERE doctor_id = $doctor_id AND booking_date = '$date' 
            AND ((start_time <= '$start_time' AND end_time >= '$start_time') OR 
                 (start_time <= '$end_time' AND end_time >= '$end_time') OR
                 (start_time >= '$start_time' AND end_time <= '$end_time'))";
    $result = $conn->query($sql);
    return $result->num_rows > 0;
}

// Function to delete overlapping appointments
function deleteOverlappingAppointments($conn, $doctor_id, $date, $start_time, $end_time) {
    $sql = "DELETE FROM appointments WHERE doctor_id = $doctor_id AND booking_date = '$date' 
            AND ((start_time <= '$start_time' AND end_time >= '$start_time') OR 
                 (start_time <= '$end_time' AND end_time >= '$end_time') OR
                 (start_time >= '$start_time' AND end_time <= '$end_time'))";
    $conn->query($sql);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Get form data
    $id = $_POST['id'];
    $date = $_POST['date'];
    $break_start = $_POST['break_start'];
    $break_end = $_POST['break_end'];
  
    // Retrieve doctor ID from the database
    $sql = "SELECT doctor_id FROM breaks WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $doctor_id = $row['doctor_id'];

        // Check for overlapping appointments
        if (checkForOverlappingAppointments($conn, $doctor_id, $date, $break_start, $break_end)) {
            // Delete overlapping appointments
            deleteOverlappingAppointments($conn, $doctor_id, $date, $break_start, $break_end);
        }
    } else {
        echo "Break not found.";
        exit();
    }

    // Update doctor break information in the database
    $sql = "UPDATE breaks SET date='$date', break_start='$break_start', break_end='$break_end' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        // Redirect to the doctor breaks list page after successful update
        // Success message with SweetAlert
        echo "<script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Break information updated successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = 'get_breaks_list.php';
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
                        text: 'Error updating Break information: " . $stmt->error . "',
                    });
                });
              </script>";
    }
}

// Check if the break ID is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve doctor break information from the database
    $sql = "SELECT * FROM breaks WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $date = $row['date'];
        $break_start = $row['break_start'];
        $break_end = $row['break_end'];
    } else {
        echo "Break not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Break</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Edit Break</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo $date; ?>" required>
            </div>
            <div class="form-group">
                <label for="break_start">Break Start:</label>
                <input type="time" class="form-control" id="break_start" name="break_start" value="<?php echo $break_start; ?>" required>
            </div>
            <div class="form-group">
                <label for="break_end">Break End:</label>
                <input type="time" class="form-control" id="break_end" name="break_end" value="<?php echo $break_end; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Update</button>
            <a href="get_breaks_list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
