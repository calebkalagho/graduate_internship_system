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
    }

    include('layout/header.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Insert vacancy into 'vacancies' table
        $vacancy_uuid = uniqid();
        $vacancy_title = $_POST['vacancy_title'];
        $summary = $_POST['summary'];
        $description = $_POST['description'];
        $duties = $_POST['duties'];
        $qualifications = $_POST['qualifications'];
        $experience = $_POST['experience'];
        $department_uuid = $_POST['department_uuid'];
        $opening_date = $_POST['opening_date'];
        $closing_date = $_POST['closing_date'];
        $employee_uuid = $_POST['employee_uuid'];
        $status = $_POST['status'];
        $type = $_POST['type'];

        // Insert vacancy query
        $stmt = $conn->prepare("INSERT INTO `vacancies` (uuid, vacancy_title, summary, description, duties, qualifications, experience, department_uuid, opening_date, closing_date, employee_uuid, created_at, updated_at, status, type) 
                            VALUES (:uuid, :vacancy_title, :summary, :description, :duties, :qualifications, :experience, :department_uuid, :opening_date, :closing_date, :employee_uuid, NOW(), NOW(), :status, :type)");
        $stmt->bindParam(':uuid', $vacancy_uuid);
        $stmt->bindParam(':vacancy_title', $vacancy_title);
        $stmt->bindParam(':summary', $summary);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':duties', $duties);
        $stmt->bindParam(':qualifications', $qualifications);
        $stmt->bindParam(':experience', $experience);
        $stmt->bindParam(':department_uuid', $department_uuid);
        $stmt->bindParam(':opening_date', $opening_date);
        $stmt->bindParam(':closing_date', $closing_date);
        $stmt->bindParam(':employee_uuid', $employee_uuid);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':type', $type);
        $stmt->execute();

        // Insert vacancy details into 'vacancy_details' table
        if (isset($_POST['program_general_uuid']) && isset($_POST['major_uuid'])) {
            foreach ($_POST['program_general_uuid'] as $index => $program_general_uuid) {
                $vacancy_detail_uuid = uniqid();
                $major_uuid = $_POST['major_uuid'][$index];

                $stmt = $conn->prepare("INSERT INTO `vacancy_details` (uuid, vacancy_uuid, program_general_uuid, major_uuid, created_at, updated_at) 
                                    VALUES (:uuid, :vacancy_uuid, :program_general_uuid, :major_uuid, NOW(), NOW())");
                $stmt->bindParam(':uuid', $vacancy_detail_uuid);
                $stmt->bindParam(':vacancy_uuid', $vacancy_uuid);
                $stmt->bindParam(':program_general_uuid', $program_general_uuid);
                $stmt->bindParam(':major_uuid', $major_uuid);
                $stmt->execute();
            }
        }

        echo "Vacancy and details added successfully!";
    }
?>

    <form method="POST">
        <!-- Vacancy Fields -->
        <h3>Add Vacancy</h3>
        <div>
            <label>Vacancy Title:</label>
            <input type="text" name="vacancy_title" required />
        </div>
        <div>
            <label>Summary:</label>
            <textarea name="summary" required></textarea>
        </div>
        <div>
            <label>Description:</label>
            <textarea name="description" required></textarea>
        </div>
        <div>
            <label>Duties:</label>
            <textarea name="duties" required></textarea>
        </div>
        <div>
            <label>Qualifications:</label>
            <textarea name="qualifications" required></textarea>
        </div>
        <div>
            <label>Experience:</label>
            <input type="text" name="experience" required />
        </div>
        <div>
            <label>Department UUID:</label>
            <input type="text" name="department_uuid" required />
        </div>
        <div>
            <label>Opening Date:</label>
            <input type="date" name="opening_date" required />
        </div>
        <div>
            <label>Closing Date:</label>
            <input type="date" name="closing_date" required />
        </div>
        <div>
            <label>Employee UUID:</label>
            <input type="text" name="employee_uuid" required />
        </div>
        <div>
            <label>Status:</label>
            <input type="text" name="status" required />
        </div>
        <div>
            <label>Type:</label>
            <input type="text" name="type" required />
        </div>

        <!-- Dynamic Vacancy Details Section -->
        <h3>Vacancy Details</h3>
        <div id="vacancy-details-container">
            <div class="vacancy-detail-row">
                <div>
                    <label>Program General UUID:</label>
                    <input type="text" name="program_general_uuid[]" required />
                </div>
                <div>
                    <label>Major UUID:</label>
                    <input type="text" name="major_uuid[]" required />
                </div>
                <button type="button" class="remove-detail-btn">Remove</button>
            </div>
        </div>
        <button type="button" id="add-detail-btn">Add More Details</button>

        <div>
            <button type="submit">Submit Vacancy</button>
        </div>
    </form>

    <script>
        // JavaScript to dynamically add/remove vacancy details fields
        document.getElementById('add-detail-btn').addEventListener('click', function() {
            var container = document.getElementById('vacancy-details-container');
            var newRow = document.createElement('div');
            newRow.classList.add('vacancy-detail-row');
            newRow.innerHTML = `
        <div>
            <label>Program General UUID:</label>
            <input type="text" name="program_general_uuid[]" required />
        </div>
        <div>
            <label>Major UUID:</label>
            <input type="text" name="major_uuid[]" required />
        </div>
        <button type="button" class="remove-detail-btn">Remove</button>
    `;
            container.appendChild(newRow);

            // Add event listener to the newly added remove button
            newRow.querySelector('.remove-detail-btn').addEventListener('click', function() {
                newRow.remove();
            });
        });

        // Remove existing row on click
        document.querySelectorAll('.remove-detail-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                button.closest('.vacancy-detail-row').remove();
            });
        });
    </script>


<?php
    include('layout/footer.php');
} else {
    header("Location: http://localhost/graduate_internship_system");
}
?>