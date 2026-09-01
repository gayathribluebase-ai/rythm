<?php

include("connect.php");
session_start();

$code1 = $_POST['digit1'];
$code2 = $_POST['digit2'];
$code3 = $_POST['digit3'];
$code4 = $_POST['digit4'];

$inputotpcode = $code1 . $code2 . $code3 . $code4;

if (isset($_SESSION["fromailid"], $_SESSION["newpasswordd"])) {

    $receivedEmail = $_SESSION["fromailid"];
    $newPassword = $_SESSION["newpasswordd"];

    $sql = $con->query("SELECT * FROM `otptable`
                        WHERE email='$receivedEmail'
                        AND password='$newPassword'
                        ORDER BY id DESC");

    $row = $sql->fetch(PDO::FETCH_ASSOC);

    if ($row) {

        $tableotp = $row['otpcode'];

        if ($tableotp == $inputotpcode) {

            // Password is already MD5 hashed
            $newinputPassword = $newPassword;

            $sql2 = $con->query("UPDATE `user_master`
                                 SET `password`='$newinputPassword'
                                 WHERE email='$receivedEmail'");

            if ($sql2) {
                echo 1;
            } else {
                echo 2;
            }

        } else {
            echo 3;
        }

    } else {
        echo 2;
    }

} else {
    echo 4;
}
?>