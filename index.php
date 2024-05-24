<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Doctor's Appointment</title>
    <link rel="icon" type="image/x-icon" href="images/download.jpg">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
       <style>
        body {
            font-family: Arial, sans-serif;
         background-image: url("images/da2.jpg");
            background-size: cover; /* Cover the entire background */
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: float; /* Fix the background when scrolling */
            height: 100vh; /* Ensure the background covers the full viewport height */
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */*/
        }
        .navbar {
            background-color: rgba(173, 216, 230, 0.8);
        }
        .navbar-brand {
            font-weight: bold;
            color: #333;
        }
        .navbar-nav .nav-link {
            color: #333;
        }
        .navbar-nav .nav-link.active {
            border-bottom: 2px solid #333;
        }
        .navbar-nav .nav-link:hover {
            color: #555;
        }
       .container {
    background-color: rgba(255, 255, 255, 1); /* Background color with transparency */
    padding: 17px;
    border-radius: 15px;
}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="index.php">HOME</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                 <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'register_doctor.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="register_doctor.php">Doctor Registration</a>
                </li>
                  <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'get_doctors_list.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="get_doctors_list.php">Doctors List</a>
                </li>
                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'register_patient.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="register_patient.php">Patient Registration</a>
                </li>
                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'get_patients_list.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="get_patients_list.php">Patients List</a>
                </li>
                 <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'book_appointment.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="book_appointment.php">Doctor's Appointment</a>
                </li>
                 <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'appointments.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="appointments.php">Appointments List</a>
                </li>
                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'break.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="break.php">Doctor's Break</a>
                </li>
                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'get_breaks_list.php') ? 'active' : ''; ?>">
                    <a class="nav-link" href="get_breaks_list.php">Breaks List</a>
                </li>
            </ul>
        </div>
    </nav>
</body>
</html>
