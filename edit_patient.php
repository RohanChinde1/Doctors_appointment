<?php include 'index.php'; ?>
<?php
include 'db_connect.php'; // Include database connection

// Function to sanitize user input
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, $input);
}

// Initialize variables
$name = $age = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Get form data
    $id = $_POST['id'];
    $name = sanitize($conn, $_POST['name']);
    $age = sanitize($conn, $_POST['age']);

    // Update patient information in the database
    $sql = "UPDATE patients SET name='$name', age='$age' WHERE patient_id=$id";

    if ($conn->query($sql) === TRUE) {
        // Success message with SweetAlert
        echo "<script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Patient information updated successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Redirect to the patient list page after successful update
                        window.location.href = 'get_patients_list.php';
                    });
                });
              </script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Check if the patient ID is provided in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve patient information from the database
    $sql = "SELECT * FROM patients WHERE patient_id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $age = $row['age'];
    } else {
        echo "Patient not found.";
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
    <title>Edit Patient</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <h2>Edit Patient</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" class="form-control" id="age" name="age" value="<?php echo $age; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Update</button>
            <a href="get_patients_list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
