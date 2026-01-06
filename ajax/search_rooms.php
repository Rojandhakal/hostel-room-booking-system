<?php
// ajax/search_rooms.php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $conn = getDB();
    
    $query = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING);
    
    if (strlen($query) < 2) {
        echo json_encode([]);
        exit;
    }
    
    $stmt = $conn->prepare("
        SELECT room_id, room_number, room_type, price 
        FROM rooms 
        WHERE (room_number LIKE :query OR room_type LIKE :query)
        AND status = 'Available'
        LIMIT 10
    ");
    
    $stmt->execute([':query' => "%$query%"]);
    $results = $stmt->fetchAll();
    
    echo json_encode($results);
    
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>