<?php
include '../includes/admin_header.php';
require '../config/database.php';
$error="";
$edit=null;
if(isset($_GET['edit'])){
    $stmt=$pdo->prepare("SELECT * FROM occupants WHERE id=?");
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
            $stmt=$pdo->prepare("UPDATE occupants SET name=?, phone=?, room_number=?, check_in=?, check_out=? WHERE id=?");
            $stmt->execute([$name,$phone,$room_number,$check_in,$check_out,$_POST['id']]);
        }else{
            $stmt=$pdo->prepare("INSERT INTO occupants (name,phone,room_number,check_in,check_out) VALUES (?,?,?,?,?)");
            $stmt->execute([$name,$phone,$room_number,$check_in,$check_out]);
            $pdo->prepare("DELETE FROM bookings WHERE name=? AND phone=? AND room_number=?")->execute([$name,$phone,$room_number]);
        }
        header("Location: occupants.php"); exit;
    }
}
if(isset($_GET['delete'])){
    $pdo->prepare("DELETE FROM occupants WHERE id=?")->execute([$_GET['delete']]);
    header("Location: occupants.php"); exit;
}
$occupants=$pdo->query("SELECT * FROM occupants ORDER BY check_in DESC")->fetchAll();
$rooms=$pdo->query("SELECT room_number, room_type FROM rooms")->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<h3>Occupants</h3>
<?php if($error) echo "<p class='error'>$error</p>"; ?>
<form method="post">
<input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
<label>Name</label><br>
<input type="text" name="name" required value="<?= $edit['name'] ?? '' ?>"><br><br>
<label>Phone</label><br>
<input type="text" name="phone" maxlength="10" required value="<?= $edit['phone'] ?? '' ?>"><br><br>
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
<button name="save"><?= $edit?'Update':'Add Occupant' ?></button>
</form>
<hr>
<table>
<tr><th>Name</th><th>Phone</th><th>Room</th><th>Room Type</th><th>Check In</th><th>Check Out</th><th>Action</th></tr>
<?php foreach($occupants as $o): ?>
<tr>
<td><?= $o['name'] ?></td>
<td><?= $o['phone'] ?></td>
<td><?= $o['room_number'] ?></td>
<td><?= $rooms[$o['room_number']] ?? '' ?></td>
<td><?= $o['check_in'] ?></td>
<td><?= $o['check_out'] ?></td>
<td>
<a href="?edit=<?= $o['id'] ?>">Edit</a> |
<a class="action" href="?delete=<?= $o['id'] ?>" onclick="return confirm('Delete occupant?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php include '../includes/footer.php'; ?>
