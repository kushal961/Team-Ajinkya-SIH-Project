<?php
session_start();
require 'config.php'; // Database connection

// Ensure only doctors can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];

// Handle Appointment Actions (Approve, Reject, Complete)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'approved' WHERE id = ?");
    } elseif ($action == 'reject') {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'rejected' WHERE id = ?");
    } elseif ($action == 'complete') {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?");
    }

    if ($stmt) {
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch Appointments for this Doctor, including the reason field
$stmt = $conn->prepare("SELECT a.id, u.name AS patient_name, a.appointment_date, a.appointment_time, a.status, a.reason 
                        FROM appointments a 
                        JOIN users u ON a.patient_id = u.id 
                        WHERE a.doctor_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold text-blue-600 mb-4">Doctor Dashboard</h2>

        <!-- New Fill Details Button -->
        <div class="mb-4">
            <a href="fill_doctor_details.php" class="bg-purple-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-purple-700">
                📝 Fill Your Details
            </a>

            <a href="logout.php" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-red-400 transition">Logout</a>
        </div>

        <h3 class="text-xl font-semibold mb-3">Your Appointments</h3>
        
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-blue-500 text-white">
                    <th class="border border-gray-300 px-4 py-2">Patient</th>
                    <th class="border border-gray-300 px-4 py-2">Date</th>
                    <th class="border border-gray-300 px-4 py-2">Time</th>
                    <th class="border border-gray-300 px-4 py-2">Reason</th>
                    <th class="border border-gray-300 px-4 py-2">Status</th>
                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment) { ?>
                    <tr class="text-center bg-gray-50 hover:bg-gray-100">
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                        <td class="border px-4 py-2"><?php echo $appointment['appointment_date']; ?></td>
                        <td class="border px-4 py-2"><?php echo $appointment['appointment_time']; ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($appointment['reason']); ?></td>
                        <td class="border px-4 py-2">
                            <span class="px-2 py-1 text-white rounded-md
                                <?php echo ($appointment['status'] == 'pending' ? 'bg-yellow-500' :
                                            ($appointment['status'] == 'approved' ? 'bg-green-500' :
                                            ($appointment['status'] == 'rejected' ? 'bg-red-500' : 'bg-gray-500'))); ?>">
                                <?php echo ucfirst($appointment['status']); ?>
                            </span>
                        </td>
                        <td class="border px-4 py-2">
                            <?php if ($appointment['status'] == 'pending') { ?>
                                <form action="" method="POST" class="inline-block">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <button type="submit" name="action" value="approve" class="bg-green-500 text-white px-3 py-1 rounded-md">✔ Approve</button>
                                </form>
                                <form action="" method="POST" class="inline-block">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <button type="submit" name="action" value="reject" class="bg-red-500 text-white px-3 py-1 rounded-md">❌ Reject</button>
                                </form>
                            <?php } elseif ($appointment['status'] == 'approved') { ?>
                                <form action="" method="POST">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <button type="submit" name="action" value="complete" class="bg-blue-500 text-white px-3 py-1 rounded-md">✔ Mark as Completed</button>
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
