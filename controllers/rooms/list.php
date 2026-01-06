<?php
// controllers/rooms/list.php
require_once '../../config/database.php';
require_once '../../includes/header.php';

$conn = getDB();

// Get all rooms
$stmt = $conn->query("SELECT * FROM rooms ORDER BY room_number");
$rooms = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-door-closed"></i> Room Management</h2>
    <a href="add.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Room
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Room #</th>
                        <th>Type</th>
                        <th>Capacity</th>
                        <th>Price/Night</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rooms)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-door-closed display-4"></i>
                                <p class="mt-2">No rooms found. Add your first room!</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rooms as $room): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($room['room_number']); ?></strong></td>
                                <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                                <td><span class="badge bg-info"><?php echo $room['capacity']; ?> person(s)</span></td>
                                <td><strong>$<?php echo number_format($room['price'], 2); ?></strong></td>
                                <td>
                                    <?php 
                                    $status_class = ($room['status'] == 'Available') ? 'success' : 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($room['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="edit.php?id=<?php echo $room['room_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="delete.php?id=<?php echo $room['room_id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure?');">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>