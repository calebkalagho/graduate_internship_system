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
                    <h3 class="mb-0">All Vacancies</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Vacancies</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline mb-4">
                <div class="card-header">
                    <h3 class="card-title">Vacancies</h3>
                    <a href="./add_vacancy.php" class="btn btn-primary float-sm-end">
                        Add New Vacancy
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Vacancy Title</th>
                            <th>Summary</th>
                            <th>Department</th>
                            <th>Opening Date</th>
                            <th>Closing Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Query to fetch vacancies and join the departments and employees tables
                        $stmt = $conn->prepare("
                                SELECT 
                                    v.uuid,
                                    v.vacancy_title,
                                    v.summary,
                                    v.opening_date,
                                    v.closing_date,
                                    d.name AS department_name
                                FROM `vacancies` v
                                JOIN `departments` d ON v.department_uuid = d.uuid
                            ");
                        $stmt->execute();
                        $result = $stmt->fetchAll();

                        foreach ($result as $row) {
                            $uuid = $row['uuid'];
                            $vacancy_title = $row['vacancy_title'];
                            $summary = $row['summary'];
                            $opening_date = $row['opening_date'];
                            $closing_date = $row['closing_date'];
                            $department_name = $row['department_name'];
                            ?>
                            <tr>
                                <td><?= $vacancy_title ?></td>
                                <td><?= $summary ?></td>
                                <td><?= $department_name ?></td>
                                <td><?= $opening_date ?></td>
                                <td><?= $closing_date ?></td>
                                <td>
                                    <a href="vacancy_details.php?uuid=<?= $uuid ?>" class="btn btn-info btn-sm">Details</a>
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