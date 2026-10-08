<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo "Please login again";
    exit;
}
require("../connect.php");

$users_id = $_SESSION['users_id'];
$username = $_SESSION['username'];

$date        = $_POST['date'] ?? '';
$time        = $_POST['time'] ?? '';
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$location    = trim($_POST['location'] ?? '');
$organizer   = trim($_POST['organizer'] ?? '');
$amount      = $_POST['amount'] ?? 0;

if ($date === '' || $time === '' || $title === '') {
    echo "Please fill all required fields";
    exit;
}
if ($date < date('Y-m-d')) {
    echo "Cannot schedule events in the past";
    exit;
}

$stmt = $con->prepare("INSERT INTO daily_event
    (users_id, date, time, title, description, location, organizer, amount, songs, singer_type, created_on, createdby)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, '-', 'singer', NOW(), ?)");

$ok = $stmt->execute([$users_id, $date, $time, $title, $description, $location, $organizer, $amount, $username]);

echo $ok ? "ok" : "Something went wrong";