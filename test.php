<?php
echo "Checking if ajax folder exists: " . (is_dir(__DIR__ . '/ajax') ? 'YES' : 'NO') . "<br>";
echo "Checking if check_availability.php exists: " . (file_exists(__DIR__ . '/ajax/check_availability.php') ? 'YES' : 'NO') . "<br>";

// Test the direct URL
echo "Direct URL: http://localhost/FullStack/hostel-booking/ajax/check_availability.php<br>";
?>