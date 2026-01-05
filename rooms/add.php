<?php
include '../config/db.php';      
include '../templates/header.php'; 

if (isset($_POST['submit'])) {
    $room_no = $_POST['room_number'];
    $type = $_POST['room_type'];
    $capacity = $_POST['capacity'];
    $price = $_POST['price'];

    $stmt = $conn->prepare(
      "INSERT INTO rooms (room_number, room_type, capacity, price)
       VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssid", $room_no, $type, $capacity, $price);
    
    if($stmt->execute()) {
        echo "<p style='color:green;'>Room added successfully!</p>";
    } else {
        echo "<p style='color:red;'>Failed to add room.</p>";
    }
}
?>

<h3>Add New Room</h3>
<form method="POST">
    <label>Room Number:</label><br>
    <input type="text" name="room_number" required><br><br>

    <label>Room Type:</label><br>
    <select name="room_type" required>
        <option value="Single">Single</option>
        <option value="Double">Double</option>
        <option value="Dorm">Dorm</option>
    </select><br><br>

    <label>Capacity:</label><br>
    <input type="number" name="capacity" min="1"  required><br><br>

    <label>Price:</label><br>
    <input type="text" name="price" required><br><br>

    <input type="submit" name="submit" value="Add Room">
</form>

<?php include '../templates/footer.php'; ?>
