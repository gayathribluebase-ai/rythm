<?php

session_start();
require('connect.php');

$loggedInUserId = $_SESSION['users_id'] ?? 0;

if (!$loggedInUserId) {
    echo "0";
    exit;
}

/*
 * Remove profile picture only for the logged-in user
 */
$stmt = $con->prepare("
    UPDATE user_master
    SET profile_img = NULL
    WHERE id = ?
");

$success = $stmt->execute([$loggedInUserId]);

if ($success) {
    echo "1";
} else {
    echo "0";
}

?>