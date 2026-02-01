<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../auth/login.php");
    exit;
}

require '../config/database.php';
include '../includes/client_header.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$user_id = $_SESSION['user_id'];

$rooms = $pdo->query("SELECT room_number, room_type FROM rooms")
             ->fetchAll(PDO::FETCH_KEY_PAIR);

if (isset($_POST['save'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $name        = htmlspecialchars(trim($_POST['name']));
    $phone       = htmlspecialchars(trim($_POST['phone']));
    $room_number = $_POST['room_number'];
    $check_in    = $_POST['check_in'];
    $check_out   = $_POST['check_out'];

    if (empty($name) || empty($phone) || empty($room_number) || empty($check_in) || empty($check_out)) {
        $error = "All fields are required!";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone must be 10 digits!";
    } elseif ($check_out <= $check_in) {
        $error = "Check-out must be after check-in!";
    } else {

        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM bookings 
             WHERE room_number=? AND check_in <= ? AND check_out >= ?"
        );
        $stmt->execute([$room_number, $check_out, $check_in]);

        if ($stmt->fetchColumn() > 0) {
            $error = "Room already booked for selected dates!";
        } else {
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

$stmt = $pdo->prepare(
    "SELECT * FROM bookings WHERE user_id=? ORDER BY check_in DESC"
);
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll();
?>

<h3>Book a Room</h3>

<?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <label>Name</label><br>
    <input type="text" name="name" required value="<?= htmlspecialchars($_SESSION['full_name']) ?>"><br><br>

    <label>Phone</label><br>
    <input type="text" name="phone" maxlength="10" pattern="[0-9]{10}" required><br><br>

    <label>Check In</label><br>
    <input type="date" name="check_in" id="check_in" required><br><br>

    <label>Check Out</label><br>
    <input type="date" name="check_out" id="check_out" required><br><br>

    <label>Room Number</label><br>
    <select name="room_number" id="room_number" required>
        <option value="">Select Room</option>
        <?php foreach ($rooms as $number => $type): ?>
            <option value="<?= $number ?>">
                <?= htmlspecialchars($number) ?> (<?= htmlspecialchars($type) ?>)
            </option>
        <?php endforeach; ?>
    </select><br>

    <p id="room-status"></p>

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

<?php if (count($bookings) === 0): ?>
<tr><td colspan="4">You have no bookings.</td></tr>
<?php else: ?>
<?php foreach ($bookings as $b): ?>
<tr>
    <td><?= htmlspecialchars($b['room_number']) ?></td>
    <td><?= htmlspecialchars($rooms[$b['room_number']] ?? '') ?></td>
    <td><?= htmlspecialchars($b['check_in']) ?></td>
    <td><?= htmlspecialchars($b['check_out']) ?></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</table>

<script>
const roomSelect = document.getElementById("room_number");
const checkIn = document.getElementById("check_in");
const checkOut = document.getElementById("check_out");
const statusBox = document.getElementById("room-status");
const submitBtn = document.querySelector('button[name="save"]');

function checkAvailability() {
    if (!roomSelect.value || !checkIn.value || !checkOut.value) {
        statusBox.textContent = "";
        submitBtn.disabled = false;
        return;
    }

    fetch(`../ajax/check_availability.php?room_number=${roomSelect.value}&check_in=${checkIn.value}&check_out=${checkOut.value}`)
        .then(response => response.text())
        .then(result => {
            if (result === "booked") {
                statusBox.textContent = "Room is already booked";
                statusBox.style.color = "red";
                submitBtn.disabled = true;
            } else {
                statusBox.textContent = "Room is available";
                statusBox.style.color = "green";
                submitBtn.disabled = false;
            }
        });
}

roomSelect.addEventListener("change", checkAvailability);
checkIn.addEventListener("change", checkAvailability);
checkOut.addEventListener("change", checkAvailability);
</script>

<?php include '../includes/footer.php'; ?>
