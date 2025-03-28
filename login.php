<?php
session_start();
require 'config.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Fetch user details from database
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 'doctor') {
                    header("Location: doctor_dashboard.php");
                } else {
                    header("Location: patient_dashboard.php");
                }
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with this email.";
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
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold text-blue-600 text-center mb-6">Login</h2>

        <?php if (isset($error)) { ?>
            <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>

        <form action="" method="POST" class="space-y-4">
            <label class="block text-gray-600 font-medium">Email:</label>
            <input type="email" name="email" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

            <label class="block text-gray-600 font-medium">Password:</label>
            <input type="password" name="password" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
                Login
            </button>
        </form>

        <p class="text-center text-gray-600 mt-4">
            Don't have an account? <a href="register.php" class="text-blue-600">Register here</a>
        </p>
    </div>

</body>
</html>



