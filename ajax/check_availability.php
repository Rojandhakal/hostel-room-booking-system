<?php
require '../config/database.php';

if (!isset($_GET['room_number'], $_GET['check_in'], $_GET['check_out'])) {
    echo "invalid";
    exit;
}

$room = $_GET['room_number'];
$check_in = $_GET['check_in'];
$check_out = $_GET['check_out'];

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM bookings 
     WHERE room_number=? AND check_in <= ? AND check_out >= ?"
);
$stmt->execute([$room, $check_out, $check_in]);

echo ($stmt->fetchColumn() > 0) ? "booked" : "available";
