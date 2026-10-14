
<?php
require("../connect.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = trim($_POST["date"] ?? "");
    $time = trim($_POST["time"] ?? "");
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $organizer = trim($_POST["organizer"] ?? "");
    $amount = trim($_POST["amount"] ?? "0");

    if (
        $date === "" || $time === "" || $title === "" ||
        $description === "" || $location === "" ||
        $organizer === "" || !is_numeric($amount) ||
        (float)$amount < 0
    ) {
        $message = "Please complete all fields correctly.";
    } else {
        try {
            $stmt = $con->prepare(
                "INSERT INTO daily_event
                (date, time, title, description, location, organizer, amount, singer_type)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $date, $time, $title, $description,
                $location, $organizer, $amount, "singer"
            ]);

            $message = "Event created successfully!";
echo "<script>
    alert('Event created successfully!');
    window.location.href = '/rythm/home.php';
</script>";
exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $message = "Unable to save the event. Please check your database columns.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create New Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff5fa;
            padding: 25px;
        }
        .event-form {
            max-width: 650px;
            margin: 20px auto;
            padding: 25px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 15px #00000015;
        }
        h2 { color: #ff1493; text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            margin-top: 6px;
            border: 1px solid #ddd;
            border-radius: 7px;
        }
        .save-btn {
            width: 100%;
            margin-top: 22px;
            padding: 12px;
            background: #ff1493;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        .message { text-align: center; color: #b00050; }
    </style>
</head>
<body>
    <div class="event-form">
        <h2>Create New Event</h2>

        <?php if ($message !== ""): ?>
            <p class="message">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label>Event Date</label>
            <input type="date" name="date" required>

            <label>Event Time</label>
            <input type="time" name="time" required>

            <label>Event Title</label>
            <input type="text" name="title" required>

            <label>Description</label>
            <textarea name="description" rows="3" required></textarea>

            <label>Location</label>
            <input type="text" name="location" required>

            <label>Organizer</label>
            <input type="text" name="organizer" required>

            <label>Registration Fee</label>
            <input type="number" name="amount" min="0" step="0.01" value="0" required>

            <label>Total Payment</label>
<input type="number" name="total_payment"
       min="0" step="0.01" required>

<label>Received Payment</label>
<input type="number" name="received_payment"
       min="0" step="0.01" value="0" required>

<label>Pending Payment</label>
<input type="number" id="pending_payment"
       value="0" readonly>

            <button type="submit" class="save-btn">Save Event</button>
        </form>
    </div>

    <script>
const totalInput = document.querySelector(
    '[name="total_payment"]'
);
const receivedInput = document.querySelector(
    '[name="received_payment"]'
);
const pendingInput = document.getElementById(
    'pending_payment'
);

function calculatePending() {
    const total = Number(totalInput.value) || 0;
    const received = Number(receivedInput.value) || 0;

    pendingInput.value = Math.max(0, total - received).toFixed(2);
}

totalInput.addEventListener('input', calculatePending);
receivedInput.addEventListener('input', calculatePending);
</script>

</body>
</html>
