<?php
session_start();
include('./conn/conn.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    include('layout/header.php');
    ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add New Vacancy</h3>
                    </div>
                    <div class="card-body">
                        <!-- Vacancy Add Form -->
                        <form action="process_add_vacancy.php" method="POST">
                            <div class="form-group mb-3">
                                <label for="vacancy_title">Vacancy Title</label>
                                <input type="text" class="form-control" name="vacancy_title" id="vacancy_title" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="summary">Summary</label>
                                <textarea class="form-control" name="summary" id="summary" rows="2" required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="duties">Duties</label>
                                <textarea class="form-control" name="duties" id="duties" rows="4" required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="qualifications">Qualifications</label>
                                <textarea class="form-control" name="qualifications" id="qualifications" rows="4" required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="experience">Experience</label>
                                <input type="text" class="form-control" name="experience" id="experience" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="department_uuid">Department</label>
                                <select class="form-control" name="department_uuid" required>
                                    <option value="">Select Department</option>
                                    <?php
                                    // Fetch departments from the departments table
                                    $stmt = $conn->prepare("SELECT uuid, name FROM departments");
                                    $stmt->execute();
                                    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($departments as $department) {
                                        echo "<option value='" . $department['uuid'] . "'>" . $department['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="employee_uuid">Employee Responsible</label>
                                <select class="form-control" name="employee_uuid" required>
                                    <option value="">Select Employee</option>
                                    <?php
                                    // Fetch employees from the employees table
                                    $stmt = $conn->prepare("SELECT uuid, first_name, last_name FROM employees");
                                    $stmt->execute();
                                    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    foreach ($employees as $employee) {
                                        echo "<option value='" . $employee['uuid'] . "'>" . $employee['first_name'] . " " . $employee['last_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="opening_date">Opening Date</label>
                                <input type="date" class="form-control" name="opening_date" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="closing_date">Closing Date</label>
                                <input type="date" class="form-control" name="closing_date" required>
                            </div>


                            <!-- Dynamic fields for vacancy details -->
                            <div id="vacancy-details">
                                <h4>Vacancy Details</h4>
                                <div class="vacancy-detail">
                                    <div class="form-group mb-3">
                                        <label for="program_general_uuid">Program General</label>
                                        <select class="form-control" name="program_general_uuid[]" required>
                                            <option value="">Select Program</option>
                                            <?php
                                            // Fetch programs from the program_general table
                                            $stmt = $conn->prepare("SELECT uuid, name FROM education_programs_generals");
                                            $stmt->execute();
                                            $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            foreach ($programs as $program) {
                                                echo "<option value='" . $program['uuid'] . "'>" . $program['name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="major_uuid">Major</label>
                                        <select class="form-control" name="major_uuid[]" required>
                                            <option value="">Select Major</option>
                                            <?php
                                            // Fetch majors from the majors table
                                            $stmt = $conn->prepare("SELECT uuid, name FROM education_programs_details");
                                            $stmt->execute();
                                            $majors = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            foreach ($majors as $major) {
                                                echo "<option value='" . $major['uuid'] . "'>" . $major['name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary mb-3" id="add-detail">Add Another Detail</button>

                            <button type="submit" class="btn btn-primary">Add Vacancy</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('add-detail').addEventListener('click', function() {
            var detailsContainer = document.getElementById('vacancy-details');
            var newDetail = detailsContainer.querySelector('.vacancy-detail').cloneNode(true);
            detailsContainer.appendChild(newDetail);
        });
    </script>

    <?php
    include('layout/footer.php');
} else {
    header("Location: login.php");
    exit();
}
?>