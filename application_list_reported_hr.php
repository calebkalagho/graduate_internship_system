<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("
    SELECT 
        graduate.id, 
        graduate.name, 
        graduate.email, 
        graduate.department_uuid,
        graduate.graduate_uuid,
        departments.name AS department_name, 
        roles.name AS role_name 
    FROM 
        graduate
    LEFT JOIN departments ON graduate.department_uuid = departments.uuid
    LEFT JOIN roles ON graduate.role_uuid = roles.uuid
    WHERE 
        graduate.id = :id
    ");

    // Bind the `id` parameter to the value of `$user_id`
    $stmt->bindParam(':id', $user_id);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if data is returned
    if ($result) {
        // Assign fetched values to variables
        $graduate_name = $result['name'];
        $graduate_email = $result['email'];

        $department_name = $result['department_name'];
        $role_name = $result['role_name'];
        $user_name = $result['name'];
        $dept_uuid = $result['department_uuid'];

        $user_name = $result['name'];
        $guuid = $result['graduate_uuid'];
    }

    include('layout/headerhr.php');
?>

    <div class="app-content-header">


        <!-- Section to list applicants -->
        <div class="card card-info card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Applicant List</h3>
            </div>
            <div class="card-body">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Applicant Name</th>
                                <th>Applied Date</th>
                                <th>Reporting Date</th>
                                <th>Reported Date</th>
                                <th>Allocation Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query to fetch applicants from allocate_applicants table where department_uuid matches $dept_uuid
                            $stmt = $conn->prepare("
                SELECT 
                    app.id AS applicant_id,
                    app.first_name AS first_name,
                    app.last_name AS last_name,
                    a.applied_date,
                    alloc.allocate_applicant_id as alloc_id,
                    alloc.reporting_date,
                    alloc.reported_date,
                    alloc.allocation_status
                FROM 
                    allocate_applicants alloc
                JOIN 
                    applications a ON alloc.applications_uuid = a.uuid
                JOIN 
                    graduate app ON alloc.applicant_uuid = app.graduate_uuid
                WHERE 
                    alloc.department_uuid = :dept_uuid
            ");

                            // Bind the department_uuid parameter to the value of $dept_uuid
                            $stmt->bindParam(':dept_uuid', $dept_uuid);

                            // Execute the query
                            $stmt->execute();

                            // Fetch all the results
                            $result = $stmt->fetchAll();

                            // Loop through the results and display them in the table
                            foreach ($result as $row) {
                                $applicant_ids = $row['applicant_id'];
                                $applicant_id = $row['alloc_id'];
                                $applicant_name = $row['first_name'] . ' ' . $row['last_name'];
                                $applied_date = $row['applied_date'];
                                $reporting_date = $row['reporting_date'];
                                $reported_date = $row['reported_date'] ?? 'N/A'; // If reported_date is null, show 'N/A'
                                $allocation_status = $row['allocation_status'];
                                if ($row['allocation_status'] == 'reported') {


                            ?>
                                    <tr>
                                        <td><?= $applicant_name ?></td>
                                        <td><?= $applied_date ?></td>
                                        <td><?= $reporting_date ?></td>
                                        <td><?= $reported_date ?></td>
                                        <td><?= $allocation_status ?></td>
                                        <td>
                                            <a href="view_applicant__other_details.php?applicant_uuid=<?= $applicant_ids ?>" class="btn btn-primary">
                                                View Details
                                            </a>
                                            <?php if ($allocation_status != 'reported') {
                                            ?>

                                                <a href="allocated_applicant.php?applicant_uuid=<?= $applicant_id ?>" class="btn btn-primary">Update Reported</a>

                                            <?php }

                                            ?>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal for updating applicant status -->
    <div class="modal fade" id="updateApplicantModal" tabindex="-1" role="dialog" aria-labelledby="updateApplicantModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateApplicantModalLabel">Update Applicant Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateApplicantForm" action="./endpoint/Applicants.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="applicant_id" name="applicant_id" value="">
                        <div class="form-group">
                            <label for="allocation_status">Select Status</label>
                            <select class="form-control" id="allocation_status" name="allocation_status" required>
                                <option value="">Select Status</option>
                                <option value="reported">Reported</option>
                                <option value="withdrawn">Withdrawn</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="form-group" id="reportedDateField" style="display:none;">
                            <label for="reported_date">Reported Date</label>
                            <input type="date" class="form-control" id="reported_date" name="reported_date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_reporting_applicant">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>




<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>

<script>
    $(document).ready(function() {
        // Show modal and set applicant_id when "Update" is clicked
        $('.btn-update').on('click', function() {
            var applicant_id = $(this).data('applicant-id');
            $('#applicant_id').val(applicant_id);
            $('#updateApplicantModal').modal('show');
        });

        // Show/hide the reported_date field based on the selected status
        $('#allocation_status').on('change', function() {
            var status = $(this).val();
            if (status === 'reported') {
                $('#reportedDateField').show();
            } else {
                $('#reportedDateField').hide();
                $('#reported_date').val(''); // Clear the reported date if not 'reported'
            }
        });

        // Handle form submission with AJAX
        $('#updateApplicantForm').on('submit', function(e) {
            e.preventDefault();

            var applicant_id = $('#applicant_id').val();
            var allocation_status = $('#allocation_status').val();
            var reported_date = $('#reported_date').val();

            $.ajax({
                url: './endpoint/Applicants.php',
                method: 'POST',
                data: {
                    applicant_id: applicant_id,
                    allocation_status: allocation_status,
                    reported_date: reported_date
                },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.status === 'success') {
                        // Close modal and show success message
                        $('#updateApplicantModal').modal('hide');
                        alert(res.message);
                        location.reload(); // Optionally refresh the page or update the table
                    } else {
                        alert(res.message);
                    }
                },
                error: function() {
                    alert('An error occurred while updating the applicant status.');
                }
            });
        });
    });
</script>