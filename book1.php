<?php
session_start();
require 'config.php';

// Ensure doctor ID is provided
if (!isset($_GET['doctor_id']) || empty($_GET['doctor_id'])) {
    die("Doctor ID is required.");
}

$doctor_id = $_GET['doctor_id'];

// Check if doctor exists
$stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Doctor not found.");
}

$doctor = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-20 p-10 bg-white shadow-md rounded-lg">
        <h2 class="text-3xl font-bold text-blue-700 text-center">Book an Appointment</h2>
        
        <p class="text-center text-lg mt-3">You are booking an appointment with <strong>Dr. <?php echo htmlspecialchars($doctor['name']); ?></strong></p>

        <form method="post" action="confirm_booking.php" class="mt-6">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">

            <label class="block text-gray-700 font-medium">Select Date:</label>
            <input type="date" name="appointment_date" required class="w-full p-3 border rounded-lg mt-2 mb-4">

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">Confirm Booking</button>
        </form>
    </div>
</body>
</html>
