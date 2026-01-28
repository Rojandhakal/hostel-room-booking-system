<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require '../config/database.php';
include '../includes/client_header.php';

$results = $pdo->query("SELECT * FROM rooms ORDER BY room_number")->fetchAll();

if (isset($_GET['search'])) {
    $type     = $_GET['type'] ?? '';
    $capacity = $_GET['capacity'] ?? 0;

    $stmt = $pdo->prepare(
        "SELECT * FROM rooms WHERE room_type LIKE ? AND capacity >= ? ORDER BY room_number"
    );
    $stmt->execute(["%$type%", $capacity]);
    $results = $stmt->fetchAll();
}

function getRoomStatus($pdo, $room_number) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM occupants 
         WHERE room_number = ? AND check_in <= ? AND check_out >= ?"
    );
    $stmt->execute([$room_number, $today, $today]);
    return $stmt->fetchColumn() > 0 ? 'Occupied' : 'Available';
}
?>

<h3>Search Rooms</h3>
<form method="get">
    <input name="type" placeholder="Room Type" value="<?= htmlspecialchars($_GET['type'] ?? '') ?>">
    <input name="capacity" type="number" placeholder="Min Capacity" value="<?= htmlspecialchars($_GET['capacity'] ?? '') ?>">
    <button name="search">Search</button>
</form>

<hr>

<table>
<tr>
    <th>Room Number</th>
    <th>Room Type</th>
    <th>Capacity</th>
    <th>Status</th>
</tr>

<?php if (count($results) === 0): ?>
<tr>
    <td colspan="4">No rooms found.</td>
</tr>
<?php else: ?>
<?php foreach ($results as $r): ?>
<tr>
    <td><?= htmlspecialchars($r['room_number']) ?></td>
    <td><?= htmlspecialchars($r['room_type']) ?></td>
    <td><?= $r['capacity'] ?></td>
    <td><?= getRoomStatus($pdo, $r['room_number']) ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>

<?php include '../includes/footer.php'; ?>
