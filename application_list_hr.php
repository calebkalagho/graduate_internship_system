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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query to fetch applicants and join relevant tables
                        $stmt = $conn->prepare("
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
                                WHERE a.status = :status
                            ");
                        $stmt->execute([
                            ':status' => 'submitted'
                        ]);

                        $result = $stmt->fetchAll();

                        foreach ($result as $row) {
                            $id = $row['id'];
                            $uuid = $row['uuid'];
                            $applicant_name = $row['first_name'] . ' ' . $row['last_name'];
                            $applied_date = $row['applied_date'];
                            $status = $row['status'];
                        ?>
                            <tr>

                                <td><?= $applicant_name ?></td>
                                <td><?= $applied_date ?></td>
                                <td><?= $status ?></td>
                                <td>
                                    <!-- Add your action buttons here if needed -->
                                    <a href="view_applicant__other_details.php?applicant_uuid=<?= $row['id'] ?>" class="btn btn-primary">
                                        View Details
                                    </a>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#updateStatusModal" data-applicant-id="<?= $row['uuid'] ?>">
                                        Update Status
                                    </button>
                                </td>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Applicant Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="updateStatusForm" action="./endpoint/Applicants.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="uuid" id="applicantUuid">

                        <!-- Status select field -->
                        <div class="form-group">
                            <label for="status">Select Status:</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="reviewed">Reviewed</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="updateApplicantStatus" name="updateApplicantStatus">Update Status</button>
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