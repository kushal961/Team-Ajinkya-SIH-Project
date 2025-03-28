<?php
require 'config.php'; // Include the database connection

// Fetch all doctors
$sql = "SELECT * FROM doctors";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Doctor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">

    <div class="container mx-auto px-6">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-8">Find a Doctor</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($doctor = $result->fetch_assoc()) { ?>
                <div class="bg-white p-6 rounded-lg shadow-lg flex flex-col items-center">
                    <img src="images/<?php echo $doctor['image']; ?>" alt="Doctor Image" class="w-32 h-32 rounded-full mb-4">
                    <h2 class="text-xl font-semibold text-gray-800"><?php echo $doctor['name']; ?></h2>
                    <p class="text-gray-600 font-medium"><?php echo $doctor['specialization']; ?></p>
                    <p class="text-gray-500">🎓 <?php echo $doctor['qualification']; ?></p>
                    <p class="text-gray-500">🏥 <?php echo $doctor['hospital']; ?></p>
                    <p class="text-gray-500">🕒 <?php echo $doctor['experience']; ?> years experience</p>
                    <p class="text-gray-500">📞 <?php echo $doctor['contact']; ?></p>
                </div>
            <?php } ?>
        </div>
    </div>

</body>
</html>
