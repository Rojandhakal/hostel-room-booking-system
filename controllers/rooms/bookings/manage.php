<?php
// bookings/manage.php
require_once '../config/database.php';
require_once '../includes/header.php';

$conn = getDB();

// Get all bookings with room and occupant details
$stmt = $conn->prepare("
    SELECT b.*, r.room_number, r.room_type, r.price, 
           o.full_name, o.email, o.phone
    FROM bookings b
    JOIN rooms r ON b.room_id = r.room_id
    JOIN occupants o ON b.occupant_id = o.occupant_id
    ORDER BY b.check_in DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="bi bi-list-check"></i> Manage Bookings</h4>
    </div>
    <div class="card-body">
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
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): 
                            $check_in = new DateTime($booking['check_in']);
                            $check_out = new DateTime($booking['check_out']);
                            $days = $check_in->diff($check_out)->days;
                            $total = $days * $booking['price'];
                        ?>
                            <tr>
                                <td>#<?php echo $booking['booking_id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['room_number']); ?></strong><br>
                                    <small class="text-muted"><?php echo $booking['room_type']; ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['full_name']); ?></strong><br>
                                    <small class="text-muted"><?php echo $booking['email']; ?></small>
                                </td>
                                <td><?php echo $check_in->format('M d, Y'); ?></td>
                                <td><?php echo $check_out->format('M d, Y'); ?></td>
                                <td><?php echo $days; ?></td>
                                <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="cancelBooking(<?php echo $booking['booking_id']; ?>)">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        // You can implement AJAX cancellation here
        alert('Cancellation feature to be implemented');
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>