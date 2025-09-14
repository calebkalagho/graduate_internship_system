<?php
session_start();
include('../conn/conn.php');

// Check if the form has been submitted
if (isset($_POST['goal_name']) && isset($_POST['goal_description']) && isset($_POST['department_uuid'])) {
    // Get form inputs
    $goal_name = $_POST['goal_name'];
    $goal_description = $_POST['goal_description'];
    $department_uuid = $_POST['department_uuid'];
    $userID = $_POST['added_by'];

    // Generate UUID for the goal
    function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x%04x%04x%04x',
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

    $uuid = generateUUID();
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    try {
        // Prepare SQL insert statement
        $stmt = $conn->prepare("INSERT INTO `goals` (`goal_id`, `goal_name`, `goal_description`, `dept_id`,`added_by`, `created_at`) 
                                VALUES (:goal_id, :goal_name, :goal_description, :dept_id,:added_by, :created_at)");

        // Bind parameters
        $stmt->bindParam(':goal_id', $uuid);
        $stmt->bindParam(':goal_name', $goal_name);
        $stmt->bindParam(':goal_description', $goal_description);
        $stmt->bindParam(':dept_id', $department_uuid);
        $stmt->bindParam(':added_by',$userID );
        $stmt->bindParam(':created_at', $created_at);


        // Execute the statement
        $stmt->execute();

        // Redirect or show success message
        echo "
            <script>
                alert('Goal added successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/goal_list_admin.php';
            </script>
            ";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "<script>alert('All fields are required!'); window.history.back();</script>";
}


if (!isset($_POST['edit_button']) ) {
    echo "<script>alert('All fields are required!'); window.history.back();</script>";
    exit();
}

$goal_id = $_POST['goal_id'];
$goal_name = $_POST['goal_name'];
$goal_description = $_POST['goal_description'];
$department_uuid = $_POST['department_uuid'];
$updated_at = date('Y-m-d H:i:s');

try {
    $stmt = $conn->prepare("UPDATE `goals` SET `goal_name` = :goal_name, `goal_description` = :goal_description, `dept_id` = :dept_id WHERE `goal_id` = :goal_id");

    $stmt->bindParam(':goal_id', $goal_id);
    $stmt->bindParam(':goal_name', $goal_name);
    $stmt->bindParam(':goal_description', $goal_description);
    $stmt->bindParam(':dept_id', $department_uuid);


    $stmt->execute();

    echo "<script>alert('Goal updated successfully!'); window.location.href = 'http://localhost/graduate_internship_system/goal_list_admin.php';</script>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>