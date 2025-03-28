<?php
session_start();
require 'config.php'; // Database connection

// Ensure only doctors can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];

// Fetch appointments for the logged-in doctor
$stmt = $conn->prepare("SELECT a.id, a.patient_name, a.date, a.time, a.status FROM appointments a WHERE a.doctor_id = ? ORDER BY a.date, a.time");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    // Update appointment status
    if (in_array($action, ['confirmed', 'completed', 'canceled'])) {
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
        $stmt->bind_param("sii", $action, $appointment_id, $doctor_id);
        $stmt->execute();
    }
    header("Location: manage_appointments.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex justify-center items-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl">
        <h2 class="text-2xl font-bold text-blue-600 text-center mb-6">Manage Appointments</h2>
        
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-blue-500 text-white">
                    <th class="p-3">Patient</th>
                    <th class="p-3">Date</th>
                    <th class="p-3">Time</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr class="border-b">
                        <td class="p-3 text-center"><?php echo htmlspecialchars($row['patient_name']); ?></td>
                        <td class="p-3 text-center"><?php echo htmlspecialchars($row['date']); ?></td>
                        <td class="p-3 text-center"><?php echo htmlspecialchars($row['time']); ?></td>
                        <td class="p-3 text-center text-<?php echo $row['status'] == 'pending' ? 'yellow' : ($row['status'] == 'confirmed' ? 'green' : 'red'); ?>-500 font-bold">
                            <?php echo ucfirst($row['status']); ?>
                        </td>
                        <td class="p-3 text-center">
                            <?php if ($row['status'] == 'pending') { ?>
                                <form action="" method="POST" class="inline">
                                    <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="confirmed" class="bg-green-500 text-white px-3 py-1 rounded">Confirm</button>
                                </form>
                                <form action="" method="POST" class="inline">
                                    <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="canceled" class="bg-red-500 text-white px-3 py-1 rounded">Cancel</button>
                                </form>
                            <?php } elseif ($row['status'] == 'confirmed') { ?>
                                <form action="" method="POST">
                                    <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="completed" class="bg-blue-500 text-white px-3 py-1 rounded">Mark as Completed</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
