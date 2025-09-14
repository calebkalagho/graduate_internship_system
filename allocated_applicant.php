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

    if (isset($_GET['applicant_uuid'])) {
        $applicant_uuid = $_GET['applicant_uuid'];

        // Fetch Ministries
        $stmt = $conn->prepare("SELECT uuid, name FROM ministries");
        $stmt->execute();
        $ministries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch Institutions
        $stmt = $conn->prepare("SELECT uuid, name FROM institutions");
        $stmt->execute();
        $institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Handle the form submission
            $allocation_type = $_POST['allocation_type'];
            $institution_or_ministry = $_POST['institution_or_ministry'];
            $department_uuid = $_POST['department_uuid'];
            $reporting_date = $_POST['reporting_date'];

            // Insert the allocation into the database
            $stmt = $conn->prepare("
            INSERT INTO allocations 
            (applicant_uuid, allocation_type, institution_or_ministry, department_uuid, reporting_date)
            VALUES (:applicant_uuid, :allocation_type, :institution_or_ministry, :department_uuid, :reporting_date)
        ");
            $stmt->execute([
                ':applicant_uuid' => $applicant_uuid,
                ':allocation_type' => $allocation_type,
                ':institution_or_ministry' => $institution_or_ministry,
                ':department_uuid' => $department_uuid,
                ':reporting_date' => $reporting_date
            ]);

            // Redirect after successful allocation
            header("Location: success.php");
            exit();
        }
    }

?>

    <div class="container">
        <h3>Update Allocated Applicant</h3>

        <form action="./endpoint/Applicants.php" method="POST">
            <input type="hidden" name="applicant_id" id="applicant_id" value="  <?= $applicant_uuid ?> " class="form-control" required>
            <div class="modal-body">

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



<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>

<script>
    $(document).ready(function() {
        // Show modal and set applicant_id when "Update" is clicked


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


    });
</script>