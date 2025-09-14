<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's name from the database
    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $user_name = $row['name'];
        $guuid = $row['graduate_uuid'];
    }

    include('layout/header.php');
    ?>

    <div class="app-content-header">
        <!-- Filter form -->
        <div class="card card-info card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Filter Applicants</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="" id="filterForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="year_filter">Year:</label>
                                <select class="form-control" id="year_filter" name="year_filter">
                                    <option value="">All Years</option>
                                    <?php
                                    $current_year = date('Y');
                                    for ($i = $current_year; $i >= $current_year - 5; $i--) {
                                        $selected = (isset($_GET['year_filter']) && $_GET['year_filter'] == $i) ? 'selected' : '';
                                        echo "<option value='$i' $selected>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_filter">Status:</label>
                                <select class="form-control" id="status_filter" name="status_filter">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="reviewed" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'reviewed') ? 'selected' : ''; ?>>Reviewed</option>
                                    <option value="rejected" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Section to list applicants -->
        <div class="card card-info card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Applicant List</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    // Base query
                    $query = "
                            SELECT 
                                app.id,
                                a.uuid,
                                app.first_name AS first_name,
                                app.last_name AS last_name,
                                a.applied_date,
                                a.status
                            FROM `applications` a
                            JOIN `graduate` app ON a.applicant_uuid = app.graduate_uuid
                            JOIN `cohort_programs` cp ON a.cohort_uuid = cp.uuid
                            WHERE 1=1
                        ";

                    $params = array();

                    // Apply year filter
                    if (!empty($_GET['year_filter'])) {
                        $query .= " AND YEAR(a.applied_date) = :year";
                        $params[':year'] = $_GET['year_filter'];
                    }

                    // Apply status filter
                    if (!empty($_GET['status_filter'])) {
                        $query .= " AND a.status = :status";
                        $params[':status'] = $_GET['status_filter'];
                    }

                    $stmt = $conn->prepare($query);
                    $stmt->execute($params);

                    $result = $stmt->fetchAll();

                    foreach ($result as $row) {
                        $id = $row['id'];
                        $uuid = $row['uuid'];
                        $applicant_name = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                        $applied_date = htmlspecialchars($row['applied_date']);
                        $status = htmlspecialchars($row['status']);
                        ?>
                        <tr>
                            <td><?= $applicant_name ?></td>
                            <td><?= $applied_date ?></td>
                            <td><?= $status ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for updating applicant status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <!-- ... (modal content remains the same) ... -->
    </div>

    <?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>
<script>
    $(document).ready(function() {
        // Existing modal and form submission code...

        // Auto-submit form when filters change
        $('#year_filter, #status_filter').change(function() {
            $('#filterForm').submit();
        });
    });
</script>
<script>
    $(document).ready(function() {
        // When the update status button is clicked, populate the hidden input with the applicant's UUID
        $('#updateStatusModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var applicantId = button.data('applicant-id'); // Extract info from data-* attributes
            var modal = $(this);
            modal.find('#applicantUuid').val(applicantId);
        });

        // Handle form submission with confirmation dialog
        $('#updateStatusForm').on('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting immediately

            var confirmed = confirm("Are you sure you want to update the status?");
            if (confirmed) {
                this.submit(); // Submit the form if confirmed
            }
        });
    });
</script>