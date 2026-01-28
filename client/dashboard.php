<?php
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}
require '../config/database.php';
include '../includes/client_header.php';
?>

<div class="hero">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
    <p class="tagline">Search available rooms and make your booking</p>
</div>

<div class="dashboard">
    <div class="card">
        <h3>Search Rooms</h3>
        <p>Find available rooms based on type and capacity.</p>
        <a href="../shared/search.php">Go to Search</a>
    </div>
    <div class="card">
        <h3>My Bookings</h3>
        <p>View and manage your bookings.</p>
        <a href="bookings.php">Go to My Bookings</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
