<?php

session_start();
require_once("includes/config.php");

if (!isset($_SESSION['users_id'])) {
    exit;
}

$login_user_id = $_SESSION['users_id'];

$stmt = $con->prepare("
    SELECT 
        sender_id,
        COUNT(*) AS unread_count
    FROM messages
    WHERE receiver_id = ?
      AND is_read = 0
    GROUP BY sender_id
");

$stmt->execute([$login_user_id]);

$counts = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $counts[$row['sender_id']] = (int)$row['unread_count'];
}

header('Content-Type: application/json');

echo json_encode($counts);
?>