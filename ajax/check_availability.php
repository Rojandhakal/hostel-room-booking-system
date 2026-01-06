<?php
// search.php
require_once 'config/database.php';
require_once 'includes/header.php';

$conn = getDB();

$params = [];
$conditions = ["r.status = 'Available'"];

// Build search conditions
if (isset($_GET['search'])) {
    // Room type filter
    if (!empty($_GET['room_type'])) {
        $conditions[] = "r.room_type = :room_type";
        $params[':room_type'] = $_GET['room_type'];
    }
    
    // Capacity filter
    if (!empty($_GET['capacity'])) {
        $conditions[] = "r.capacity >= :capacity";
        $params[':capacity'] = (int)$_GET['capacity'];
    }
    
    // Price filter
    if (!empty($_GET['max_price'])) {
        $conditions[] = "r.price <= :max_price";
        $params[':max_price'] = (float)$_GET['max_price'];
    }
    
    // Build the query
    $sql = "SELECT * FROM rooms r WHERE " . implode(" AND ", $conditions) . " ORDER BY r.price ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $rooms = $stmt->fetchAll();
    
    $search_performed = true;
} else {
    // Default: show all available rooms
    $rooms = $conn->query("SELECT * FROM rooms WHERE status = 'Available' ORDER BY price ASC")->fetchAll();
    $search_performed = false;
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0"><i class="bi bi-search"></i> Search Rooms</h4>
            </div>
            <div class="card-body">
                <form method="GET" class="search-form">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Room Type</label>
                            <select name="room_type" class="form-select">
                                <option value="">Any Type</option>
                                <option value="Single" <?php echo ($_GET['room_type'] ?? '') == 'Single' ? 'selected' : ''; ?>>Single</option>
                                <option value="Double" <?php echo ($_GET['room_type'] ?? '') == 'Double' ? 'selected' : ''; ?>>Double</option>
                                <option value="Dorm" <?php echo ($_GET['room_type'] ?? '') == 'Dorm' ? 'selected' : ''; ?>>Dorm</option>
                                <option value="Suite" <?php echo ($_GET['room_type'] ?? '') == 'Suite' ? 'selected' : ''; ?>>Suite</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Min Capacity</label>
                            <input type="number" name="capacity" class="form-control" min="1" max="10"
                                   value="<?php echo htmlspecialchars($_GET['capacity'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Max Price ($/night)</label>
                            <input type="number" name="max_price" class="form-control" min="0" step="0.01"
                                   value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" name="search" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Results -->
                <div class="mt-4">
                    <h5>Available Rooms 
                        <?php if ($search_performed): ?>
                            <span class="badge bg-success"><?php echo count($rooms); ?> found</span>
                        <?php endif; ?>
                    </h5>
                    
                    <?php if (empty($rooms)): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            No rooms found matching your criteria. Try different search options.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($rooms as $room): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                Room <?php echo htmlspecialchars($room['room_number']); ?>
                                                <span class="badge bg-primary float-end">
                                                    $<?php echo number_format($room['price'], 2); ?>/night
                                                </span>
                                            </h5>
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <?php echo htmlspecialchars($room['room_type']); ?> Room
                                            </h6>
                                            <p class="card-text">
                                                <i class="bi bi-people"></i> Capacity: <?php echo $room['capacity']; ?> person(s)<br>
                                                <i class="bi bi-circle-fill text-success"></i> Status: Available
                                            </p>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="/FullStack/hostel-booking/controllers/bookings/book.php?room_id=<?php echo $room['room_id']; ?>" 
                                               class="btn btn-success btn-sm">
                                                <i class="bi bi-calendar-check"></i> Book Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>