<?php
require '../config/database.php';
include '../includes/admin_header.php';
$error = "";
$edit = null;

if(isset($_POST['save'])){
    $room_number = trim($_POST['room_number']);
    $room_type   = $_POST['room_type'];
    $capacity    = (int) $_POST['capacity'];
    $price       = (int) $_POST['price'];

    if(!$room_number || !$room_type || $capacity < 1 || $price < 1){
        $error = "All fields are required with valid values!";
    } else {
        if(!empty($_POST['id'])){
            $stmt = $pdo->prepare("SELECT id FROM rooms WHERE room_number=? AND id != ?");
            $stmt->execute([$room_number, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("SELECT id FROM rooms WHERE room_number=?");
            $stmt->execute([$room_number]);
        }

        if($stmt->rowCount() > 0){
            $error = "Room number already exists!";
        } else {
            if(!empty($_POST['id'])){
                $stmt = $pdo->prepare("UPDATE rooms SET room_number=?, room_type=?, capacity=?, price=? WHERE id=?");
                $stmt->execute([$room_number,$room_type,$capacity,$price,$_POST['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO rooms (room_number,room_type,capacity,price,status) VALUES (?,?,?,?, 'available')");
                $stmt->execute([$room_number,$room_type,$capacity,$price]);
            }
            header("Location: rooms.php");
            exit;
        }
    }
}
if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: rooms.php");
    exit;
}
if(isset($_GET['edit'])){
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit = $stmt->fetch();
}
$rooms = $pdo->query("SELECT * FROM rooms ORDER BY room_number")->fetchAll();
?>

<h3>Manage Rooms</h3>

<?php if($error): ?>
<p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
<input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

<label>Room Number</label>
<input type="text" name="room_number" required value="<?= $edit['room_number'] ?? '' ?>">

<label>Room Type</label>
<select name="room_type" required>
<option value="">Select Type</option>
<option value="Single" <?= (isset($edit)&&$edit['room_type']=='Single')?'selected':'' ?>>Single</option>
<option value="Double" <?= (isset($edit)&&$edit['room_type']=='Double')?'selected':'' ?>>Double</option>
<option value="Shared" <?= (isset($edit)&&$edit['room_type']=='Shared')?'selected':'' ?>>Shared</option>
</select>

<label>Capacity</label>
<input type="number" name="capacity" min="1" required value="<?= $edit['capacity'] ?? 1 ?>">

<label>Price</label>
<input type="number" name="price" min="100" required value="<?= $edit['price'] ?? 100 ?>">

<button name="save"><?= $edit ? 'Update Room' : 'Add Room' ?></button>
</form>

<hr>

<h3>All Rooms</h3>
<table>
<tr>
<th>Room No</th>
<th>Type</th>
<th>Capacity</th>
<th>Price</th>
<th>Status</th>
<th>Actions</th>
</tr>
<?php foreach($rooms as $r): ?>
<tr>
<td><?= htmlspecialchars($r['room_number']) ?></td>
<td><?= htmlspecialchars($r['room_type']) ?></td>
<td><?= $r['capacity'] ?></td>
<td><?= $r['price'] ?></td>
<td><?= $r['status'] ?></td>
<td>
<a href="rooms.php?edit=<?= $r['id'] ?>">Edit</a> |
<a href="rooms.php?delete=<?= $r['id'] ?>" onclick="return confirm('Delete this room?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
