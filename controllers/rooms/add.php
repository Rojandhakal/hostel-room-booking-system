<?php
// controllers/rooms/add.php
require_once '../../config/database.php';
require_once '../../includes/header.php';

$conn = getDB();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get inputs
        $room_number = trim($_POST['room_number'] ?? '');
        $room_type = $_POST['room_type'] ?? '';
        $capacity = filter_var($_POST['capacity'] ?? 0, FILTER_VALIDATE_INT);
        $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
        
        // Validation
        if (empty($room_number)) {
            $error = 'Room number is required';
        } elseif ($capacity < 1) {
            $error = 'Capacity must be at least 1';
        } elseif ($price <= 0) {
            $error = 'Price must be greater than 0';
        } else {
            // Check if room number exists
            $stmt = $conn->prepare("SELECT room_id FROM rooms WHERE room_number = :room_number");
            $stmt->execute([':room_number' => $room_number]);
            
            if ($stmt->fetch()) {
                $error = 'Room number already exists';
            } else {
                // Insert new room
                $stmt = $conn->prepare("
                    INSERT INTO rooms (room_number, room_type, capacity, price, status) 
                    VALUES (:room_number, :room_type, :capacity, :price, 'Available')
                ");
                
                $stmt->execute([
                    ':room_number' => $room_number,
                    ':room_type' => $room_type,
                    ':capacity' => $capacity,
                    ':price' => $price
                ]);
                
                $_SESSION['message'] = 'Room added successfully!';
                $_SESSION['message_type'] = 'success';
                header('Location: list.php');
                exit;
            }
        }
    } catch(PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Add New Room</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="room_number" class="form-label">Room Number *</label>
                            <input type="text" class="form-control" id="room_number" name="room_number" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="room_type" class="form-label">Room Type *</label>
                            <select class="form-select" id="room_type" name="room_type" required>
                                <option value="">Select Type</option>
                                <option value="Single">Single</option>
                                <option value="Double">Double</option>
                                <option value="Dorm">Dorm</option>
                                <option value="Suite">Suite</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Capacity *</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price per Night ($) *</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Save Room
                        </button>
                        <a href="list.php" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>