<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the user's name from the database

    // Fetch the user's name from the database
    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `id` = :id");  // Use SELECT * without quotes
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $user_name = $row['name'];


        // Fetch total applicants
        $query = $conn->query("SELECT COUNT(*) FROM graduate");
        $totalApplicants = $query->fetchColumn();

        // Fetch total applications
        $query = $conn->query("SELECT COUNT(*) FROM applications");
        $totalApplications = $query->fetchColumn();

        // Fetch active vacancies (where closing_date is in the future)
        $query = $conn->query("SELECT COUNT(*) FROM vacancies WHERE closing_date > NOW()");
        $activeVacancies = $query->fetchColumn();

        // Fetch active cohort programs (where end_date is in the future)
        $query = $conn->query("SELECT COUNT(*) FROM cohort_programs WHERE end_date > NOW()");
        $activeCohortPrograms = $query->fetchColumn();
    }


    include('layout/header.php');

?>




    <div class="app-content-header">
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

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Total Applicants -->
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3><?= $totalApplicants ?></h3>
                            <p>Total Applicants</p>
                        </div>
                        <a href="./graduates_list.php" class="small-box-footer link-light">
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
                        <a href="./graduates_list.php" class="small-box-footer link-dark">
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
                        <a href="./vacancies_list.php" class="small-box-footer link-light">
                            More info <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                <!-- Active Cohort Programs -->
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h3><?= $activeCohortPrograms ?></h3>
                            <p>Active Cohort Programs</p>
                        </div>
                        <a href="./cohort_program_list.php" class="small-box-footer link-light">
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


    <script>
        // Validate title selection
        document.getElementById("title").addEventListener("change", function(e) {
            if (!e.target.value) {
                document.getElementById("title_error").textContent = "Please select a title.";
            } else {
                document.getElementById("title_error").textContent = "";
            }
        });

        // Validate first name as the user types
        document.getElementById("first_name").addEventListener("input", function(e) {
            if (!e.target.value) {
                document.getElementById("first_name_error").textContent = "First name is required.";
            } else {
                document.getElementById("first_name_error").textContent = "";
            }
        });

        // Validate last name as the user types
        document.getElementById("last_name").addEventListener("input", function(e) {
            if (!e.target.value) {
                document.getElementById("last_name_error").textContent = "Last name is required.";
            } else {
                document.getElementById("last_name_error").textContent = "";
            }
        });

        // Validate gender selection
        var genderRadios = document.getElementsByName("gender");
        genderRadios.forEach(function(radio) {
            radio.addEventListener("change", function() {
                document.getElementById("gender_error").textContent = "";
            });
        });

        // Validate date of birth
        document.getElementById("date_of_birth").addEventListener("input", function(e) {
            if (!e.target.value) {
                document.getElementById("dob_error").textContent = "Date of Birth is required.";
            } else {
                document.getElementById("dob_error").textContent = "";
            }
        });

        // Validate national ID input
        document.getElementById("national_id").addEventListener("input", function(e) {
            var nationalId = e.target.value;
            if (!nationalId.match(/^\d{9,}$/)) { // Example pattern: at least 9 digits
                document.getElementById("national_id_error").textContent = "National ID must contain at least 9 digits.";
            } else {
                document.getElementById("national_id_error").textContent = "";
            }
        });

        // Validate disability selection
        document.getElementById("disability").addEventListener("change", function(e) {
            if (!e.target.value) {
                document.getElementById("disability_error").textContent = "Please select an option.";
            } else {
                document.getElementById("disability_error").textContent = "";
            }
        });

        // Validate mobile number to allow only digits
        document.getElementById("mobile_number").addEventListener("input", function(e) {
            var mobile = e.target.value;
            var mobilePattern = /^[0-9]*$/;
            if (!mobilePattern.test(mobile)) {
                document.getElementById("mobile_error").textContent = "Only numbers are allowed.";
                e.target.value = mobile.replace(/[^0-9]/g, ''); // Remove non-digits
            } else {
                document.getElementById("mobile_error").textContent = "";
            }
        });

        // Validate email
        document.getElementById("email").addEventListener("input", function(e) {
            var email = e.target.value;
            var emailPattern = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById("email_error").textContent = "Invalid email format.";
            } else {
                document.getElementById("email_error").textContent = "";
            }
        });

        // Validate postal address
        document.getElementById("postal_address").addEventListener("input", function(e) {
            if (!e.target.value) {
                document.getElementById("postal_error").textContent = "Postal address is required.";
            } else {
                document.getElementById("postal_error").textContent = "";
            }
        });

        // Validate program name
        document.getElementById("program_name").addEventListener("input", function(e) {
            if (!e.target.value) {
                document.getElementById("program_name_error").textContent = "Program name is required.";
            } else {
                document.getElementById("program_name_error").textContent = "";
            }
        });

        // Validate institution
        document.getElementById("institution").addEventListener("input", function(e) {
            if (!e.target.value) {
                document.getElementById("institution_error").textContent = "Institution name is required.";
            } else {
                document.getElementById("institution_error").textContent = "";
            }
        });

        // Validate completion date
        document.getElementById("completion_date").addEventListener("input", function(e) {
            if (!e.target.value) {
                document.getElementById("completion_date_error").textContent = "Completion date is required.";
            } else {
                document.getElementById("completion_date_error").textContent = "";
            }
        });

        // Validate bank account number
        document.getElementById("account_number").addEventListener("input", function(e) {
            var accountNumber = e.target.value;
            if (!accountNumber.match(/^\d+$/)) {
                document.getElementById("account_number_error").textContent = "Account number must contain only digits.";
            } else {
                document.getElementById("account_number_error").textContent = "";
            }
        });

        // Validate file attachments
        document.getElementById("id_copy").addEventListener("change", function(e) {
            if (!e.target.files.length) {
                document.getElementById("id_copy_error").textContent = "Please upload a copy of your ID.";
            } else {
                document.getElementById("id_copy_error").textContent = "";
            }
        });

        document.getElementById("degree_copy").addEventListener("change", function(e) {
            if (!e.target.files.length) {
                document.getElementById("degree_copy_error").textContent = "Please upload a copy of your degree certificate.";
            } else {
                document.getElementById("degree_copy_error").textContent = "";
            }
        });

        // Prevent form submission if any errors
        document.querySelector("form").addEventListener("submit", function(e) {
            var errorFields = document.querySelectorAll(".error");
            errorFields.forEach(function(field) {
                if (field.textContent !== "") {
                    e.preventDefault();
                    alert("Please correct the errors in the form before submitting.");
                }
            });
        });
    </script>
    <!--end::Script-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const districtSelect = document.getElementById('preferred_district');

            // Optional: Add a search functionality to the district dropdown
            if (districtSelect) {
                new SlimSelect({
                    select: districtSelect,
                    placeholder: 'Select a district',
                    searchText: 'No districts found',
                    searchPlaceholder: 'Search for a district'
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const programGeneralSelect = document.getElementById('program_general');
            const majorSelect = document.getElementById('major');

            programGeneralSelect.addEventListener('change', function() {
                const selectedProgramGeneral = this.value;

                // Clear current options
                majorSelect.innerHTML = '<option value="">Loading...</option>';

                if (selectedProgramGeneral) {
                    // Fetch majors based on selected program general
                    fetch(`.') ?>/${selectedProgramGeneral}`)
                        .then(response => response.json())
                        .then(data => {
                            majorSelect.innerHTML = '<option value="">Choose...</option>';
                            data.forEach(major => {
                                const option = document.createElement('option');
                                option.value = major.uuid;
                                option.textContent = major.name;
                                majorSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            majorSelect.innerHTML = '<option value="">Error loading majors</option>';
                        });
                } else {
                    majorSelect.innerHTML = '<option value="">Choose a program general first...</option>';
                }
            });
        });
    </script>
    </body><!--end::Body-->

    </html>

<?php
} else {
    header("Location: http://localhost/graduate_internship_system");
}

?>