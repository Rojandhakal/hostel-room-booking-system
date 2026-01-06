<?php
// config/database.php

class Database {
    private static $pdo = null;
    
    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                $host = 'localhost';
                $dbname = 'hostel_booking';
                $username = 'root';
                $password = '';
                
                self::$pdo = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch(PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

// Helper function
function getDB() {
    return Database::getConnection();
}
// REMOVE session_start() from this file completely
?>