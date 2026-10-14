
<?php

session_start();
include("connect.php");

// Get logged-in user's email
$username = $_SESSION['username'] ?? '';

$alldata = $_REQUEST['alldata'] ?? '';
$splitthedata = explode('**', $alldata);

$posterid = $splitthedata[0] ?? '';
$commanderid = $splitthedata[1] ?? '';
$commands = $splitthedata[2] ?? '';

if ($username !== '' && $posterid !== '' && $commands !== '') {

    try {

        // Get the logged-in user's actual account ID
        $userStmt = $con->prepare("
            SELECT users_id
            FROM user_master
            WHERE email = ?
            LIMIT 1
        ");

        $userStmt->execute([$username]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "User not found in user_master.";
            exit;
        }

        if (empty($user['users_id'])) {
            echo "User found, but users_id is empty.";
            exit;
        }

        $actual_user_id = $user['users_id'];

        // Insert the comment
        $stmt = $con->prepare("
            INSERT INTO posters_commads
(
    posterid,
    commander_id,
    commands,
    likests_cmd,
    likeorno,
    created_on
)
            VALUES (?, ?, ?, 0, 0, NOW())
        ");

        $insertQuery = $stmt->execute([
            $posterid,
            $actual_user_id,
            $commands
        ]);

        echo $insertQuery ? "1" : "0";

    } catch (PDOException $e) {
    echo "DATABASE ERROR: " . $e->getMessage();
}

} else {

    echo "0";

}

?>
