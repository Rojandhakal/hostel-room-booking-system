<?php
// controllers/rooms/bookings/manage.php
require_once '../../../config/database.php';
require_once '../../../includes/header.php';

$conn = getDB();

// Get all bookings with room and occupant details
try {
    $stmt = $conn->prepare("
        SELECT 
            b.booking_id,
            b.check_in,
            b.check_out,
            r.room_number,
            r.room_type,
            r.price,
            o.full_name,
            o.email,
            o.phone
        FROM bookings b
        JOIN rooms r ON b.room_id = r.room_id
        JOIN occupants o ON b.occupant_id = o.occupant_id
        ORDER BY b.check_in DESC
    ");
    $stmt->execute();
    $bookings = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Error fetching bookings: " . $e->getMessage();
    $bookings = [];
}
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="bi bi-list-check"></i> View All Bookings</h4>
    </div>
    <div class="card-body">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No bookings found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Room</th>
                            <th>Guest</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Nights</th>
                            <th>Total Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): 
                            $check_in = new DateTime($booking['check_in']);
                            $check_out = new DateTime($booking['check_out']);
                            $today = new DateTime();
                            $days = $check_in->diff($check_out)->days;
                            $total = $days * $booking['price'];
                            
                            // Determine status
                            if ($check_in > $today) {
                                $status = 'Upcoming';
                                $status_class = 'info';
                            } elseif ($check_out < $today) {
                                $status = 'Completed';
                                $status_class = 'secondary';
                            } else {
                                $status = 'Active';
                                $status_class = 'success';
                            }
                        ?>
                            <tr>
                                <td>#<?php echo $booking['booking_id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['room_number']); ?></strong><br>
                                    <small class="text-muted"><?php echo $booking['room_type']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['full_name']); ?></strong><br>
                                    <small class="text-muted"><?php echo $booking['email']; ?></small><br>
                                    <small class="text-muted"><?php echo $booking['phone']; ?></small>
                                </td>
                                <td><?php echo $check_in->format('M d, Y'); ?></td>
                                <td><?php echo $check_out->format('M d, Y'); ?></td>
                                <td><?php echo $days; ?></td>
                                <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                                <td>
                                    <span class="badge bg-<?php echo $status_class; ?>">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Statistics -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5><?php echo count($bookings); ?></h5>
                            <p class="card-text">Total Bookings</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>$<?php 
                                $total_revenue = 0;
                                foreach ($bookings as $b) {
                                    $check_in = new DateTime($b['check_in']);
                                    $check_out = new DateTime($b['check_out']);
                                    $days = $check_in->diff($check_out)->days;
                                    $total_revenue += $days * $b['price'];
                                }
                                echo number_format($total_revenue, 2);
                            ?></h5>
                            <p class="card-text">Total Revenue</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5><?php 
                                $upcoming = 0;
                                $today = new DateTime();
                                foreach ($bookings as $b) {
                                    $check_in = new DateTime($b['check_in']);
                                    if ($check_in > $today) $upcoming++;
                                }
                                echo $upcoming;
                            ?></h5>
                            <p class="card-text">Upcoming</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5><?php 
                                $active = 0;
                                $today = new DateTime();
                                foreach ($bookings as $b) {
                                    $check_in = new DateTime($b['check_in']);
                                    $check_out = new DateTime($b['check_out']);
                                    if ($check_in <= $today && $check_out >= $today) $active++;
                                }
                                echo $active;
                            ?></h5>
                            <p class="card-text">Active Now</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../../includes/footer.php'; ?>