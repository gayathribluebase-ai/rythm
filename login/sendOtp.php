<?php

include("connect.php");
session_start();

$receivedemail = $_REQUEST['Inputemail'];
$newpassword = $_REQUEST['InputPassword'];
// $newconfirmapassword = $_REQUEST['InputconfirmPassword'];

$md5password = md5($newpassword);

$_SESSION["fromailid"] = $receivedemail;
$_SESSION["newpasswordd"] = $md5password;
// $_SESSION["newconfirmmpasswordd"] = $newconfirmapassword;

$otp = rand(1000, 9999);

$subject = 'OTP Verification';
$emailbody = 'Your 4 Digit OTP Code: ';

include('smtp/PHPMailerAutoload.php');
$result = smtp_mailer($receivedemail, $subject, $emailbody . $otp);

function smtp_mailer($to, $subject, $msg)
{
    $mail = new PHPMailer();

    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;

    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';

    $mail->SMTPDebug = 2;

    $mail->Username = "gayathri.bluebase@gmail.com";
    $mail->Password = "rjxd tegh zdfa ncwd"; //Sender's Email App Password 

    $mail->SetFrom("gayathri.bluebase@gmail.com");

    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);

    if (!$mail->Send()) {
        die("Mailer Error: " . $mail->ErrorInfo);
    }

    return true;
}
        $updateQuery = $con->query("INSERT INTO `otptable`(`id`, `email`, `password`, `confirmpassword`, `otpcode`, `created_on`, `modify_on`) VALUES (NULL, '$receivedemail', '$md5password', '', '$otp', now(), '0000-00-00')");
if ($updateQuery) {
	echo "<script>alert('Mail Sent Successfully!')</script>";
	echo "<script>window.location.href='/rythm/login/otpverifypage.php'</script>";
} else {
	echo "<script>alert('Mail Sent Successfully! Database query failed.')</script>";
	echo "<script>window.location.href='/rythm/login/forgotpass.php'</script>";
}
