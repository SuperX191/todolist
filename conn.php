<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "todolist_app";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตั้งค่า charset
$conn->set_charset("utf8mb4");
?>