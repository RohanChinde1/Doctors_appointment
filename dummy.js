 // Function to load doctor's slots
function loadDoctorslots() {
    var doctorId = $("#doctor").val();
    var bookingDate = $("#booking_date").val();
    var specialty = $("#doctor option:selected").data("specialty");
    $("#doctor_specialty").val(specialty);

    // Get the current date and time
    var currentDate = new Date();
    var currentHour = currentDate.getHours();
    var currentMinute = currentDate.getMinutes();    

    if (doctorId !== "") {
        $.ajax({
            url: "slots.php",
            type: "POST",
            data: { doctor_id: doctorId, booking_date: bookingDate },
            dataType: "json",
            success: function (response) {
                console.log(response);
                var availableSlots = response; // Define availableSlots here
                var timeSlotSelect = $("#time_slot");
                timeSlotSelect.empty(); // Clear previous options
                if (availableSlots.length > 0) {
                    // Populate time slots
                    $.each(availableSlots, function (index, slot) {
                        // Parse start time to check if it's in the past
                        var startTimeParts = slot.start.split(":");
                        var slotHour = parseInt(startTimeParts[0]);
                        var slotMinute = parseInt(startTimeParts[1]);

                        // Check if the slot is in the past
                        if (
                            currentDate < new Date(bookingDate + "T" + slot.start) || 
                            (currentDate.getDate() === new Date(bookingDate).getDate() && 
                            currentHour < slotHour) ||
                            (currentDate.getDate() === new Date(bookingDate).getDate() && 
                            currentHour === slotHour && 
                            currentMinute < slotMinute)
                        ) {
                            // Create option elements for available slots
                            var optionText = slot.start + " - " + slot.end;
                            var option = $("<option></option>").text(optionText).val(slot.start + "-" + slot.end);
                            timeSlotSelect.append(option);
                        }
                    });
                } else {
                    // Handle case when no available slots
                    var option = $("<option></option>").text("No available time slots").val("");
                    timeSlotSelect.append(option);
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                // Handle error
            }
        });
    } else {
        // Reset time slot select if doctor not selected
        $("#time_slot").empty();
    }
}

// Add event listener for form submission
$("#appointmentForm").submit(function (event) {
    // Prevent default form submission behavior
    event.preventDefault();

    // Get selected start time
    var startTime = $("#time_slot").val();

    // Calculate end time
    var endTime = calculateEndTime(startTime);

    // Include patient name in form data 
    var patientName = $("#patient").val();

    // Include both start time, end time, and patient name in form data
    $(this).append('<input type="hidden" name="patient_name" value="' + patientName + '">');
    $(this).append('<input type="hidden" name="start_time" value="' + startTime + '">');
    $(this).append('<input type="hidden" name="end_time" value="' + endTime + '">');

    // Serialize form data
    var formData = $(this).serialize();

    // Perform booking
    $.ajax({
        url: "book_slot.php",
        type: "POST",
        data: formData,
        success: function (response) {
            if (response === "Booking Successful") {
                // Disable booked slot
                $("#time_slot option[value='" + startTime + "']").prop("disabled", true);
                // Use SweetAlert for success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Booking successful!',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "appointments.php";
                    }
                });
            } else if (response === "Slot already booked") {
                // Use SweetAlert for already booked slot
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'This slot is already booked. Please choose another slot.'
                });
            } else {
                // Use SweetAlert for other errors
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Booking failed: ' + response
                });
            }
        },
        error: function (xhr, status, error) {
            // Use SweetAlert for error message
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Booking failed: ' + xhr.responseText
            });
        }
    });
});

// Function to calculate end time based on start time
function calculateEndTime(startTime) {
    // Parse the start time into hours and minutes
    var startHour = parseInt(startTime.split(":")[0]);
    var startMinute = parseInt(startTime.split(":")[1]);

    // Add 15 minutes to the start time
    var endMinute = startMinute + 15;
    var endHour = startHour;

    // Adjust hours and minutes if end minute exceeds 60
    if (endMinute >= 60) {
        endMinute -= 60;
        endHour++;
    }

    // Format the end time
    var endTime = endHour.toString().padStart(2, '0') + ":" + endMinute.toString().padStart(2, '0') + ":00";

    return endTime;
}

// Trigger initial loading of doctor's slots
loadDoctorslots();

// Update doctor's slots on doctor selection change
$("#doctor").change(function () {
    loadDoctorslots();
});
