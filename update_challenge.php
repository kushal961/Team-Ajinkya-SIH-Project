<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];

$updateQuery = "UPDATE user_challenges SET status = 'completed' 
                WHERE user_id = ? AND date_assigned = CURDATE()";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Challenge completed!"]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating challenge"]);
}
?>
