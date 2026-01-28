<?php
include '../includes/admin_header.php';
?>
<div class="hero">
<h1>Welcome, <?= $_SESSION['full_name'] ?></h1>
<p class="tagline">Manage rooms, bookings, and occupants</p>
</div>
<div class="dashboard">
<div class="card">
<h3>Rooms</h3>
<a href="rooms.php">Manage Rooms</a>
</div>
<div class="card">
<h3>Bookings</h3>
<a href="bookings.php">View Bookings</a>
</div>
<div class="card">
<h3>Occupants</h3>
<a href="occupants.php">View Occupants</a>
</div>
</div>
<?php include '../includes/footer.php'; ?>
