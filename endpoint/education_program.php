<?php
session_start();
include('../conn/conn.php');

// Check if the form has been submitted
if (isset($_POST['add_program'])) {
    // Get form inputs

    $general_name = $_POST['program_general_name'];
    $general_description = $_POST['program_general_description'];

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
    $general_uuid = generateUUID();
    $details_uuid = generateUUID();
    $created_at = date('Y-m-d H:i:s');

    try {


        $stmt_general = $conn->prepare("INSERT INTO `education_programs_generals` (uuid, name, description, created_at, updated_at) VALUES (:uuid, :name, :description, NOW(), NOW())");
        $stmt_general->bindParam(':uuid', $general_uuid);
        $stmt_general->bindParam(':name', $general_name);
        $stmt_general->bindParam(':description', $general_description);
        $stmt_general->execute();

        // Insert into `education_programs_details`
        $detail_names = $_POST['program_detail_name'];
        $detail_descriptions = $_POST['program_detail_description'];

        foreach ($detail_names as $index => $detail_name) {
            $detail_uuid = generateUUID();
            $detail_description = $detail_descriptions[$index];

            $stmt_detail = $conn->prepare("INSERT INTO `education_programs_details` (uuid, general_pg_uuid, name, description, created_at, updated_at) VALUES (:uuid, :general_pg_uuid, :name, :description, NOW(), NOW())");
            $stmt_detail->bindParam(':uuid', $detail_uuid);
            $stmt_detail->bindParam(':general_pg_uuid', $general_uuid);
            $stmt_detail->bindParam(':name', $detail_name);
            $stmt_detail->bindParam(':description', $detail_description);
            $stmt_detail->execute();
        }
        // Commit transaction
        echo "
                <script>
                    alert('Cohort assignment submitted successfully!');
                    window.location.href = 'http://localhost/graduate_internship_system/programs_general_list.php'; // Update with your redirect page
                </script>
                ";
    } catch (Exception $e) {
        // Rollback transaction if something goes wrong

        echo "Failed to insert data: " . $e->getMessage();
    }
} else {
    header('Location: ../add_program.php');
    exit();
}
