<?php
session_start();
include('../conn/conn.php');

// Check if the form has been submitted
if (
    isset($_POST['objective_id']) &&
    isset($_POST['kpi_name']) &&
    isset($_POST['kpi_description']) &&
    isset($_POST['kpi_weightage']) &&
    isset($_POST['min_target']) &&
    isset($_POST['max_target']) &&
    isset($_POST['measurement_unit']) &&
    isset($_POST['added_by'])
) {
    // Get form inputs
    $objective_id = $_POST['objective_id'];
    $kpi_name = $_POST['kpi_name'];
    $kpi_description = $_POST['kpi_description'];
    $kpi_weightage = $_POST['kpi_weightage'];
    $min_target = $_POST['min_target'];
    $max_target = $_POST['max_target'];
    $measurement_unit = $_POST['measurement_unit'];
    $added_by = $_POST['added_by'];

    // Generate UUID for the KPI
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

    $kpi_id = generateUUID();
    $created_at = date('Y-m-d H:i:s');

    try {
        // Prepare SQL insert statement
        $stmt = $conn->prepare("INSERT INTO `kpi_metrics` 
            (`kpi_id`, `objective_id`, `kpi_name`, `kpi_description`, `kpi_weightage`, `min_target`, `max_target`, `measurement_unit`, `added_by`, `created_at`) 
            VALUES (:kpi_id, :objective_id, :kpi_name, :kpi_description, :kpi_weightage, :min_target, :max_target, :measurement_unit, :added_by, :created_at)");

        // Bind parameters
        $stmt->bindParam(':kpi_id', $kpi_id);
        $stmt->bindParam(':objective_id', $objective_id);
        $stmt->bindParam(':kpi_name', $kpi_name);
        $stmt->bindParam(':kpi_description', $kpi_description);
        $stmt->bindParam(':kpi_weightage', $kpi_weightage);
        $stmt->bindParam(':min_target', $min_target);
        $stmt->bindParam(':max_target', $max_target);
        $stmt->bindParam(':measurement_unit', $measurement_unit);
        $stmt->bindParam(':added_by', $added_by);
        $stmt->bindParam(':created_at', $created_at);

        // Execute the statement
        $stmt->execute();

        // Redirect or show success message
        echo "<script>
                alert('KPI Metric added successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/kpi_admin_list.php';
              </script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "<script>alert('All fields are required!'); window.history.back();</script>";
}



if (isset($_POST['edit_button'])) {
    $kpi_id = $_POST['kpi_id'];
    $kpi_name = $_POST['kpi_name'];
    $kpi_description = $_POST['kpi_description'];
    $kpi_weightage = $_POST['kpi_weightage'];
    $min_target = $_POST['min_target'];
    $max_target = $_POST['max_target'];
    $measurement_unit = $_POST['measurement_unit'];
    $objective_id = $_POST['objective_id'];

    // Update KPI details
    $stmt = $conn->prepare("UPDATE kpi_metrics SET 
        kpi_name = :kpi_name, 
        kpi_description = :kpi_description, 
        kpi_weightage = :kpi_weightage, 
        min_target = :min_target, 
        max_target = :max_target, 
        measurement_unit = :measurement_unit, 
        objective_id = :objective_id 
        WHERE kpi_id = :kpi_id");

    $stmt->bindParam(':kpi_name', $kpi_name);
    $stmt->bindParam(':kpi_description', $kpi_description);
    $stmt->bindParam(':kpi_weightage', $kpi_weightage);
    $stmt->bindParam(':min_target', $min_target);
    $stmt->bindParam(':max_target', $max_target);
    $stmt->bindParam(':measurement_unit', $measurement_unit);
    $stmt->bindParam(':objective_id', $objective_id);
    $stmt->bindParam(':kpi_id', $kpi_id);

    if ($stmt->execute()) {
        header("Location: ../kpi_admin_list.php");
        exit();
    } else {
        echo "Error updating KPI details.";
    }
} else {
    echo "Invalid request!";
}

?>
