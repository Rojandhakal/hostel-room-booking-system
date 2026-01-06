<?php
// index.php
require_once 'config/database.php';
require_once 'includes/header.php';

$conn = getDB();

// Get statistics
$stats = [];
try {
    // Total rooms
    $stmt = $conn->query("SELECT COUNT(*) as total FROM rooms");
    $stats['total_rooms'] = $stmt->fetch()['total'];

    // Available rooms
    $stmt = $conn->query("SELECT COUNT(*) as available FROM rooms WHERE status = 'Available'");
    $stats['available_rooms'] = $stmt->fetch()['available'];

    // Total bookings
    $stmt = $conn->query("SELECT COUNT(*) as bookings FROM bookings");
    $stats['total_bookings'] = $stmt->fetch()['bookings'];

    // Total occupants
    $stmt = $conn->query("SELECT COUNT(*) as occupants FROM occupants");
    $stats['total_occupants'] = $stmt->fetch()['occupants'];
} catch(PDOException $e) {
    // Handle error silently or show a message
    $stats = [
        'total_rooms' => 0,
        'available_rooms' => 0,
        'total_bookings' => 0,
        'total_occupants' => 0
    ];
}
?>

<div class="hero-section text-center py-5 mb-5 bg-primary text-white rounded">
    <h1 class="display-4">🏨 Hostel Booking System</h1>
    <p class="lead">Manage room availability and bookings with ease</p>
    <a href="/FullStack/hostel-booking/controllers/rooms/bookings/book.php" class="btn btn-light btn-lg mt-3">
        <i class="bi bi-calendar-check"></i> Book a Room Now
    </a>
</div>

<!-- Quick Stats -->
<div class="row mb-5">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-door-closed display-4 text-primary"></i>
                <h2><?php echo $stats['total_rooms']; ?></h2>
                <p class="card-text">Total Rooms</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-check-circle display-4 text-success"></i>
                <h2><?php echo $stats['available_rooms']; ?></h2>
                <p class="card-text">Available Rooms</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar display-4 text-info"></i>
                <h2><?php echo $stats['total_bookings']; ?></h2>
                <p class="card-text">Total Bookings</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-people display-4 text-warning"></i>
                <h2><?php echo $stats['total_occupants']; ?></h2>
                <p class="card-text">Total Guests</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-door-closed-fill display-1 text-primary"></i>
                <h4>Manage Rooms</h4>
                <p>View, add, edit, or delete rooms</p>
                <a href="/FullStack/hostel-booking/controllers/rooms/list.php" class="btn btn-primary">View Rooms</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-calendar-check-fill display-1 text-success"></i>
                <h4>Make Booking</h4>
                <p>Book a room for students/guests</p>
                <a href="/FullStack/hostel-booking/bookings/book.php" class="btn btn-success">Book Now</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-search display-1 text-info"></i>
                <h4>Search Rooms</h4>
                <p>Find available rooms by criteria</p>
                <a href="/FullStack/hostel-booking/search.php" class="btn btn-info">Search</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-list-check display-1 text-warning"></i>
                <h4>View Bookings</h4>
                <p>See all bookings</p>
                <a href="/FullStack/hostel-booking/bookings/manage.php" class="btn btn-warning">View</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Search -->
<div class="card mt-5">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-search"></i> Quick Room Search</h5>
    </div>
    <div class="card-body">
        <form action="/FullStack/hostel-booking/search.php" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Room Type</label>
                <select name="room_type" class="form-select">
                    <option value="">Any Type</option>
                    <option value="Single">Single</option>
                    <option value="Double">Double</option>
                    <option value="Dorm">Dorm</option>
                    <option value="Suite">Suite</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Min Capacity</label>
                <input type="number" name="capacity" class="form-control" min="1" max="10" placeholder="e.g., 2">
            </div>
            <div class="col-md-3">
                <label class="form-label">Max Price ($)</label>
                <input type="number" name="max_price" class="form-control" min="0" step="0.01" placeholder="e.g., 100">
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>