<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Book Appointment</h1>
        <form action="book_appointment.php" method="POST">
            <div class="form-group">
                <label for="doctor">Select Doctor:</label>
                <select class="form-control" id="doctor" name="doctor">
                    <!-- Populate this dropdown with the list of doctors from the database -->
                    <option value="1">Doctor 1</option>
                    <option value="2">Doctor 2</option>
                    <!-- Add more options dynamically using PHP -->
                </select>
            </div>
            <div class="form-group">
                <label for="date">Select Date:</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="time">Select Time Slot:</label>
                <select class="form-control" id="time" name="time">
                    <!-- Populate this dropdown with the available time slots for the selected doctor and date -->
                    <!-- Use PHP to dynamically generate options based on available slots -->
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Book Appointment</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
