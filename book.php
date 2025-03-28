<?php
session_start();
require 'config.php'; // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=not_logged_in");
    exit();
}

// Validate doctor ID
if (!isset($_GET['doctor_id']) || !is_numeric($_GET['doctor_id'])) {
    die("Error: Doctor ID is required and must be valid.");
}

$doctor_id = intval($_GET['doctor_id']);
$user_id = $_SESSION['user_id'];

// Check if doctor exists
$stmt = $conn->prepare("SELECT id FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Selected doctor does not exist.");
}

// Insert booking into the database
$stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, status) VALUES (?, ?, 'pending')");
$stmt->bind_param("ii", $user_id, $doctor_id);

if ($stmt->execute()) {
    echo "<script>alert('Appointment booked successfully!'); window.location.href='dashboard.php';</script>";
} else {
    die("SQL Error: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>

