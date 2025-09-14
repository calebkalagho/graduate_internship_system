<?php
// Database credentials
$host = 'localhost';
$db_name = 'multi_role_login_db'; // Change this to your database name
$username = 'root';
$password = '';

// Create connection
$conn = mysqli_connect($host, $username, $password, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
