<?php
session_start();
require 'config.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        die("Error: All fields are required.");
    }

    // Check doctor in database
    $stmt = $conn->prepare("SELECT id, password FROM doctors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $doctor = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $doctor['password'])) {
            $_SESSION['doctor_id'] = $doctor['id'];
            header("Location: doctor_dashboard.php");
            exit();
        } else {
            die("Error: Incorrect password.");
        }
    } else {
        die("Error: Doctor not found.");
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold text-blue-600 text-center mb-6">Doctor Login</h2>
        
        <form method="POST" action="doctor_login.php" class="space-y-4">
            <label class="block text-gray-600 font-medium">Email:</label>
            <input type="email" name="email" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

            <label class="block text-gray-600 font-medium">Password:</label>
            <input type="password" name="password" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
                Login
            </button>
        </form>
    </div>

</body>
</html>
