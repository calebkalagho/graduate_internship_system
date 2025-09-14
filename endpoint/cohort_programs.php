<?php
session_start();
include('../conn/conn.php');

// Initialize the reference variable
$reference = '';

// Function to generate UUID
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

// Function to generate a reference in the format YYYYMMDD + random string
function generateReference()
{
    $date = date('Ymd'); // Current date in YYYYMMDD format
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $randomString = '';

    for ($i = 0; $i < 6; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $date . $randomString; // Combine date and random string
}

// ADD NEW COHORT PROGRAM
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['program_name']) && !isset($_POST['update_cohort_program'])) {
    // Get form inputs
    $program_name = $_POST['program_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $description = $_POST['description'];
    $terms_conditions = $_POST['terms_conditions'];

    // Generate UUID for the new cohort program
    $cohort_uuid = generateUUID();
    $reference = generateReference(); // Generate the reference
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');
    $status = 'active'; // Set the status for the new program

    try {
        // Start transaction
        $conn->beginTransaction();

        // First, deactivate all existing active cohort programs
        $deactivate_stmt = $conn->prepare("UPDATE `cohort_programs` SET `status` = 'inactive', `updated_at` = :updated_at WHERE `status` = 'active'");
        $deactivate_stmt->bindParam(':updated_at', $updated_at);
        $deactivate_stmt->execute();

        // Then, insert the new cohort program as active
        $stmt = $conn->prepare("INSERT INTO `cohort_programs` (`uuid`, `references`, `name`, `descriptions`, `start_date`, `end_date`, `terms_conditions`, `status`, `created_at`, `updated_at`) 
                                VALUES (:uuid, :references, :name, :descriptions, :start_date, :end_date, :terms_conditions, :status, :created_at, :updated_at)");

        // Bind parameters for the new cohort program
        $stmt->bindParam(':uuid', $cohort_uuid);
        $stmt->bindParam(':references', $reference);
        $stmt->bindParam(':name', $program_name);
        $stmt->bindParam(':descriptions', $description);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':terms_conditions', $terms_conditions);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':updated_at', $updated_at);

        // Execute the insert query for the new cohort program
        if ($stmt->execute()) {
            // Commit transaction
            $conn->commit();
            
            echo "
            <script>
                alert('Cohort program added successfully! Previous active cohorts have been deactivated. Reference: " . addslashes($reference) . "');
                window.location.href = '../cohort_program_list.php';
            </script>
            ";
        } else {
            $conn->rollback();
            echo "
            <script>
                alert('Error adding cohort program.');
                window.location.href = '../cohort_program_list.php';
            </script>
            ";
        }
    } catch (PDOException $e) {
        $conn->rollback();
        echo "
        <script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.location.href = '../cohort_program_list.php';
        </script>
        ";
    }
}

// UPDATE EXISTING COHORT PROGRAM
if (isset($_POST['update_cohort_program'])) {
    // Get form inputs
    $cohort_uuid = $_POST['cohort_uuid'];
    $program_name = $_POST['program_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $description = $_POST['description'];
    $terms_conditions = $_POST['terms_conditions'];
    $status = $_POST['status'];
    $updated_at = date('Y-m-d H:i:s');

    try {
        // Start transaction
        $conn->beginTransaction();

        // If setting this cohort to active, deactivate all other active cohorts first
        if ($status === 'active') {
            $deactivate_stmt = $conn->prepare("UPDATE `cohort_programs` SET `status` = 'inactive', `updated_at` = :updated_at WHERE `status` = 'active' AND `uuid` != :current_uuid");
            $deactivate_stmt->bindParam(':updated_at', $updated_at);
            $deactivate_stmt->bindParam(':current_uuid', $cohort_uuid);
            $deactivate_stmt->execute();
        }

        // Update the cohort program
        $stmt = $conn->prepare("UPDATE `cohort_programs` SET 
            `name` = :name, 
            `descriptions` = :descriptions, 
            `start_date` = :start_date, 
            `end_date` = :end_date, 
            `terms_conditions` = :terms_conditions, 
            `status` = :status, 
            `updated_at` = :updated_at 
            WHERE `uuid` = :uuid");

        // Bind parameters
        $stmt->bindParam(':name', $program_name);
        $stmt->bindParam(':descriptions', $description);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':terms_conditions', $terms_conditions);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':updated_at', $updated_at);
        $stmt->bindParam(':uuid', $cohort_uuid);

        // Execute the update query
        if ($stmt->execute()) {
            // Commit transaction
            $conn->commit();
            
            echo "
            <script>
                alert('Cohort program updated successfully!');
                window.location.href = '../cohort_program_list.php';
            </script>
            ";
        } else {
            $conn->rollback();
            echo "
            <script>
                alert('Error updating cohort program.');
                window.location.href = '../cohort_program_list.php';
            </script>
            ";
        }
    } catch (PDOException $e) {
        $conn->rollback();
        echo "
        <script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.location.href = '../cohort_program_list.php';
        </script>
        ";
    }
}

// CHANGE STATUS OF COHORT PROGRAM
if (isset($_POST['change_status'])) {
    $uuid = $_POST['uuid'];
    $status = $_POST['status'];
    $updated_at = date('Y-m-d H:i:s');

    try {
        // Start transaction
        $conn->beginTransaction();

        // If setting status to active, deactivate all other active cohorts first
        if ($status === 'active') {
            $deactivate_stmt = $conn->prepare("UPDATE `cohort_programs` SET `status` = 'inactive', `updated_at` = :updated_at WHERE `status` = 'active' AND `uuid` != :current_uuid");
            $deactivate_stmt->bindParam(':updated_at', $updated_at);
            $deactivate_stmt->bindParam(':current_uuid', $uuid);
            $deactivate_stmt->execute();
        }

        // Update the status of the selected cohort
        $stmt = $conn->prepare("UPDATE `cohort_programs` SET `status` = :status, `updated_at` = :updated_at WHERE `uuid` = :uuid");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':updated_at', $updated_at);
        $stmt->bindParam(':uuid', $uuid);

        if ($stmt->execute()) {
            // Commit transaction
            $conn->commit();
            
            $status_text = ucfirst($status);
            echo "
            <script>
                alert('Cohort program status changed to $status_text successfully!');
                window.location.href = '../cohort_program_list.php';
            </script>
            ";
        } else {
            $conn->rollback();
            echo "
            <script>
                alert('Error changing cohort program status.');
                window.location.href = '../cohort_program_list.php';
            </script>
            ";
        }
    } catch (PDOException $e) {
        $conn->rollback();
        echo "
        <script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.location.href = '../cohort_program_list.php';
        </script>
        ";
    }
}

// If no valid action was performed, redirect to the list page
if (!isset($_POST['program_name']) && !isset($_POST['update_cohort_program']) && !isset($_POST['change_status'])) {
    header("Location: ../cohort_program_list.php");
    exit();
}
?>