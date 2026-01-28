<?php
require '../config/database.php';

$stmt = $pdo->prepare("SELECT status FROM rooms WHERE id=?");
$stmt->execute([$_GET['room_id']]);
echo $stmt->fetchColumn();
