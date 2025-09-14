<?php

$graduate_uuid = $graduate_uuid ?? null;

// Fetch the count of new notifications
$new_notifications_count = 0;

if ($graduate_uuid) {
    // PDO Query
    $query = "SELECT COUNT(*) AS new_notifications FROM notifications WHERE graduate_uuid = :graduate_uuid AND status = 'new'";

    $stmt = $conn->prepare($query);
    $stmt->execute(['graduate_uuid' => $graduate_uuid]);

    $new_notifications_count = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Graduate MS |Graduate</title><!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="AdminLTE v4 | Dashboard">
    <meta name="author" content="ColorlibHQ">
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS.">
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard"><!--end::Primary Meta Tags--><!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous"><!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css" integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous"><!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css" integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous"><!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="./dist/css/adminlte.css"><!--end::Required Plugin(AdminLTE)--><!-- apexcharts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"><!-- jsvectormap -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css" integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
    <link rel="stylesheet" href="./dist/css/style.css"><!--end::Required Plugin(AdminLTE)--><!-- apexcharts -->

</head> <!--end::Head--> <!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="./admin.php" class="nav-link">Home</a> </li>


                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->

                    <li class="nav-item dropdown"> <a class="nav-link" href="./admin_notification.php"> <i class="bi bi-chat-text"></i>




                            <?php if ($new_notifications_count > 0) { ?>
                                <span class="navbar-badge badge text-bg-danger"><?= $new_notifications_count ?></span>
                            <?php } else {
                            ?>
                                <span class="navbar-badge badge text-bg-danger">0</span>

                            <?php
                            }

                            ?></a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> <a href="#" class="dropdown-item"> <!--begin::Message-->
                                <div class="d-flex">
                                    <div class="flex-shrink-0"> <img src="./dist/assets/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 rounded-circle me-3"> </div>
                                    <div class="flex-grow-1">


                                    </div>
                                </div> <!--end::Message-->
                            </a>
                            <div class="dropdown-divider"></div> <a href="#" class="dropdown-item"> <!--begin::Message-->
                                <div class="d-flex">
                                    <div class="flex-shrink-0"> <img src="./dist/assets/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 rounded-circle me-3"> </div>
                                    <div class="flex-grow-1">


                                    </div>
                                </div> <!--end::Message-->
                            </a>
                            <div class="dropdown-divider"></div> <a href="#" class="dropdown-item"> <!--begin::Message-->
                                <div class="d-flex">
                                    <div class="flex-shrink-0"> <img src="./dist/assets/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 rounded-circle me-3"> </div>
                                    <div class="flex-grow-1">

                                    </div>
                                </div> <!--end::Message-->
                            </a>
                        </div>
                    </li> <!--end::Messages Dropdown Menu--> <!--begin::Notifications Dropdown Menu-->
                    <!--end::Notifications Dropdown Menu--> <!--begin::Fullscreen Toggle-->
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a> </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"> <img src="./dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image"> <span class="d-none d-md-inline"><?= $user_name ?></span> </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> <!--begin::User Image-->
                            <li class="user-header text-bg-primary"> <img src="./dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image">
                                <p>
                                <h2> <?= $user_name ?></h2>

                                </p>
                            </li> <!--end::User Image--> <!--begin::Menu Body-->
                            <li class="user-body"> <!--begin::Row-->
                                <div class="row">

                                </div> <!--end::Row-->
                            </li> <!--end::Menu Body--> <!--begin::Menu Footer-->
                            <li class="user-footer"> <a href=" btn btn-default btn-flat">Profile</a> <a href="./endpoint/logout.php">Sign out</a> </li> <!--end::Menu Footer-->
                        </ul>
                    </li> <!--end::User Menu Dropdown-->
                </ul> <!--end::End Navbar Links-->
            </div> <!--end::Container-->
        </nav> <!--end::Header--> <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <!-- Sidebar Brand -->
            <div class="sidebar-brand">
                <a href="./index.html" class="brand-link">
                    <img src="./dist/assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow">
                    <span class="brand-text fw-light">Graduate MS</span>
                </a>
            </div>

            <!-- Sidebar Wrapper -->
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <!-- Sidebar Menu -->
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="./admin.php" class="nav-link">
                                <i class="nav-icon bi bi-speedometer2"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>


                        <!-- Applicants -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>
                                    Applicants
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./graduates_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Graduates</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <!-- Vacancies -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-briefcase-fill"></i>
                                <p>
                                    Vacancies
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./vacancies_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Vacancy Management</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Application -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-file-earmark-person"></i>
                                <p>
                                    Application
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./application_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Review</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="./application_list_reviewed.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Allocations</p>
                                    </a>
                                </li>
                            </ul>
                            <!-- Reports -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-file-earmark-text"></i>
                                <p>
                                    Reports
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./applicants-report.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Applicants Report</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        </li>
                        <!-- M & E Configuration -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-gear"></i>
                                <p>
                                   M & E Configuration
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./goal_list_admin.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Goals</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="./objectives_admin_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Objectives</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="./kpi_admin_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Key perfomance indicators </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="./intern_perfomance_admin_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Intern perfomance report </p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <!-- Programs Configuration -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-gear"></i>
                                <p>
                                    Programs Configuration
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./cohort_assignments_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Cohort Program Assignments</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="./programs_general_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Education Programs General</p>
                                    </a>
                                </li>
                            </ul>
                        </li>



                        <!-- Configurations -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-sliders"></i>
                                <p>
                                    Configurations
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./cohort_program_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Cohort Programs</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="./department_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Departments</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="./institution_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Institutions</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="./ministries_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Ministries</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="./roles_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Roles</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- User Management -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-person-fill"></i>
                                <p>
                                    User Management
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="./employees_list.php" class="nav-link">
                                        <i class="nav-icon bi bi-circle"></i>
                                        <p>Employees</p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    </ul>
                    <!-- End Sidebar Menu -->
                </nav>
            </div>
            <!-- End Sidebar Wrapper -->
        </aside>

        <main class="app-main"> <!--begin::App Content Header-->