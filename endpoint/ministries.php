<?php
session_start();
include('../conn/conn.php');


// Check if the form has been submitted
if (isset($_POST['add_ministry'])) {
    // Get form inputs
    $name = $_POST['name'];
    $description = $_POST['description'];


    // Generate UUID for the ministries
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

    // Generate UUID for this ministries
    $uuid = generateUUID();
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    try {
        // Prepare the SQL insert statement
        $stmt = $conn->prepare("INSERT INTO `ministries` (`uuid`, `name`, `description`,`created_at`, `updated_at`)
                                VALUES (:uuid, :name, :description, :created_at, :updated_at)");

        // Bind parameters
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);

        $stmt->bindParam(':created_at', $created_at);
        $stmt->bindParam(':updated_at', $updated_at);

        // Execute the statement
        $stmt->execute();

        // Redirect or show success message
        echo "
            <script>
                alert('ministry added successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/ministries_list.php';
            </script>
            ";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
