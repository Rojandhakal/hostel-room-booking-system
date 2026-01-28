<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/style.css">
<title>Admin Panel</title>
</head>
<body>
<div class="container">
<nav>
<a href="dashboard.php">Home</a>
<a href="rooms.php">Rooms</a>
<a href="bookings.php">Bookings</a>
<a href="occupants.php">Occupants</a>
<a href="search.php">Search Rooms</a>
<a href="../auth/logout.php">Logout</a>
</nav>
