<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor's Appointment</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .container {
            max-width: 800px;
            margin: auto;
            margin-top: 50px;
        }
        .btn {
            margin-bottom: 10px;
        }
        .section-heading {
            cursor: pointer;
        }
        .dashboard-menu {
            padding-left: 0;
            list-style: none;
        }
        .dashboard-menu li {
            margin-bottom: 10px;
        }
        .dashboard-menu li a {
            text-decoration: none;
            color: #000;
            padding: 5px 10px;
            display: block;
        }
        .dashboard-menu li a:hover {
            background-color: #f0f0f0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Doctor's Appointment</h1>

        <!-- Dashboard Menu -->
        <button class="btn btn-primary" onclick="toggleMenu()">Dashboard</button>
        <div class="dashboard" style="padding-left: 0;">
            <ul class="dashboard-menu" style="display: none;">
                <li><a href="#" onclick="toggleSection('doctorsList')">Doctors List</a></li>
                <li><a href="#" onclick="toggleSection('registrationForm')">Doctor Registration</a></li>
            </ul>
        </div>

        <!-- Doctors List -->
        <div class="section">
            <h2 class="section-heading" onclick="toggleSection('doctorsList')"></h2>
            <div id="doctorsList" style="display: none;">
                <?php
                // Include database connection
                include_once 'db_connect.php';

                // Query to fetch the list of doctors
                $sql = "SELECT * FROM doctors";
                $result = $conn->query($sql);

                // Check if there are any doctors in the database
                if ($result->num_rows > 0) {
                    // Output data of each row
                    echo "<ul>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>" . $row["name"] . " - " . $row["specialty"] . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "No doctors found";
                }

                // Close database connection
                $conn->close();
                ?>
            </div>
        </div>

        <!-- Doctor Registration -->
        <div class="section">
            <h2 class="section-heading" onclick="toggleSection('registrationForm')"></h2>
            <div id="registrationForm" style="display: none;">
                <form action="register_doctor.php" method="POST">
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="specialty">Specialty:</label>
                        <input type="text" class="form-control" id="specialty" name="specialty" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="text" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="availability">Available Slots:</label>
                        <input type="text" class="form-control" id="availability" name="availability" placeholder="Enter available slots (e.g., Monday 9:00 AM - 12:00 PM)" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional, for button styling) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script>
        function toggleSection(sectionId) {
            var section = document.getElementById(sectionId);
            if (section.style.display === "none") {
                section.style.display = "block";
            } else {
                section.style.display = "none";
            }
        }

        function toggleMenu() {
            var menu = document.querySelector('.dashboard-menu');
            if (menu.style.display === "none") {
                menu.style.display = "block";
            } else {
                menu.style.display = "none";
            }
        }
    </script>
</body>
</html>
