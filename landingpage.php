<?php
session_start();
require 'config.php'; // Database connection

// Fetch doctors from the database
$sql = "SELECT * FROM doctors";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find & Book Doctors</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    
    <!-- Navbar -->
    <nav class="bg-blue-600 shadow-md fixed top-0 left-0 w-full z-50 py-4">
        <div class="container mx-auto flex justify-between items-center px-6">
            <a href="index.php" class="text-2xl font-bold text-white hover:text-gray-300 transition">HealthCare+</a>
            <ul class="hidden md:flex space-x-6">
                <li><a href="#features" class="text-white px-4 py-2 rounded-lg hover:bg-blue-500 transition">Features</a></li>
                <li><a href="#about" class="text-white px-4 py-2 rounded-lg hover:bg-blue-500 transition">About</a></li>
                <li><a href="#testimonials" class="text-white px-4 py-2 rounded-lg hover:bg-blue-500 transition">Testimonials</a></li>
                <li><a href="#contact" class="text-white px-4 py-2 rounded-lg hover:bg-blue-500 transition">Contact</a></li>
            </ul>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <a href="logout.php" class="hidden md:block bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-200 transition">Logout</a>
            <?php } else { ?>
                <a href="login.php" class="hidden md:block bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-200 transition">Login</a>
            <?php } ?>
        </div>
    </nav>

    <section class="container mx-auto mt-24 p-10">
        <h2 class="text-4xl font-bold text-center mb-8 text-blue-700">Find & Book Doctors</h2>
        
        <!-- Search Feature -->
        <div class="flex justify-center mb-6">
            <input type="text" id="search" placeholder="Search by specialization or location..." class="p-3 w-1/2 border rounded-lg shadow-md focus:outline-none">
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8" id="doctorList">
            <?php while ($doctor = $result->fetch_assoc()) { ?>
                <div class="bg-white p-6 rounded-lg shadow-lg transform transition hover:scale-105 doctor-card" data-specialization="<?php echo strtolower($doctor['specialization']); ?>" data-location="<?php echo strtolower($doctor['location']); ?>">
                    <h3 class="text-xl font-semibold text-blue-700">Dr. <?php echo htmlspecialchars($doctor['name']); ?></h3>
                    <p class="text-gray-600 font-medium">Specialization: <?php echo htmlspecialchars($doctor['specialization']); ?></p>
                    <p class="text-gray-600">Location: <?php echo htmlspecialchars($doctor['location']); ?></p>
                    <button class="mt-4 px-5 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition" onclick="bookAppointment(<?php echo $doctor['id']; ?>)">Book Appointment</button>
                </div>
            <?php } ?>
        </div>
    </section>

    <footer class="text-center p-6 text-gray-600 mt-10">
        <p>&copy; 2025 Healthcare Platform. All rights reserved.</p>
    </footer>

    <script>
        function bookAppointment(doctorId) {
            console.log("Doctor ID:", doctorId);
            if (!doctorId) {
                alert("Error: Doctor ID is missing!");
                return;
            }
            window.location.href = 'book.php?doctor_id=' + doctorId;
        }

        // Search functionality
        document.getElementById('search').addEventListener('input', function() {
            let filter = this.value.toLowerCase();
            let doctors = document.querySelectorAll('.doctor-card');
            
            doctors.forEach(function(doctor) {
                let specialization = doctor.getAttribute('data-specialization');
                let location = doctor.getAttribute('data-location');
                
                if (specialization.includes(filter) || location.includes(filter)) {
                    doctor.style.display = "block";
                } else {
                    doctor.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>
