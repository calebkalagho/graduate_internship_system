<?php
session_start();
include('../conn/conn.php');

// Check if the form has been submitted
if (isset($_POST['objective_name']) && isset($_POST['objective_description']) && isset($_POST['department_uuid'])) {
    // Get form inputs
    $objective_name = $_POST['objective_name'];
    $goal_description = $_POST['objective_description'];
    $department_uuid = $_POST['department_uuid'];
    $goal_id = $_POST['goal_id'];
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
        $stmt = $conn->prepare("INSERT INTO `performance_objectives` (`objective_id`,`goal_id`, `objective_name`, `objective_description`, `dept_id`,`added_by`, `created_at`) 
                                VALUES (:objective_id,:goal_id, :objective_name, :goal_description, :dept_id,:added_by, :created_at)");

        // Bind parameters
        $stmt->bindParam(':objective_id', $uuid);
        $stmt->bindParam(':goal_id', $goal_id);
        $stmt->bindParam(':objective_name', $objective_name);
        $stmt->bindParam(':goal_description', $objective_description);
        $stmt->bindParam(':dept_id', $department_uuid);
        $stmt->bindParam(':added_by',$userID );
        $stmt->bindParam(':created_at', $created_at);


        // Execute the statement
        $stmt->execute();

        // Redirect or show success message
        echo "
            <script>
                alert('Goal added successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/objectives_admin_list.php';
            </script>
            ";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "<script>alert('All fields are required!'); window.history.back();</script>";
}


if (isset($_POST['edit_button'])) {
    $objective_id = $_POST['objective_id'];
    $objective_name = $_POST['objective_name'];
    $objective_description = $_POST['objective_description'];
    $department_uuid = $_POST['department_uuid'];


    // Update the objective
    $stmt = $conn->prepare("UPDATE performance_objectives SET objective_name = :name, objective_description = :description, dept_id = :dept WHERE objective_id = :objective_id");
    $stmt->bindParam(':name', $objective_name);
    $stmt->bindParam(':description', $objective_description);
    $stmt->bindParam(':dept', $department_uuid);
    $stmt->bindParam(':objective_id', $objective_id);

    if ($stmt->execute()) {
        header("Location: ../details_objective.php?objective_id=" . $objective_id);
        exit();
    } else {
        echo "Error updating objective.";
    }
} else {
    echo "Invalid request!";
}
?>