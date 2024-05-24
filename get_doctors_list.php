<?php
include 'index.php';
include 'db_connect.php'; // Include database connection

// Function to sanitize user input
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, $input);
}

// Check if delete button is clicked
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = sanitize($conn, $_GET['id']);
    $sql = "DELETE FROM doctors WHERE doctor_id = $id";
    if($conn->query($sql) === TRUE) {
        // Success message with SweetAlert
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Doctor deleted successfully',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    location.reload();
                });
              </script>";
    } else {
        // Error message with SweetAlert
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error deleting doctor: " . $conn->error . "',
                });
              </script>";
    }
}
?>

<div id="doctor_list" class="container">
    <!-- Doctor list will be loaded here -->
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    // Function to load doctors for a specific page
    function loadDoctors(page) {
        $.ajax({
            url: 'load_doctors.php',
            type: 'get',
            data: {page: page},
            success: function(response) {
                $('#doctor_list').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading doctors:', error);
            }
        });
    }

    // Load initial doctors
    loadDoctors(1);

    // Handle pagination clicks
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        loadDoctors(page);
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function() {
        var doctorId = $(this).data('id');
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
                    url: 'get_doctors_list.php?action=delete&id=' + doctorId,
                    type: 'GET',
                    success: function(response) {
                        // Reload the page after successful deletion
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting doctor:', error);
                    }
                });
            }
        });
    });
});
</script>

</body>
</html>
