<?php

session_start();
include("connect.php");

$username = $_SESSION['username'] ?? '';
$rolemaster_id = $_SESSION['role_master_id'] ?? '';

$alldata = $_REQUEST['alldata'] ?? '';

$splitthedata = explode('**', $alldata);

$posterid = $splitthedata[0] ?? '';
$commanderid = $splitthedata[1] ?? '';
$commands = $splitthedata[2] ?? '';

if ($posterid != '' && $commanderid != '' && $commands != '') {

    try {

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
            $rolemaster_id,
            $commands
        ]);

        if ($insertQuery) {
            echo "1";
        } else {
            echo "0";
        }

    } catch (PDOException $e) {

        error_log("Comment insert error: " . $e->getMessage());
        echo "0";
    }

} else {

    echo "0";
}

?>