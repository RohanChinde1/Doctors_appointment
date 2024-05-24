<?php
// Include database connection
include_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate form data (you can add more validation as needed)
    if (empty($name) || empty($specialty) || empty($email) || empty($password)) {
        // If any required field is empty, display an error message
        echo "All fields are required.";
    } else {
        // Hash the password before storing it in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute SQL query to insert doctor data into the database
        $sql = "INSERT INTO doctors (name, specialty, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $specialty, $email, $hashed_password);

        if ($stmt->execute()) {
            // If insertion is successful, display success message
            echo "Doctor registered successfully!";
        } else {
            // If insertion fails, display error message
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    }
} else {
    // If request method is not POST, redirect to the registration form
    header("Location: doctor_registration_form.html");
    exit();
}

// Close database connection
$conn->close();
?>
