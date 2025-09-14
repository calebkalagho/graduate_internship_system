<?php
session_start();
include('../conn/conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $name = $_POST['name'];
    $description = $_POST['description'];
    $post_address = $_POST['post_address'];
    $physical_address = $_POST['physical_address'];
    $contacts = $_POST['contacts'];
    $email_address = $_POST['email_address'];
    $district_id = $_POST['district_id'];
    
    // Check if this is an edit (uuid is present) or a new institution
    if (isset($_POST['uuid']) && !empty($_POST['uuid'])) {
        // This is an EDIT operation
        $uuid = $_POST['uuid'];
        $updated_at = date('Y-m-d H:i:s');
        
        try {
            // Prepare SQL update
            $stmt = $conn->prepare("UPDATE `institutions` SET 
                                    `name` = :name, 
                                    `description` = :description, 
                                    `post_address` = :post_address, 
                                    `physical_address` = :physical_address, 
                                    `contacts` = :contacts, 
                                    `email_address` = :email_address, 
                                    `district_id` = :district_id, 
                                    `updated_at` = :updated_at 
                                    WHERE `uuid` = :uuid");
            
            // Bind parameters
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':post_address', $post_address);
            $stmt->bindParam(':physical_address', $physical_address);
            $stmt->bindParam(':contacts', $contacts);
            $stmt->bindParam(':email_address', $email_address);
            $stmt->bindParam(':district_id', $district_id);
            $stmt->bindParam(':updated_at', $updated_at);
            $stmt->bindParam(':uuid', $uuid);
            
            // Execute
            if ($stmt->execute()) {
                echo "
                    <script>
                        alert('Institution updated successfully!');
                        window.location.href = '../institution_list.php';
                    </script>
                ";
            } else {
                echo "Failed to update institution.";
            }
            
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    } else {
        // This is an ADD operation (new institution)
        
        // Generate UUID
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
        
        $institution_uuid = generateUUID();
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        
        try {
            // Prepare SQL insert
            $stmt = $conn->prepare("INSERT INTO `institutions` (`uuid`, `name`, `description`, `post_address`, `physical_address`, `contacts`, `email_address`, `district_id`, `created_at`, `updated_at`)
                                    VALUES (:uuid, :name, :description, :post_address, :physical_address, :contacts, :email_address, :district_id, :created_at, :updated_at)");
            
            // Bind parameters
            $stmt->bindParam(':uuid', $institution_uuid);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':post_address', $post_address);
            $stmt->bindParam(':physical_address', $physical_address);
            $stmt->bindParam(':contacts', $contacts);
            $stmt->bindParam(':email_address', $email_address);
            $stmt->bindParam(':district_id', $district_id);
            $stmt->bindParam(':created_at', $created_at);
            $stmt->bindParam(':updated_at', $updated_at);
            
            // Execute
            if ($stmt->execute()) {
                echo "
                    <script>
                        alert('Institution added successfully!');
                        window.location.href = '../institution_list.php';
                    </script>
                ";
            } else {
                echo "Failed to add institution.";
            }
            
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo "Invalid request.";
}
?>