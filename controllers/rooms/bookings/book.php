<?php
// controllers/bookings/book.php
require_once '../../../config/database.php';
require_once '../../../includes/header.php';

$conn = getDB();

// Get all available rooms
$rooms = $conn->query("SELECT * FROM rooms WHERE status = 'Available' ORDER BY room_number")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get inputs
        $room_id = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $check_in = $_POST['check_in'] ?? '';
        $check_out = $_POST['check_out'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $phone = trim($_POST['phone'] ?? '');
        
        // Validation
        $errors = [];
        if (!$room_id) $errors[] = 'Please select a room';
        if (!$check_in || !$check_out) $errors[] = 'Please select dates';
        if (empty($full_name)) $errors[] = 'Full name is required';
        if (!$email) $errors[] = 'Valid email is required';
        
        // Validate dates
        if ($check_in && $check_out) {
            $today = date('Y-m-d');
            if ($check_in < $today) {
                $errors[] = 'Check-in date cannot be in the past';
            }
            if ($check_out <= $check_in) {
                $errors[] = 'Check-out date must be after check-in date';
            }
        }
        
        if (empty($errors)) {
            // Check availability
            $stmt = $conn->prepare("
                SELECT COUNT(*) as count 
                FROM bookings 
                WHERE room_id = :room_id 
                AND (
                    (check_in < :check_out AND check_out > :check_in)
                )
            ");
            
            $stmt->execute([
                ':room_id' => $room_id,
                ':check_in' => $check_in,
                ':check_out' => $check_out
            ]);
            
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                $errors[] = 'Sorry, this room is no longer available for the selected dates';
            } else {
                // Get room price
                $stmt = $conn->prepare("SELECT price FROM rooms WHERE room_id = :room_id");
                $stmt->execute([':room_id' => $room_id]);
                $room = $stmt->fetch();
                
                // Calculate total price
                $start = new DateTime($check_in);
                $end = new DateTime($check_out);
                $days = $start->diff($end)->days;
                $total_price = $days * $room['price'];
                
                // Insert occupant
                $stmt = $conn->prepare("
                    INSERT INTO occupants (full_name, email, phone) 
                    VALUES (:full_name, :email, :phone)
                ");
                
                $stmt->execute([
                    ':full_name' => $full_name,
                    ':email' => $email,
                    ':phone' => $phone
                ]);
                
                $occupant_id = $conn->lastInsertId();
                
                // Create booking
                $stmt = $conn->prepare("
                    INSERT INTO bookings (room_id, occupant_id, check_in, check_out) 
                    VALUES (:room_id, :occupant_id, :check_in, :check_out)
                ");
                
                $stmt->execute([
                    ':room_id' => $room_id,
                    ':occupant_id' => $occupant_id,
                    ':check_in' => $check_in,
                    ':check_out' => $check_out
                ]);
                
                // Update room status to occupied
                $stmt = $conn->prepare("UPDATE rooms SET status = 'Occupied' WHERE room_id = :room_id");
                $stmt->execute([':room_id' => $room_id]);
                
                $_SESSION['message'] = "✅ Booking confirmed! Total: $" . number_format($total_price, 2) . " for $days night(s)";
                $_SESSION['message_type'] = 'success';
                header('Location: /FullStack/hostel-booking/index.php');
                exit;
            }
        }
        
        // If there are errors
        if (!empty($errors)) {
            $error_message = implode('<br>', $errors);
        }
    } catch(PDOException $e) {
        $error_message = 'Database error: ' . $e->getMessage();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="bi bi-calendar-check"></i> Book a Room</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <form method="POST" id="bookingForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Room & Dates</h5>
                            
                            <div class="mb-3">
                                <label for="room_id" class="form-label">Select Room *</label>
                                <select class="form-select" id="room_id" name="room_id" required>
                                    <option value="">Choose a room...</option>
                                    <?php foreach ($rooms as $room): ?>
                                        <option value="<?php echo $room['room_id']; ?>">
                                            Room <?php echo htmlspecialchars($room['room_number']); ?> - 
                                            <?php echo htmlspecialchars($room['room_type']); ?> 
                                            ($<?php echo number_format($room['price'], 2); ?>/night)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="check_in" class="form-label">Check-in Date *</label>
                                    <input type="date" class="form-control datepicker" id="check_in" name="check_in" 
                                           value="<?php echo htmlspecialchars($_POST['check_in'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="check_out" class="form-label">Check-out Date *</label>
                                    <input type="date" class="form-control datepicker" id="check_out" name="check_out" 
                                           value="<?php echo htmlspecialchars($_POST['check_out'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <button type="button" class="btn btn-info" onclick="checkAvailability()">
                                    <i class="bi bi-check-circle"></i> Check Availability
                                </button>
                            </div>
                            
                            <div id="availability-result"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-3">Guest Information</h5>
                            
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="alert alert-info mt-4">
                                <h6><i class="bi bi-info-circle"></i> Booking Information</h6>
                                <small>
                                    • Your booking will be confirmed immediately<br>
                                    • Check-in time: 2:00 PM | Check-out: 11:00 AM<br>
                                    • Cancellation policy: Free cancellation up to 24 hours before check-in
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="bi bi-check-lg"></i> Confirm Booking
                        </button>
                        <a href="/FullStack/hostel-booking/index.php" class="btn btn-outline-secondary ms-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function checkAvailability() {
    const roomId = document.getElementById('room_id').value;
    const checkIn = document.getElementById('check_in').value;
    const checkOut = document.getElementById('check_out').value;
    
    if (!roomId || !checkIn || !checkOut) {
        alert('Please select a room and dates first');
        return;
    }
    
    if (checkOut <= checkIn) {
        alert('Check-out date must be after check-in date');
        return;
    }
    
    const resultDiv = document.getElementById('availability-result');
    resultDiv.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div> Checking...</div>';
    
    fetch('/FullStack/hostel-booking/ajax/check_availability.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `room_id=${roomId}&check_in=${checkIn}&check_out=${checkOut}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            resultDiv.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
        } else if (data.available) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <h6><i class="bi bi-check-circle"></i> Room Available!</h6>
                    <p>Duration: ${data.days} night(s)</p>
                    <p>Price per night: $${data.nightly_rate}</p>
                    <p>Total Price: <strong>$${data.price}</strong></p>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="bi bi-x-circle"></i> Room Not Available</h6>
                    <p>${data.message}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<div class="alert alert-danger">Error checking availability</div>`;
    });
}
</script>

<?php require_once '../../../includes/footer.php'; ?>
