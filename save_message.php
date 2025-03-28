<?php
$pdo = new PDO("mysql:host=localhost;dbname=chatdb", "root", "");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO chat_messages (username, message) VALUES (?, ?)");
    $stmt->execute([$username, $message]);
}
?>
