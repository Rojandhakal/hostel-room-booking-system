<?php
// controllers/rooms/delete.php
require_once '../../config/database.php';

session_start();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['message'] = 'Invalid room ID';
    $_SESSION['message_type'] = 'error';
    header('Location: list.php');
    exit;
}

$conn = getDB();

try {
    // Check if room has bookings
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE room_id = :id");
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        $_SESSION['message'] = 'Cannot delete room with existing bookings';
        $_SESSION['message_type'] = 'error';
    } else {
        // Delete room
        $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = :id");
        $stmt->execute([':id' => $id]);
        
        $_SESSION['message'] = 'Room deleted successfully';
        $_SESSION['message_type'] = 'success';
    }
} catch(PDOException $e) {
    $_SESSION['message'] = 'Error deleting room: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

header('Location: list.php');
exit;
?>