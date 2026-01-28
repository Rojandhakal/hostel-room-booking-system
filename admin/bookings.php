<?php
include '../includes/admin_header.php';
require '../config/database.php';
$error="";
$edit=null;
$rooms=$pdo->query("SELECT room_number, room_type FROM rooms")->fetchAll(PDO::FETCH_KEY_PAIR);
if(isset($_GET['edit'])){
    $stmt=$pdo->prepare("SELECT * FROM bookings WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit=$stmt->fetch();
}
if(isset($_POST['save'])){
    $name=$_POST['name'];
    $phone=$_POST['phone'];
    $room_number=$_POST['room_number'];
    $check_in=$_POST['check_in'];
    $check_out=$_POST['check_out'];
    if(!$name || !$phone || !$room_number || !$check_in || !$check_out){
        $error="All fields required";
    }elseif(!preg_match('/^[0-9]{10}$/',$phone)){
        $error="Phone must be 10 digits";
    }elseif($check_out<=$check_in){
        $error="Check-out must be after check-in";
    }else{
        if(!empty($_POST['id'])){
            $stmt=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_number=? AND id!=? AND check_in<=? AND check_out>=?");
            $stmt->execute([$room_number,$_POST['id'],$check_out,$check_in]);
        }else{
            $stmt=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_number=? AND check_in<=? AND check_out>=?");
            $stmt->execute([$room_number,$check_out,$check_in]);
        }
        if($stmt->fetchColumn()>0){
            $error="Room already booked for these dates";
        }else{
            if(!empty($_POST['id'])){
                $stmt=$pdo->prepare("UPDATE bookings SET name=?, phone=?, room_number=?, check_in=?, check_out=? WHERE id=?");
                $stmt->execute([$name,$phone,$room_number,$check_in,$check_out,$_POST['id']]);
            }else{
                $stmt=$pdo->prepare("INSERT INTO bookings (name,phone,room_number,check_in,check_out) VALUES (?,?,?,?,?)");
                $stmt->execute([$name,$phone,$room_number,$check_in,$check_out]);
            }
            header("Location: bookings.php"); exit;
        }
    }
}
if(isset($_GET['delete'])){
    $pdo->prepare("DELETE FROM bookings WHERE id=?")->execute([$_GET['delete']]);
    header("Location: bookings.php"); exit;
}
$bookings=$pdo->query("SELECT * FROM bookings ORDER BY check_in DESC")->fetchAll();
?>
<h3>Bookings Management</h3>
<?php if($error) echo "<p class='error'>$error</p>"; ?>
<form method="post">
<input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
<label>Name</label><br>
<input type="text" name="name" required value="<?= $edit['name'] ?? '' ?>"><br><br>
<label>Phone</label><br>
<input type="text" name="phone" maxlength="10" pattern="[0-9]{10}" required value="<?= $edit['phone'] ?? '' ?>"><br><br>
<label>Room Number</label><br>
<select name="room_number" required>
<option value="">Select Room</option>
<?php foreach($rooms as $num=>$type): ?>
<option value="<?= $num ?>" <?= (isset($edit)&&$edit['room_number']==$num)?'selected':'' ?>><?= $num ?> (<?= $type ?>)</option>
<?php endforeach; ?>
</select><br><br>
<label>Check In</label><br>
<input type="date" name="check_in" required value="<?= $edit['check_in'] ?? '' ?>"><br><br>
<label>Check Out</label><br>
<input type="date" name="check_out" required value="<?= $edit['check_out'] ?? '' ?>"><br><br>
<button name="save"><?= $edit?'Update Booking':'Add Booking' ?></button>
</form>
<hr>
<table>
<tr><th>Name</th><th>Phone</th><th>Room</th><th>Room Type</th><th>Check In</th><th>Check Out</th><th>Action</th></tr>
<?php foreach($bookings as $b): ?>
<tr>
<td><?= $b['name'] ?></td>
<td><?= $b['phone'] ?></td>
<td><?= $b['room_number'] ?></td>
<td><?= $rooms[$b['room_number']] ?? '' ?></td>
<td><?= $b['check_in'] ?></td>
<td><?= $b['check_out'] ?></td>
<td>
<a href="?edit=<?= $b['id'] ?>">Edit</a> |
<a class="action" href="?delete=<?= $b['id'] ?>" onclick="return confirm('Delete booking?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php include '../includes/footer.php'; ?>
