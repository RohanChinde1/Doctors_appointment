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
    $sql = "DELETE FROM patients WHERE patient_id = $id";
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

<div id="patient_list" class="container">
    <!-- Patient list and pagination will be loaded here -->
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    // Function to load patients for a specific page
    function loadPatients(page) {
        $.ajax({
            url: 'load_patients.php',
            type: 'get',
            data: {page: page},
            success: function(response) {
                $('#patient_list').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading patients:', error);
            }
        });
    }

    // Load initial patients
    loadPatients(1);

    // Handle pagination clicks
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        loadPatients(page);
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
                    url: 'get_patients_list.php?action=delete&id=' + patientId,
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
