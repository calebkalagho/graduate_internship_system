<?php
session_start();
include('../conn/conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['national_id'])) {
    $national_id = $_POST['national_id'];

    // Query to check if national_id exists
    $stmt = $conn->prepare("SELECT national_id FROM graduate WHERE national_id = ?");
    $stmt->bind_param("s", $national_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Send response back to AJAX
    if ($result->num_rows > 0) {
        echo json_encode(['exists' => true]);  // National ID already exists
    } else {
        echo json_encode(['exists' => false]); // National ID is unique
    }

    $stmt->close();
    $conn->close();
}
?>
