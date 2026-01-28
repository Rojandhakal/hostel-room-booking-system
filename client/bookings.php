<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

require '../config/database.php';
include '../includes/client_header.php';

$error = "";

$user_id = $_SESSION['user_id'];

// Fetch all rooms (room_number => room_type)
$rooms = $pdo->query("SELECT room_number, room_type FROM rooms")->fetchAll(PDO::FETCH_KEY_PAIR);

// Handle Add Booking
if (isset($_POST['save'])) {

    $name        = htmlspecialchars(trim($_POST['name']));
    $phone       = htmlspecialchars(trim($_POST['phone']));
    $room_number = $_POST['room_number'];
    $check_in    = $_POST['check_in'];
    $check_out   = $_POST['check_out'];

    // Validation
    if (empty($name) || empty($phone) || empty($room_number) || empty($check_in) || empty($check_out)) {
        $error = "All fields are required!";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone must be 10 digits!";
    } elseif ($check_out <= $check_in) {
        $error = "Check-out date must be after check-in!";
    } else {
        // Check overlapping booking
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM bookings 
             WHERE room_number=? AND check_in <= ? AND check_out >= ?"
        );
        $stmt->execute([$room_number, $check_out, $check_in]);

        if ($stmt->fetchColumn() > 0) {
            $error = "This room is already booked for the selected dates!";
        } else {
            // Add booking
            $stmt = $pdo->prepare(
                "INSERT INTO bookings (user_id, name, phone, room_number, check_in, check_out) 
                 VALUES (?,?,?,?,?,?)"
            );
            $stmt->execute([$user_id, $name, $phone, $room_number, $check_in, $check_out]);
            header("Location: bookings.php");
            exit;
        }
    }
}

// Fetch user's bookings only
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id=? ORDER BY check_in DESC");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();

// Determine available rooms based on selected dates
$selected_check_in  = $_POST['check_in'] ?? '';
$selected_check_out = $_POST['check_out'] ?? '';
$available_rooms = $rooms; // default: all rooms

if ($selected_check_in && $selected_check_out) {
    // Find booked rooms overlapping with selected dates
    $stmt = $pdo->prepare(
        "SELECT room_number FROM bookings 
         WHERE check_in <= ? AND check_out >= ?"
    );
    $stmt->execute([$selected_check_out, $selected_check_in]);
    $booked_rooms = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Remove booked rooms
    foreach ($booked_rooms as $broom) {
        unset($available_rooms[$broom]);
    }
}
?>

<h3>Book a Room</h3>

<?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <label>Name</label><br>
    <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? $_SESSION['full_name']) ?>"><br><br>

    <label>Phone</label><br>
    <input type="text" name="phone" maxlength="10" pattern="[0-9]{10}" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"><br><br>

    <label>Check In</label><br>
    <input type="date" name="check_in" required value="<?= htmlspecialchars($selected_check_in) ?>"><br><br>

    <label>Check Out</label><br>
    <input type="date" name="check_out" required value="<?= htmlspecialchars($selected_check_out) ?>"><br><br>

    <label>Room Number (only available rooms)</label><br>
    <select name="room_number" required>
        <option value="">Select Room</option>
        <?php foreach ($available_rooms as $number => $type): ?>
            <option value="<?= $number ?>"><?= $number ?> (<?= $type ?>)</option>
        <?php endforeach; ?>
    </select><br><br>

    <button name="save">Book Room</button>
</form>

<hr>

<h3>My Bookings</h3>
<table>
<tr>
    <th>Room Number</th>
    <th>Room Type</th>
    <th>Check In</th>
    <th>Check Out</th>
</tr>

<?php if(count($bookings) === 0): ?>
<tr><td colspan="4">You have no bookings.</td></tr>
<?php else: ?>
<?php foreach($bookings as $b): ?>
<tr>
    <td><?= htmlspecialchars($b['room_number']) ?></td>
    <td><?= htmlspecialchars($rooms[$b['room_number']] ?? '') ?></td>
    <td><?= $b['check_in'] ?></td>
    <td><?= $b['check_out'] ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>

<?php include '../includes/footer.php'; ?>
