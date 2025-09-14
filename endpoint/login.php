<?php
include('../conn/conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM `graduate` WHERE `email` = :email");
    $stmt->bindParam(':email', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        $stored_password = $row['password'];
        $stored_role = $row['role'];
        $user_id = $row['id'];
        $graduate_uuid = $row['graduate_uuid'];

        if (sha1($password) === $stored_password) {
            session_start();
            $_SESSION['user_id'] = $user_id;

            if ($stored_role == 'admin') {
                echo "
                <script>
                    alert('Login Successfully!');
                    window.location.href = 'http://localhost/graduate_internship_system/admin.php';
                </script>
                ";
            } else if ($stored_role == 'graduate') {
                echo "
                <script>
                    alert('Login Successfully!');
                    window.location.href = 'http://localhost/graduate_internship_system/graduate.php';
                </script>
                ";
            } else if ($stored_role == 'hr') {
                echo "
                <script>
                    alert('Login Successfully!');
                    window.location.href = 'http://localhost/graduate_internship_system/hr.php';
                </script>
                ";
            }
        } else {
            echo "
            <script>
                alert('Login Failed, Incorrect Password!');
                window.location.href = 'http://localhost/graduate_internship_system/login.php';
            </script>
            ";
        }
    } else {
        echo "
            <script>
                alert('Login Failed, User Not Found!');
                window.location.href = 'http://localhost/graduate_internship_system/';
            </script>
            ";
    }
}
