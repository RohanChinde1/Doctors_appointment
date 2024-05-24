<?php
include 'index.php';
include 'db_connect.php'; // Include database connection

// Function to sanitize user input
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, $input);
}

// Check if form is submitted for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $doctorId = sanitize($conn, $_POST['doctor_id']);
    $name = sanitize($conn, $_POST['name']);
    $specialty = sanitize($conn, $_POST['specialty']);
    $start_time = sanitize($conn, $_POST['start_time']);
    $end_time = sanitize($conn, $_POST['end_time']);

    // Update doctor details in the database
    $sql = "UPDATE doctors SET name='$name', specialty='$specialty', start_time='$start_time', end_time='$end_time' WHERE doctor_id='$doctorId'";
    if ($conn->query($sql) === TRUE) {
        // Success message with SweetAlert
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Doctor details updated successfully',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location.href = 'get_doctors_list.php';
                });
              </script>";
    } else {
        echo "<div class='alert alert-danger mt-3' role='alert'>Error updating doctor details: " . $conn->error . "</div>";
    }
} else {
    // Get the doctor ID from the URL
    $doctorId = isset($_GET['id']) ? $_GET['id'] : null;

    // Fetch doctor details from the database based on the ID
    $sql = "SELECT * FROM doctors WHERE doctor_id = '$doctorId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
?>
<div class="container">
    <h2 class="mt-3 mb-4 text-center">Edit Doctor</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="doctor_id" value="<?php echo $row['doctor_id']; ?>">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name']; ?>">
        </div>
        <div class="form-group">
            <label for="specialty">Specialization:</label>
            <input type="text" class="form-control" id="specialty" name="specialty" value="<?php echo $row['specialty']; ?>">
        </div>
        <div class="form-group">
            <label for="start_time">Start Time:</label>
            <input type="text" class="form-control" id="start_time" name="start_time" value="<?php echo $row['start_time']; ?>" readonly>
        </div>
        <div class="form-group">
            <label for="end_time">End Time:</label>
            <input type="text" class="form-control" id="end_time" name="end_time" value="<?php echo $row['end_time']; ?>" readonly>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="get_doctors_list.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php
    } else {
        echo "<div class='alert alert-danger mt-3' role='alert'>Doctor not found</div>";
    }
}
?>
