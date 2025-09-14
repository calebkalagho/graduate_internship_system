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

                        $stmt->execute([':status' => 'submitted']);
                        $result = $stmt->fetchAll();

                        foreach ($result as $row) {
                            $applicant_name = $row['first_name'] . ' ' . $row['last_name'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($applicant_name) ?></td>
                                <td><?= htmlspecialchars($row['applied_date']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td>
                                    <a href="view_applicant__other_details.php?application_uuid=<?= $row['uuid'] ?>" class="btn btn-primary">
                                        View Details
                                    </a>
                                     <a href="update_status.php?uuid=<?= $row['uuid'] ?>" class="btn btn-success btn-sm">
                                        Update Status
                                    </a>
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
                        <button type="submit" class="btn btn-success" name="updateApplicantStatus">Update Status</button>
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
    exit(); // Always exit after a header redirect
}
?>

