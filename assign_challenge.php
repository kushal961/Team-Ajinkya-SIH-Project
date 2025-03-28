<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id']; // Get logged-in user's ID

// Check if a challenge has already been assigned today
$checkQuery = "SELECT uc.id, c.challenge_text, uc.status 
               FROM user_challenges uc 
               JOIN challenges c ON uc.challenge_id = c.id 
               WHERE uc.user_id = ? AND uc.date_assigned = CURDATE()";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If challenge exists, return it
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    // Assign a new random challenge
    $challengeQuery = "SELECT id, challenge_text FROM challenges ORDER BY RAND() LIMIT 1";
    $result = $conn->query($challengeQuery);
    $challenge = $result->fetch_assoc();

    // Insert into user_challenges
    $insertQuery = "INSERT INTO user_challenges (user_id, challenge_id, status) VALUES (?, ?, 'pending')";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ii", $user_id, $challenge['id']);
    $stmt->execute();

    // Return assigned challenge
    echo json_encode($challenge);
}
?>
