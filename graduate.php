<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's name from the database
    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id");  // Use SELECT * without quotes
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $user_name = $row['name'];

        // Prepare and execute the query to fetch user data
        $query = $conn->prepare("SELECT * FROM graduate WHERE id = ?");  // Again, use SELECT * without quotes
        $query->execute([$user_id]);
        $userData = $query->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            $graduate_uuid = $userData['graduate_uuid'];

            // Fetch new vacancies count
            $query = $conn->query("SELECT COUNT(*) FROM vacancies WHERE status = 'open'");
            $newVacancies = $query->fetchColumn();

            // Fetch total applications for the current user
            $query = $conn->prepare("SELECT COUNT(*) FROM applications WHERE applicant_uuid = ?");
            $query->execute([$graduate_uuid]);
            $totalApplications = $query->fetchColumn();

            // Fetch application status counts
            $query = $conn->query("SELECT status, COUNT(*) as count FROM applications GROUP BY status");
            $applicationStatus = $query->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    include('layout/headergraduate.php');

?>

    <main class="app-main"> <!--begin::App Content Header-->



        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Dashboard</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <!-- New Vacancies -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-primary">
                            <div class="inner">
                                <h3><?= $newVacancies ?></h3>
                                <p>New Vacancies</p>
                            </div>
                            <!-- Add the SVG icon here -->
                            <a href="./vacancies_list_graduate.php" class="small-box-footer link-light">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Application Status -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-success">
                            <div class="inner">
                                <h3><?= !empty($applicationStatus[0]['count']) ? $applicationStatus[0]['count'] : 0 ?></h3>
                                <p>Application Status: <?= !empty($applicationStatus[0]['status']) ? $applicationStatus[0]['status'] : 'N/A' ?></p>
                            </div>
                            <!-- Add the SVG icon here -->
                            <a href="./application_graduate_list.php" class="small-box-footer link-light">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Total Applications -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-warning">
                            <div class="inner">
                                <h3><?= $totalApplications ?></h3>
                                <p>Total Applications</p>
                            </div>
                            <!-- Add the SVG icon here -->
                            <a href="./application_graduate_list.php" class="small-box-footer link-dark">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Applications in Danger -->
                    <div class="col-lg-3 col-6">
                        <div class="small-box text-bg-danger">
                            <div class="inner">
                                <h3><?= !empty($applicationStatus[1]['count']) ? $applicationStatus[1]['count'] : 0 ?></h3>
                                <p>Rejected Applications</p>
                            </div>
                            <!-- Add the SVG icon here -->
                            <a href="./application_graduate_list.php" class="small-box-footer link-light">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </main> <!--end::App Main--> <!--begin::Footer-->


<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>