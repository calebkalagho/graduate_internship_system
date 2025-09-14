<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's name from the database

    // Fetch the user's name from the database
    // Assume $user_id is already defined
    // Prepare the SQL statement with JOINs for departments and roles
    $stmt = $conn->prepare("
        SELECT 
            graduate.id, 
            graduate.name, 
            graduate.email, 
            graduate.department_uuid,
            departments.name AS department_name, 
            roles.name AS role_name 
        FROM 
            graduate
        LEFT JOIN departments ON graduate.department_uuid = departments.uuid
        LEFT JOIN roles ON graduate.role_uuid = roles.uuid
        WHERE 
            graduate.id = :id
        ");

    // Bind the `id` parameter to the value of `$user_id`
    $stmt->bindParam(':id', $user_id);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if data is returned
    if ($result) {
        // Assign fetched values to variables
        $graduate_name = $result['name'];
        $graduate_email = $result['email'];

        $department_name = $result['department_name'];
        $role_name = $result['role_name'];
        $user_name = $result['name'];
        $dept_uuid = $result['department_uuid'];






        // Fetch total applicants
        $query = $conn->query("SELECT COUNT(*) FROM graduate");
        $totalApplicants = $query->fetchColumn();

        // Fetch total applications
        $query = $conn->query("SELECT COUNT(*) FROM applications");
        $totalApplications = $query->fetchColumn();

        // Fetch active vacancies (where closing_date is in the future)
        $query = $conn->query("SELECT COUNT(*) FROM vacancies WHERE closing_date > NOW()");
        $activeVacancies = $query->fetchColumn();

        // Prepare and execute the query with a placeholder for the department UUID
        $query = $conn->prepare("SELECT COUNT(*) FROM allocate_applicants WHERE department_uuid = :dept_uuid");

        // Bind the actual value of $dept_uuid to the placeholder
        $query->bindParam(':dept_uuid', $dept_uuid);

        // Execute the query
        $query->execute();

        // Fetch the result
        $allocateapplicants = $query->fetchColumn();



        // Prepare and execute the query with placeholders for the department UUID and reported_date check
        $query = $conn->prepare("SELECT COUNT(*) FROM allocate_applicants WHERE department_uuid = :dept_uuid AND reported_date IS NOT NULL");

        // Bind the actual value of $dept_uuid to the placeholder
        $query->bindParam(':dept_uuid', $dept_uuid);

        // Execute the query
        $query->execute();

        // Fetch the result
        $reportedapplicants = $query->fetchColumn();
    }


    include('layout/headerhr.php');

?>




    <div class="app-content-header">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Dashboard for <?= $department_name  ?></h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">

                <!-- Active Vacancies -->
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3><?= $allocateapplicants ?></h3>
                            <p>Allocated Applicants</p>
                        </div>
                        <a href="./application_list_reviewed_hr.php" class="small-box-footer link-light">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
                <!-- Total Applicants -->
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3><?= $reportedapplicants ?></h3>
                            <p>Total Reported</p>
                        </div>
                        <a href="./application_list_reported_hr.php" class="small-box-footer link-light">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                <!-- Total Applications -->
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3><?= '0' ?></h3>
                            <p>Total with drawn</p>
                        </div>
                        <a href="./application_list_withdrawn_hr.php" class="small-box-footer link-dark">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                <!-- Active Vacancies -->
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3><?= $activeVacancies ?></h3>
                            <p>Active Vacancies</p>
                        </div>
                        <a href="./vacancies_list_hr.php" class="small-box-footer link-light">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>


            </div>
        </div>
    </div>


    </main> <!--end::App Main--> <!--begin::Footer-->
    <footer class="app-footer"> <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline"></div> <!--end::To the end--> <!--begin::Copyright--> <strong>
            Copyright &copy;2024&nbsp;
            <a href="https://adminlte.io" class="text-decoration-none">Graduate Management System</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
    </footer> <!--end::Footer-->
    </div> <!--end::App Wrapper--> <!--begin::Script--> <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script> <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script> <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script> <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="./dist/js/adminlte.js"></script> <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = ".sidebar-wrapper";
        const Default = {
            scrollbarTheme: "os-theme-light",
            scrollbarAutoHide: "leave",
            scrollbarClickScroll: true,
        };
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (
                sidebarWrapper &&
                typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== "undefined"
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script> <!--end::OverlayScrollbars Configure--> <!-- OPTIONAL SCRIPTS --> <!-- sortablejs -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ=" crossorigin="anonymous"></script> <!-- sortablejs -->


    </body><!--end::Body-->

    </html>

<?php
} else {
    header("Location: http://localhost/graduate_internship_system");
}

?>