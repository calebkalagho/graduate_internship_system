<?php
session_start();
include('../conn/conn.php');


// Check if the form has been submitted
if (isset($_POST['add_role'])) {
    // Get form inputs
    $name = $_POST['name'];
    $description = $_POST['description'];



    // Hash the password before storing it
    $hashed_password = sha1($password, PASSWORD_DEFAULT);

    // Generate UUID for the roles
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

    // Generate UUID for this roles
    $uuid = generateUUID();
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    try {
        // Prepare the SQL insert statement
        $stmt = $conn->prepare("INSERT INTO `roles` (`uuid`, `name`, `description`,`created_at`, `updated_at`)
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
                alert('Role added successfully!');
                window.location.href = 'http://localhost/graduate_internship_system/roles_list.php';
            </script>
            ";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


// Check if the form has been submitted
if (isset($_POST['update_role'])) {
    // Get form inputs
    $role_id = $_POST['role_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    try {
        // Prepare the SQL update statement
        $stmt = $conn->prepare("UPDATE `roles` SET 
            `name` = :name,
            `description` = :description
            WHERE `uuid` = :role_id");

        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':role_id', $role_id);

        // Execute the query
        if ($stmt->execute()) {
            echo "
            <script>
                alert('Role updated successfully!');
                window.location.href = '../roles_list.php';
            </script>
            ";
        } else {
            echo "
            <script>
                alert('Error updating role.');
                window.location.href = '../roles_list.php';
            </script>
            ";
        }
    } catch (PDOException $e) {
        echo "
        <script>
            alert('Error: " . $e->getMessage() . "');
            window.location.href = '../roles_list.php';
        </script>
        ";
    }
} else {
    // If the form wasn't submitted, redirect to the roles list
    header("Location: ../roles_list.php");
    exit();
}

