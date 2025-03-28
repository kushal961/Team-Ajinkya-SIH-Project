<?php
session_start();
require 'config.php'; // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=not_logged_in");
    exit();
}

// Validate input fields
if (empty($_POST['doctor_id']) || empty($_POST['appointment_date']) || empty($_POST['appointment_time'])) {
    header("Location: book_appointment.php?error=missing_fields");
    exit();
}

$doctor_id = intval($_POST['doctor_id']); // Ensure it's an integer
$user_id = $_SESSION['user_id'];
$appointment_date = $_POST['appointment_date'];
$appointment_time = $_POST['appointment_time'];

// Validate date & time (No past appointments)
$current_date = date("Y-m-d");
$current_time = date("H:i");
if ($appointment_date < $current_date || ($appointment_date == $current_date && $appointment_time <= $current_time)) {
    header("Location: book_appointment.php?error=invalid_datetime");
    exit();
}

// Check if doctor exists
$stmt = $conn->prepare("SELECT id FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php?error=doctor_not_found");
    exit();
}

// Insert appointment into database
$stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->bind_param("iiss", $user_id, $doctor_id, $appointment_date, $appointment_time);

if ($stmt->execute()) {
    header("Location: dashboard.php?success=appointment_booked");
} else {
    header("Location: book_appointment.php?error=booking_failed");
}

$stmt->close();
$conn->close();
?>

