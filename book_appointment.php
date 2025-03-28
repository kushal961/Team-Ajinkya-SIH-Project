<?php
session_start();
require 'config.php'; // Database connection

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

// Fetch doctors from the database
$doctors = [];
$result = $conn->query("SELECT id, name FROM users WHERE role = 'doctor'");
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

// Handle appointment booking
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = trim($_POST['reason']);

    if (empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
        $error = "All fields are required.";
    } else {
        // Insert appointment into database
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason);

        if ($stmt->execute()) {
            $success = "Appointment booked successfully!";
        } else {
            $error = "Error booking appointment.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold text-blue-600 text-center mb-6">Book an Appointment</h2>

        <?php if (isset($error)) { ?>
            <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
        <?php if (isset($success)) { ?>
            <p class="text-green-500 text-center mb-4"><?php echo htmlspecialchars($success); ?></p>
        <?php } ?>

        <form action="" method="POST" class="space-y-4">
            <label class="block text-gray-600 font-medium">Select Doctor:</label>
            <select name="doctor_id" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Choose a doctor</option>
                <?php foreach ($doctors as $doctor) { ?>
                    <option value="<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['name']); ?></option>
                <?php } ?>
            </select>

            <label class="block text-gray-600 font-medium">Appointment Date:</label>
            <input type="date" name="appointment_date" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

            <label class="block text-gray-600 font-medium">Appointment Time:</label>
            <input type="time" name="appointment_time" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

            <label class="block text-gray-600 font-medium">Reason (Optional):</label>
            <textarea name="reason" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Describe your reason"></textarea>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
                Book Appointment
            </button>
        </form>

        <p class="text-center text-gray-600 mt-4">
            <a href="patient_dashboard.php" class="text-blue-600">Back to Dashboard</a>
        </p>
    </div>

</body>
</html>

