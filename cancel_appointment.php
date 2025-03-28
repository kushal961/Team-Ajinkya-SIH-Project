<?php
session_start();
include "db.php";

echo "<pre>";
print_r($_POST);  // Print POST data
echo "</pre>";

if (!isset($_SESSION["user_id"])) {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["appointment_id"])) {
    echo "POST data received correctly!";  // Check if this prints
    // Proceed with cancellation logic
} else {
    die("Invalid request.");
}





if (!isset($_SESSION["user_id"])) {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["appointment_id"])) {
    $appointment_id = intval($_POST["appointment_id"]); // Ensures the ID is an integer

    // Use prepared statement
    $stmt = $conn->prepare("SELECT appointment_date FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();

    if ($appointment) {
        $appointment_date = new DateTime($appointment["appointment_date"]);
        $current_date = new DateTime();

        $interval = $current_date->diff($appointment_date);
        if ($interval->invert == 1 || $interval->days < 1) { // Check if less than 24 hours
            die("Cannot cancel appointments less than 24 hours before.");
        }

        $update_stmt = $conn->prepare("UPDATE appointments SET status = 'canceled' WHERE id = ?");
        $update_stmt->bind_param("i", $appointment_id);
        $update_stmt->execute();

        echo "Appointment canceled successfully.";
    } else {
        echo "Appointment not found.";
    }
} else {
    echo "Invalid request.";
}
?>
