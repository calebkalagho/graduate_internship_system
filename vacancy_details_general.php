<?php
include('layout/front/header.php');
include('./conn/conn.php');

// Check if a vacancy UUID is provided
if (!isset($_GET['uuid'])) {
    header('Location: index.php');
    exit();
}

$vacancy_uuid = $_GET['uuid'];

// Fetch vacancy details from the database
$stmt = $conn->prepare("
    SELECT 
        v.uuid,
        v.vacancy_title,
        v.summary,
        v.description,
        v.duties,
        v.qualifications,
        v.experience,
        v.department_uuid,
        v.opening_date,
        v.closing_date,
        v.employee_uuid,
        d.name AS department_name,
        CONCAT(e.first_name, ' ', e.last_name) AS employee_name
    FROM vacancies v
    JOIN departments d ON v.department_uuid = d.uuid
    JOIN employees e ON v.employee_uuid = e.uuid
    WHERE v.uuid = :uuid AND v.status = 'active'
");
$stmt->bindParam(':uuid', $vacancy_uuid);
$stmt->execute();
$vacancy = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vacancy) {
    header('Location: index.php');
    exit();
}
?>

    <!-- Vacancy Details Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="mb-5">
                        <h1 class="display-5 mb-4"><?php echo htmlspecialchars($vacancy['vacancy_title']); ?></h1>
                        <p class="mb-4"><?php echo nl2br(htmlspecialchars($vacancy['summary'])); ?></p>
                    </div>
                    <div class="mb-5">
                        <h3 class="mb-4">Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($vacancy['description'])); ?></p>
                    </div>
                    <div class="mb-5">
                        <h3 class="mb-4">Duties</h3>
                        <p><?php echo nl2br(htmlspecialchars($vacancy['duties'])); ?></p>
                    </div>
                    <div class="mb-5">
                        <h3 class="mb-4">Qualifications</h3>
                        <p><?php echo nl2br(htmlspecialchars($vacancy['qualifications'])); ?></p>
                    </div>
                    <div class="mb-5">
                        <h3 class="mb-4">Experience</h3>
                        <p><?php echo nl2br(htmlspecialchars($vacancy['experience'])); ?></p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4 mb-5">
                        <h3 class="mb-4">Vacancy Details</h3>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($vacancy['department_name']); ?></p>
                        <p><strong>Opening Date:</strong> <?php echo date('d M Y', strtotime($vacancy['opening_date'])); ?></p>
                        <p><strong>Closing Date:</strong> <?php echo date('d M Y', strtotime($vacancy['closing_date'])); ?></p>
                        <p><strong>Contact Person:</strong> <?php echo htmlspecialchars($vacancy['employee_name']); ?></p>
                    </div>
                    <div class="bg-light rounded p-4">
                        <a href="#" class="btn btn-primary w-100 mb-3">Apply Now</a>
                        <a href="index.php" class="btn btn-outline-primary w-100">Back to Vacancies</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Vacancy Details End -->

<?php
include('layout/front/footer.php');
?>