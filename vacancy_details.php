<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Check if the UUID for the vacancy is provided
    $user_id = $_SESSION['user_id'];
    // Fetch the user's name from the database
    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $user_name = $row['name'];
    }
    if (isset($_GET['uuid'])) {
        $vacancy_uuid = $_GET['uuid'];

        // Fetch the vacancy details
        $stmt = $conn->prepare("
            SELECT 
                v.uuid, v.vacancy_title, v.summary, v.description, v.duties, v.qualifications, v.experience,
                v.opening_date, v.closing_date, v.created_at, v.updated_at, v.status, v.type, 
                d.name AS department_name, CONCAT(e.first_name, ' ', e.last_name) AS employee_name
            FROM vacancies v
            JOIN departments d ON v.department_uuid = d.uuid
            JOIN employees e ON v.employee_uuid = e.uuid
            WHERE v.uuid = :vacancy_uuid
        ");
        $stmt->bindParam(':vacancy_uuid', $vacancy_uuid);
        $stmt->execute();

        // Check if the vacancy exists
        if ($stmt->rowCount() > 0) {
            $vacancy = $stmt->fetch();

            // Fetch the related vacancy details (program general and major)
            $stmt_details = $conn->prepare("
                SELECT 
                    vd.program_general_uuid, vd.major_uuid,
                    epg.name AS program_general_name, epg.description AS program_general_description,
                    epd.name AS major_name, epd.description AS major_description
                FROM vacancy_details vd
                JOIN education_programs_generals epg ON vd.program_general_uuid = epg.uuid
                JOIN education_programs_details epd ON vd.major_uuid = epd.uuid
                WHERE vd.vacancy_uuid = :vacancy_uuid
            ");
            $stmt_details->bindParam(':vacancy_uuid', $vacancy_uuid);
            $stmt_details->execute();
            $vacancy_details = $stmt_details->fetch();
        } else {
            echo "Vacancy not found.";
            exit;
        }
    } else {
        echo "No vacancy selected.";
        exit;
    }

    include('layout/headergraduate.php');
?>

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Vacancy Details</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="vacancies.php">Vacancies</a></li>
                        <li class="breadcrumb-item active">Vacancy Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">Vacancy Information</h3>
                </div>
                <div class="card-body">
                    <h4>Vacancy Title: <?= $vacancy['vacancy_title'] ?></h4>
                    <p><strong>Summary:</strong> <?= $vacancy['summary'] ?></p>
                    <p><strong>Description:</strong> <?= $vacancy['description'] ?></p>
                    <p><strong>Duties:</strong> <?= $vacancy['duties'] ?></p>
                    <p><strong>Qualifications:</strong> <?= $vacancy['qualifications'] ?></p>
                    <p><strong>Experience:</strong> <?= $vacancy['experience'] ?></p>
                    <p><strong>Department:</strong> <?= $vacancy['department_name'] ?></p>
                    <p><strong>Responsible Employee:</strong> <?= $vacancy['employee_name'] ?></p>
                    <p><strong>Opening Date:</strong> <?= $vacancy['opening_date'] ?></p>
                    <p><strong>Closing Date:</strong> <?= $vacancy['closing_date'] ?></p>
                    <p><strong>Status:</strong> <?= $vacancy['status'] ?></p>
                    <p><strong>Type:</strong> <?= $vacancy['type'] ?></p>
                </div>

                <div class="card-header">
                    <h3 class="card-title">Program and Major Details</h3>
                </div>
                <div class="card-body">
                    <p><strong>Program General:</strong> <?= $vacancy_details['program_general_name'] ?> - <?= $vacancy_details['program_general_description'] ?></p>
                    <p><strong>Major:</strong> <?= $vacancy_details['major_name'] ?> - <?= $vacancy_details['major_description'] ?></p>
                </div>
            </div>
        </div>
    </div>

<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
    exit;
}
?>