<?php
include('./conn/conn.php');

// Fetch all institutions from the database
$stmt = $conn->prepare("SELECT uuid, name FROM institutions ORDER BY name");
$stmt->execute();
$institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the institutions as JSON
echo json_encode($institutions);
