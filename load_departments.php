<?php
include('./conn/conn.php');

// Check if ministry_uuid or institution_uuid is passed in the URL
$ministry_uuid = isset($_GET['ministry_uuid']) ? $_GET['ministry_uuid'] : null;
$institution_uuid = isset($_GET['institution_uuid']) ? $_GET['institution_uuid'] : null;

if ($ministry_uuid) {
    // Fetch departments based on the selected ministry
    $stmt = $conn->prepare("SELECT uuid, name FROM departments WHERE ministry_uuid = :ministry_uuid ORDER BY name");
    $stmt->bindParam(':ministry_uuid', $ministry_uuid);
} elseif ($institution_uuid) {
    // Fetch departments based on the selected institution
    $stmt = $conn->prepare("SELECT uuid, name FROM departments WHERE da_uuid = :da_uuid ORDER BY name");
    $stmt->bindParam(':da_uuid', $institution_uuid);
} else {
    // If no ministry or institution is selected, return an empty array
    echo json_encode([]);
    exit();
}

$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the departments as JSON
echo json_encode($departments);
