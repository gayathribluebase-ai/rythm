<?php
session_start();

include("connect.php");


$username = $_SESSION['username'];
$sender_id = $_SESSION['users_id'];

$post_id = $_POST['post_id'] ?? '';
$tosenderid = $_POST['tosenderid'] ?? '';
$messagecontent = $_POST['messagecontent'] ?? '';

if ($post_id != '' && $tosenderid != '') {
  try {
    // Determine if multiple IDs (comma separated)
    $ids = explode(',', $tosenderid);
    $success = true;
    
    $stmt = $con->prepare("INSERT INTO `shareposter` (`posters_id`, `postfrom_id`, `postto_id`, `message_content`, `created_on`, `is_read`) VALUES (?, ?, ?, ?, NOW(), 0)");
    
    foreach ($ids as $to_id) {
        $to_id = trim($to_id);
        if(!empty($to_id)) {
            if ($stmt->execute([$post_id, $sender_id, $to_id, $messagecontent])) {

    // Add unread notification to existing messages table
    $notify = $con->prepare("
        INSERT INTO messages
        (sender_id, receiver_id, message, timestamp, is_read)
        VALUES (?, ?, ?, NOW(), 0)
    ");

    $notify->execute([
        $sender_id,
        $to_id,
        '__SHARED_POST_NOTIFICATION__'
    ]);

} else {

            $success = false;

        }
    }
}

echo ($success) ? 1 : 0;

} catch (PDOException $e) {

    echo 0;

}

} else {

    echo 0;

}
?>