<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
    <nav>
        <a href="../client/dashboard.php">Home</a>
        <a href="../shared/search.php">Search</a>
        <a href="../client/bookings.php">My Bookings</a>
        <a href="../auth/logout.php">Logout</a>
    </nav>
