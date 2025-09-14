<?php
session_start();
include('../conn/conn.php');

// Handle ADD NEW PERFORMANCE
if (
    isset($_POST['intern_id']) &&
    isset($_POST['kpi_id']) &&
    isset($_POST['score']) &&
    isset($_POST['comments']) &&
    isset($_POST['evaluator_id']) &&
    !isset($_POST['edit_button'])
) {
    // Get form inputs
    $intern_id = $_POST['intern_id'];
    $kpi_id = $_POST['kpi_id'];
    $score = intval($_POST['score']); // Convert to integer
    $comments = $_POST['comments'];
    $evaluator_id = $_POST['evaluator_id'];
    $created_at = date('Y-m-d H:i:s');

    // Generate UUID for the performance record
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

    $performance_id = generateUUID();

    try {
        // Prepare SQL insert statement
        $stmt = $conn->prepare("INSERT INTO `intern_performance` 
             (`performance_id`, `intern_id`, `kpi_id`, `score`, `comments`, `evaluator_id`, `created_at`) 
             VALUES (:performance_id, :intern_id, :kpi_id, :score, :comments, :evaluator_id, :created_at)");

        // Bind parameters
        $stmt->bindParam(':performance_id', $performance_id);
        $stmt->bindParam(':intern_id', $intern_id);
        $stmt->bindParam(':kpi_id', $kpi_id);
        $stmt->bindParam(':score', $score);
        $stmt->bindParam(':comments', $comments);
        $stmt->bindParam(':evaluator_id', $evaluator_id);
        $stmt->bindParam(':created_at', $created_at);

        // Execute the statement
        $stmt->execute();

        // Redirect with success message
        echo "<script>
                alert('Intern Performance added successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/intern_perfomance_admin_list.php';
              </script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle EDIT PERFORMANCE
elseif (isset($_POST['edit_button'])) {
    $performance_id = $_POST['performance_id'];
    $score = intval($_POST['score']); // Convert to integer
    $comments = $_POST['comments'];

    try {
        // Update the performance record
        $stmt = $conn->prepare("UPDATE `intern_performance` 
                                SET score = :score, comments = :comments
                                WHERE performance_id = :performance_id");
        $stmt->bindParam(':score', $score);
        $stmt->bindParam(':comments', $comments);
        $stmt->bindParam(':performance_id', $performance_id);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Performance updated successfully!');
                    window.location.href = '../intern_perfomance_admin_list.php';
                  </script>";
        } else {
            echo "Error updating performance!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle missing fields
else {
    echo "<script>alert('All fields are required!'); window.history.back();</script>";
}
?>