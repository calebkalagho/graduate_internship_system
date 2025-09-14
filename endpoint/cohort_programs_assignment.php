<?php
session_start();
include('../conn/conn.php');

// Check if the form has been submitted
if (isset($_POST['add_cohort_assignment'])) {
    // Get form inputs
    $mda_type = $_POST['mda_type'];
    $department_uuid = $_POST['department'];
    $general_uuid = $_POST['general_uuid'];
    $total_recruits = $_POST['total_recruits'];
    $gender_preference = $_POST['gender_preference'];
    $total_male = $_POST['total_male'];
    $total_female = $_POST['total_female'];

    // Generate UUID for the cohort program assignment
    function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    // Prepare to insert into cohort_program_assignments
    $assignment_uuid = generateUUID();
    $created_at = date('Y-m-d H:i:s');

    try {

        // Query to get the active cohort's UUID
        $stmt = $conn->prepare("SELECT uuid FROM cohort_programs WHERE status = 'active' LIMIT 1");
        $stmt->execute();

        // Check if a cohort program is found
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $active_cohort_uuid = $row['uuid'];
        } else {
            // Handle case when no active cohort is found
            throw new Exception('No active cohort program found');
        }
        // Prepare the SQL insert statement for cohort_program_assignments
        $stmt = $conn->prepare("INSERT INTO `cohort_program_assignments` (`uuid`, `cohort_program_uuid`, `department_uuid`, `created_at`)
                                VALUES (:uuid, :cohort_program_uuid, :department_uuid, :created_at)");

        // Assuming you already have the cohort_program_uuid (e.g. from a session or selection)




        // Bind parameters
        $stmt->bindParam(':uuid', $assignment_uuid);
        $stmt->bindParam(':cohort_program_uuid',  $active_cohort_uuid);
        $stmt->bindParam(':department_uuid', $department_uuid);
        $stmt->bindParam(':created_at', $created_at);

        // Execute the query for cohort_program_assignments
        if ($stmt->execute()) {
            // Insert into cohort_program_assignment_details
            $assignment_detail_uuid = generateUUID();
            $updated_at = date('Y-m-d H:i:s');

            // Prepare the SQL insert statement for cohort_program_assignment_details
            $detail_stmt = $conn->prepare("INSERT INTO `cohort_program_assignment_details` (`uuid`, `assignment_uuid`, `general_uuid`, `major_uuid`, `total_recruits`, `gender_preference`, `total_male`, `total_female`, `created_at`, `updated_at`)
                                            VALUES (:uuid, :assignment_uuid, :general_uuid, :major_uuid, :total_recruits, :gender_preference, :total_male, :total_female, :created_at, :updated_at)");

            // Assuming you have major_uuid from your form (you may need to adapt this)
            $major_uuid = isset($_POST['major_uuid']) ? $_POST['major_uuid'] : null; // Replace with actual major UUID if necessary

            // Bind parameters for the details
            $detail_stmt->bindParam(':uuid', $assignment_detail_uuid);
            $detail_stmt->bindParam(':assignment_uuid', $assignment_uuid);
            $detail_stmt->bindParam(':general_uuid', $general_uuid);
            $detail_stmt->bindParam(':major_uuid', $major_uuid);
            $detail_stmt->bindParam(':total_recruits', $total_recruits);
            $detail_stmt->bindParam(':gender_preference', $gender_preference);
            $detail_stmt->bindParam(':total_male', $total_male);
            $detail_stmt->bindParam(':total_female', $total_female);
            $detail_stmt->bindParam(':created_at', $created_at);
            $detail_stmt->bindParam(':updated_at', $updated_at);

            // Execute the query for cohort_program_assignment_details
            if ($detail_stmt->execute()) {
                echo "New cohort assignment added successfully.";
                echo "
                <script>
                    alert('Cohort assignment submitted successfully!');
                    window.location.href = 'http://localhost/graduate_internship_system/cohort_assignments_list.php'; // Update with your redirect page
                </script>
                ";
            } else {
                echo "Error adding cohort assignment details.";
            }
        } else {
            echo "Error adding cohort assignment.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
