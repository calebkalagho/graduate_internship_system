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
    }

    include('layout/header.php');
?>

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Cohort Program Assignments</h3>


                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cohort Program Assignments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Cohort Program Assignments</h3>

                    <a href="./add_cohort_assignments.php" class="btn btn-primary float-sm-end">
                        Assign new
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>

                                <th>Cohort Program</th>
                                <th>Department</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query to fetch cohort program assignments and join cohort_programs and departments tables
                            $stmt = $conn->prepare("
                                SELECT 
                                    cpa.uuid, 
                                    cp.name AS cohort_program_name, 
                                    d.name AS department_name 
                                FROM `cohort_program_assignments` cpa
                                JOIN `cohort_programs` cp ON cpa.cohort_program_uuid = cp.uuid
                                JOIN `departments` d ON cpa.department_uuid = d.uuid
                            ");
                            $stmt->execute();
                            $result = $stmt->fetchAll();

                            foreach ($result as $row) {
                                $uuid = $row['uuid'];
                                $cohort_program_name = $row['cohort_program_name'];
                                $department_name = $row['department_name'];
                            ?>
                                <tr>

                                    <td><?= $cohort_program_name ?></td>
                                    <td><?= $department_name ?></td>
                                    <td>
                                        <!-- Add your action buttons here if needed -->
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