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

    include('layout/headergraduate.php');
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
                                WHERE a.applicant_uuid = :guuid
                            ");
                        $stmt->execute([
                            ':guuid' => $guuid
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
                                    <a href="view_applicant_details.php?applicant_uuid=<?= $row['id'] ?>" class="btn btn-primary">
                                        View Details
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
    </div>

<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>