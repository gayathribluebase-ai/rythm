<?php
session_start();
if (!isset($_SESSION['username'])) { echo "Please login again"; exit; }
require("../connect.php");
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$username   = $_SESSION['username'];
$singerType = $_SESSION['title'] ?? '';

$eventid = (int)($_POST['eventid'] ?? 0);
$format  = $_POST['soloDuteSelect'] ?? 'nd';

if ($eventid <= 0)    { echo "Invalid event"; exit; }
if ($format === 'nd') { echo "Please choose Solo or Duet format"; exit; }

// Take title/date/time from the database, not from the browser
$ev = $con->prepare("SELECT title, date, time FROM daily_event WHERE id = ?");
$ev->execute([$eventid]);
$event = $ev->fetch(PDO::FETCH_ASSOC);
if (!$event) { echo "Event not found"; exit; }

$songsid = implode(",", array_map('intval', (array)($_POST['selectedSongs'] ?? [])));

if ($format === 'dute') {
    $pairCount = (int)($_POST['pairCountSelect'] ?? 0);
    $names = array_filter(array_map('trim', (array)($_POST['pairname'] ?? [])));
    if ($pairCount <= 0 || count($names) === 0) {
        echo "Please select number of pairs and enter performer names"; exit;
    }
    $pairNames = implode(",", $names);
} else {
    $pairCount = 0;
    $pairNames = '';
}

try {
    $con->beginTransaction();

    // remove old selection for this event (this is what makes "Update" work)
    $con->prepare("DELETE FROM addsongsinevent WHERE eventid = ?")->execute([$eventid]);

    if ($songsid !== '') {
        $stmt = $con->prepare("INSERT INTO addsongsinevent
            (eventid, tilte, date, time, songslistid, pairtype, paircount, pairname, created_by, created_on, singer_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->execute([$eventid, $event['title'], $event['date'], $event['time'],
                        $songsid, $format, $pairCount, $pairNames, $username, $singerType]);
    }

    $con->commit();
    echo "ok";
} catch (Exception $e) {
    if ($con->inTransaction()) $con->rollBack();
    echo "Error: " . $e->getMessage();
}