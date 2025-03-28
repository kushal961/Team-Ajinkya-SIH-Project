<?php
require 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $qualification = $_POST['qualification'];
    $hospital = $_POST['hospital'];
    $experience = $_POST['experience'];
    $contact = $_POST['contact'];

    // Handle image upload
    $image = 'default.jpg'; // Default image
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        $target_dir = "images/";
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }

    // Insert data into database
    $sql = "INSERT INTO doctors (name, specialization, qualification, hospital, experience, contact, image) 
            VALUES ('$name', '$specialization', '$qualification', '$hospital', '$experience', '$contact', '$image')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Doctor registered successfully!'); window.location.href='doctors_list.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Doctor</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">

    <div class="container mx-auto px-6">
        <h1 class="text-3xl font-bold text-center text-green-600 mb-8">Doctor Registration</h1>

        <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-lg max-w-lg mx-auto">
            <label class="block text-gray-700 font-medium">Full Name:</label>
            <input type="text" name="name" required class="w-full p-2 border rounded mb-4">

            <label class="block text-gray-700 font-medium">Specialization:</label>
            <input type="text" name="specialization" required class="w-full p-2 border rounded mb-4">

            <label class="block text-gray-700 font-medium">Qualification:</label>
            <input type="text" name="qualification" required class="w-full p-2 border rounded mb-4">

            <label class="block text-gray-700 font-medium">Hospital:</label>
            <input type="text" name="hospital" required class="w-full p-2 border rounded mb-4">

            <label class="block text-gray-700 font-medium">Experience (Years):</label>
            <input type="number" name="experience" required class="w-full p-2 border rounded mb-4">

            <label class="block text-gray-700 font-medium">Contact:</label>
            <input type="text" name="contact" required class="w-full p-2 border rounded mb-4">

            <label class="block text-gray-700 font-medium">Upload Image:</label>
            <input type="file" name="image" accept="image/*" class="w-full p-2 border rounded mb-4">

            <button type="submit" class="bg-blue-500 text-white p-2 rounded w-full hover:bg-blue-600">Register</button>
        </form>
    </div>

</body>
</html>

