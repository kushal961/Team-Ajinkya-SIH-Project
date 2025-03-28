<?php
$servername = "localhost";
$username = "root"; // Change if you have a different user
$password = ""; // Change if you have a password
$dbname = "doctor_patient_system"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
