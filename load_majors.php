<?php
// Start the session and include the database connection
session_start();
include('./conn/conn.php');

// Check if the general_uuid is provided
if (isset($_GET['general_uuid'])) {
    $general_uuid = $_GET['general_uuid'];

    try {
        // Prepare the SQL statement to fetch majors based on general_uuid
        $stmt = $conn->prepare("SELECT `uuid`, `name` FROM `education_programs_details` WHERE `general_pg_uuid` = :general_pg_uuid");
        $stmt->bindParam(':general_pg_uuid', $general_uuid);
        $stmt->execute();

        // Fetch the results as an associative array
        $majors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the results as a JSON response
        echo json_encode($majors);
    } catch (PDOException $e) {
        // Handle any errors during the database query
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    // If general_uuid is not provided, return an empty array
    echo json_encode([]);
}
