<?php
include 'index.php';
include 'db_connect.php';

// Function to sanitize user input
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, $input);
}
// Check if delete button is clicked
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = sanitize($conn, $_GET['id']);
    $sql = "DELETE FROM breaks WHERE id = $id";
    if($conn->query($sql) === TRUE) {
       // Success message with SweetAlert
        echo "<script>
                $(document).ready(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Patient deleted successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        location.reload();
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
                        text: 'Error deleting patient: " . $conn->error . "',
                    });
                });
              </script>";
    }
}


?>

<div id="doctor_breaks_list" class="container">
    <!-- Doctor breaks list and pagination will be loaded here -->
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    // Function to load doctor breaks for a specific page
    function loadDoctorBreaks(page) {
        $.ajax({
            url: 'breakslist.php?page=' + page, // Corrected URL
            type: 'get',
            success: function(response) {
                $('#doctor_breaks_list').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading doctor breaks:', error);
            }
        });
    }

    // Load initial doctor breaks
    loadDoctorBreaks(1);

    // Handle pagination clicks
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        loadDoctorBreaks(page);
    });
    // Handle delete button click
    $(document).on('click', '.delete-btn', function() {
        var patientId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If user confirms, proceed with deletion
                $.ajax({
                    url: 'get_breaks_list.php?action=delete&id=' + patientId,
                    type: 'GET',
                    success: function(response) {
                        // Reload the page after successful deletion
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting patient:', error);
                    }
                });
            }
        });
    });
});

</script>

</body>
</html>
