function loadDoctorslots() { 
    var doctorId = $("#doctor").val();
    var bookingDate = $("#booking_date").val();
    var specialty = $("#doctor option:selected").data("specialty");
    $("#doctor_specialty").val(specialty);

    if (doctorId !== "") {
        // Fetch and display doctor's specialty immediately
      
        // Fetch and populate time slots for the selected date
        $.ajax({
            url: "slots.php",
            type: "POST",
            data: { doctor_id: doctorId, booking_date: bookingDate },
            dataType: "json",
            success: function (response) {
                console.log(response);
                var timeSlotSelect = $("#time_slot");
                timeSlotSelect.empty(); // Clear previous options
                if (response.length > 0) {
                    $.each(response, function (index, slot) {
                        var option = $("<option></option>").text(slot.start + " - " + slot.end).val(slot.start + "-" + slot.end);
                        timeSlotSelect.append(option);
                    });
                } else {
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
        // If doctor or booking date is not selected, reset the specialty field
        $("#doctor_specialty").val(""); 
    }
}

console.log($availableSlots);
// Add event listener for form submission
$("#appointmentForm").submit(function(event) {
    // Prevent default form submission behavior
    event.preventDefault();

    // Get selected start time
    var startTime = $("#time_slot").val();

    // Calculate end time
    var endTime = calculateEndTime(startTime);

    // Include both start time and end time in form data
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
                alert("Booked successfully!");
            } else if (response === "Slot already booked") {
                alert("This slot is already booked. Please choose another slot.");
            } else {
                alert("Booking failed: " + response);
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
            alert("Booking failed: " + xhr.responseText);
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
