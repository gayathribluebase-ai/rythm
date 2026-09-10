<?php

session_start();
require_once("includes/config.php");

if (!isset($_SESSION['users_id'])) {
    exit("unauthorized");
}

$login_user_id = $_SESSION['users_id'];
$sender_id = $_POST['sender_id'] ?? 0;

if ($sender_id == 0) {
    exit;
}

$stmt = $con->prepare("
    UPDATE messages
    SET is_read = 1
    WHERE sender_id = ?
      AND receiver_id = ?
      AND is_read = 0
");

$stmt->execute([
    $sender_id,
    $login_user_id
]);

echo 1;
?>