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

    $filter_query = "";
    $program_general = isset($_GET['program_general']) ? $_GET['program_general'] : '';
    $major = isset($_GET['major']) ? $_GET['major'] : '';

    if ($program_general != 'All' && $major != 'All') {
        $filter_query = " AND vd.program_general_uuid = :program_general AND vd.major_uuid = :major";
    } elseif ($program_general != 'All') {
        $filter_query = " AND vd.program_general_uuid = :program_general";
    } elseif ($major != 'All') {
        $filter_query = " AND vd.major_uuid = :major";
    }

    $stmt = $conn->prepare("
        SELECT 
            v.uuid,
            v.vacancy_title,
            v.summary,
            v.description,
            v.duties,
            v.qualifications,
            v.experience,
            v.opening_date,
            v.closing_date,
            d.name AS department_name,
            CONCAT(e.first_name, ' ', e.last_name) AS employee_name
        FROM vacancies v
        JOIN departments d ON v.department_uuid = d.uuid
        JOIN employees e ON v.employee_uuid = e.uuid
        JOIN vacancy_details vd ON v.uuid = vd.vacancy_uuid
        WHERE 1=1 $filter_query
    ");

    // Bind filter parameters if applicable
    if ($program_general != 'All') {
        $stmt->bindParam(':program_general', $program_general);
    }
    if ($major != 'All') {
        $stmt->bindParam(':major', $major);
    }

    $stmt->execute();
    $result = $stmt->fetchAll();

    include('layout/headergraduate.php');
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
                </div>
                <div class="card-header">
                    <!-- Filter Form -->
                    <form method="GET" action="">
                        <div class="row">
                            <!-- Program General Filter -->
                            <div class="col-md-4">
                                <label for="program_general">Program General</label>
                                <select name="program_general" class="form-control">
                                    <option value="All">Select Program</option>
                                    <?php
                                    $stmt = $conn->prepare("SELECT * FROM education_programs_generals");
                                    $stmt->execute();
                                    $programs = $stmt->fetchAll();
                                    foreach ($programs as $program) {
                                        $selected = isset($_GET['program_general']) && $_GET['program_general'] == $program['uuid'] ? 'selected' : '';
                                        echo "<option value='{$program['uuid']}' $selected>{$program['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Major Filter -->
                            <div class="col-md-4">
                                <label for="major">Major</label>
                                <select name="major" class="form-control">
                                    <option value="All">Select Major</option>
                                    <?php
                                    $stmt = $conn->prepare("SELECT * FROM education_programs_details");
                                    $stmt->execute();
                                    $majors = $stmt->fetchAll();
                                    foreach ($majors as $major) {
                                        $selected = isset($_GET['major']) && $_GET['major'] == $major['uuid'] ? 'selected' : '';
                                        echo "<option value='{$major['uuid']}' $selected>{$major['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-4 mt-4">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Vacancy Title</th>
                                <th>Summary</th>
                                <th>Description</th>
                                <th>Duties</th>
                                <th>Qualifications</th>
                                <th>Experience</th>
                                <th>Department</th>
                                <th>Employee Responsible</th>
                                <th>Opening Date</th>
                                <th>Closing Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Display the filtered vacancies
                            foreach ($result as $row) {
                            ?>
                                <tr>
                                    <td><?= $row['vacancy_title'] ?></td>
                                    <td><?= $row['summary'] ?></td>
                                    <td><?= $row['description'] ?></td>
                                    <td><?= $row['duties'] ?></td>
                                    <td><?= $row['qualifications'] ?></td>
                                    <td><?= $row['experience'] ?></td>
                                    <td><?= $row['department_name'] ?></td>
                                    <td><?= $row['employee_name'] ?></td>
                                    <td><?= $row['opening_date'] ?></td>
                                    <td><?= $row['closing_date'] ?></td>
                                    <td>
                                        <a href="vacancy_details.php?uuid=<?= $row['uuid'] ?>" class="btn btn-info">Details</a>
                                        <button class="btn btn-primary" onclick="shareVacancy('<?= addslashes($row['vacancy_title']) ?>', '<?= $row['uuid'] ?>')">Share</button>
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
    <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">Share Vacancy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="share-options"></div>
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