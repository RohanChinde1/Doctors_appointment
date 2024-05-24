<?php
include 'db_connect.php';

// Pagination variables
$results_per_page = 5; // Adjust the number of results per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page number, default to 1
$offset = ($page - 1) * $results_per_page;

// Query to fetch doctor breaks for the current page
$sql = "SELECT b.id, d.name AS doctor_name, d.doctor_id, b.date, b.break_start, b.break_end FROM breaks b INNER JOIN doctors d ON b.doctor_id = d.doctor_id LIMIT $offset, $results_per_page";
$result = $conn->query($sql);

?>
<h2 class="mt-3 mb-4 text-center">Break's List</h2>
<div class="table-responsive">
    <table class="table table-bordered table-striped text-center">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Doctor ID</th>
                <th scope="col">Doctor Name</th>
                <th scope="col">Date</th>
                <th scope="col">Break Start</th>
                <th scope="col">Break End</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serialNumber = ($page - 1) * $results_per_page + 1;
            // Output data of each doctor break
            while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $row["doctor_id"]; ?></td>
                    <td><?php echo $row["doctor_name"]; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row["date"])); ?></td>
                    <td><?php echo $row["break_start"]; ?></td>
                    <td><?php echo $row["break_end"]; ?></td>
                    <td>
                        <!-- Edit and delete buttons -->
                        <a href="edit_break.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-primary mr-2">
                            <i class="fas fa-edit"></i> 
                        </a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row["id"]; ?>">
                                <i class="fas fa-trash-alt"></i> 
                            </button>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

<div class="container">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php
            // Pagination links
            $sql_count = "SELECT COUNT(id) AS total FROM breaks";
            $result_count = $conn->query($sql_count);
            $row_count = $result_count->fetch_assoc();
            $total_breaks = $row_count['total'];
            $total_pages = ceil($total_breaks / $results_per_page);
            
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<li class='page-item ".($i==$page ? 'active' : '')."'><a class='page-link' href='breaklist.php?page=".$i."'>".$i."</a></li>";
            }
            ?>
        </ul>
    </nav>
</div>

<?php
// Close database connection
$conn->close();
?>
