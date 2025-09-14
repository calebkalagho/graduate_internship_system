<?php
include('./conn/conn.php');

// Fetch all ministries from the database
$stmt = $conn->prepare("SELECT uuid, name FROM ministries ORDER BY name");
$stmt->execute();
$ministries = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the ministries as JSON
echo json_encode($ministries);
