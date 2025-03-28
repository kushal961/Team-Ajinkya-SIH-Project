<?php
session_start();
require 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role']; // Patient or Doctor

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Hash password and insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['role'] = $role;

                // Redirect based on role
                if ($role == 'doctor') {
                    header("Location: doctor_dashboard.php");
                } else {
                    header("Location: patient_dashboard.php");
                }
                exit();
            } else {
                $error = "Error registering user.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold text-blue-600 text-center mb-6">Register</h2>

        <?php if (isset($error)) { ?>
            <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>

        <form action="" method="POST" class="space-y-4">
            <label class="block text-gray-600 font-medium">Name:</label>
            <input type="text" name="name" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

            <label class="block text-gray-600 font-medium">Email:</label>
            <input type="email" name="email" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

            <label class="block text-gray-600 font-medium">Password:</label>
            <input type="password" name="password" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" minlength="6">

            <label class="block text-gray-600 font-medium">Register as:</label>
            <select name="role" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="patient">Patient</option>
                <option value="doctor">Doctor</option>
            </select>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
                Register
            </button>
        </form>

        <p class="text-center text-gray-600 mt-4">
            Already have an account? <a href="login.php" class="text-blue-600">Login here</a>
        </p>
    </div>

</body>
</html>



