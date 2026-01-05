<?php
$host = "localhost";
$dbname = "hostel_booking";
$user = "root";       
$pass = "";            

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed");
}
?>
