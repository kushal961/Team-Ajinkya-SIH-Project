<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch upcoming appointments
$appointments = [];
$result = $conn->query("SELECT a.id, u.name AS doctor_name, a.appointment_date, a.appointment_time, a.reason, a.status 
                        FROM appointments a
                        JOIN users u ON a.doctor_id = u.id
                        WHERE a.patient_id = $user_id
                        ORDER BY a.appointment_date, a.appointment_time");
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

// Fetch doctors list
$doctors = [];
$result = $conn->query("SELECT id, name FROM users WHERE role = 'doctor'");
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

// Handle appointment booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment'])) {
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = trim($_POST['reason']);

    if (!empty($doctor_id) && !empty($appointment_date) && !empty($appointment_time)) {
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, status) 
                                VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("iisss", $user_id, $doctor_id, $appointment_date, $appointment_time, $reason);
        if ($stmt->execute()) {
            header("Location: patient_dashboard.php"); // Refresh page after booking
        }
    }
}

// Handle appointment cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $conn->query("DELETE FROM appointments WHERE id = $appointment_id AND patient_id = $user_id");
    header("Location: patient_dashboard.php"); // Refresh page after cancellation
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-blue-600">Welcome, Patient!</h2>

    <!-- Show Appointments -->
    <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-700">Your Appointments</h3>
        <table class="w-full mt-4 border-collapse">
            <tr class="bg-gray-200">
                <th class="p-2">Doctor</th>
                <th class="p-2">Date</th>
                <th class="p-2">Time</th>
                <th class="p-2">Reason</th>
                <th class="p-2">Status</th>
                <th class="p-2">Action</th>
            </tr>
            <?php foreach ($appointments as $appt) { ?>
            <tr class="border-b">
                <td class="p-2"><?php echo htmlspecialchars($appt['doctor_name']); ?></td>
                <td class="p-2"><?php echo $appt['appointment_date']; ?></td>
                <td class="p-2"><?php echo $appt['appointment_time']; ?></td>
                <td class="p-2"><?php echo htmlspecialchars($appt['reason']); ?></td>
                <td class="p-2"><?php echo $appt['status']; ?></td>
                <td class="p-2">
                    <?php if ($appt['status'] === 'Pending') { ?>
                        <form method="POST">
                            <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                            <button type="submit" name="cancel_appointment" class="bg-red-500 text-white px-3 py-1 rounded-lg">
                                Cancel
                            </button>
                        </form>
                    <?php } else { echo 'N/A'; } ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Book New Appointment -->
    <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-700">Book a New Appointment</h3>

        <form method="POST" class="mt-4 space-y-4">
            <select name="doctor_id" required class="w-full p-3 border rounded-lg">
                <option value="">Choose a Doctor</option>
                <?php foreach ($doctors as $doctor) { ?>
                    <option value="<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['name']); ?></option>
                <?php } ?>
            </select>

            <input type="date" name="appointment_date" required class="w-full p-3 border rounded-lg">
            <input type="time" name="appointment_time" required class="w-full p-3 border rounded-lg">
            <textarea name="reason" class="w-full p-3 border rounded-lg" placeholder="Reason (Optional)"></textarea>

            <button type="submit" name="book_appointment" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                Book Appointment
            </button>
        </form>
    </div>
</div>

</body>
</html>

