<?php
$pdo = new PDO("mysql:host=localhost;dbname=chatdb", "root", "");
$query = $pdo->query("SELECT * FROM chat_messages ORDER BY created_at DESC");
$messages = $query->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($messages);
?>
