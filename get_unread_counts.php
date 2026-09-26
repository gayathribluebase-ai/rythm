<?php

session_start();
require_once("includes/config.php");

if (!isset($_SESSION['users_id'])) {
    exit;
}

$login_user_id = $_SESSION['users_id'];

$counts = [];

/* ---------------------------------
   1. Unread normal messages
---------------------------------- */

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

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $sender_id = $row['sender_id'];

    $counts[$sender_id] = (int)$row['unread_count'];
}


/* ---------------------------------
   2. Unread shared posts
---------------------------------- */

$shareStmt = $con->prepare("
    SELECT
        postfrom_id,
        COUNT(*) AS unread_count
    FROM shareposter
    WHERE postto_id = ?
      AND is_read = 0
    GROUP BY postfrom_id
");

$shareStmt->execute([$login_user_id]);

while ($row = $shareStmt->fetch(PDO::FETCH_ASSOC)) {

    $sender_id = $row['postfrom_id'];
    $share_count = (int)$row['unread_count'];

    if (isset($counts[$sender_id])) {

        $counts[$sender_id] += $share_count;

    } else {

        $counts[$sender_id] = $share_count;

    }
}


/* ---------------------------------
   Return JSON
---------------------------------- */

header('Content-Type: application/json');

echo json_encode($counts);

?>