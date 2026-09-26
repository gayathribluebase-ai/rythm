
<?php

session_start();
include("connect.php");

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 0,
        'username' => ''
    ]);
    exit;
}

// Get the commenter's user ID
$commenter_id = filter_input(
    INPUT_POST,
    'commder_id',
    FILTER_VALIDATE_INT
);

if (!$commenter_id || $commenter_id <= 0) {
    echo json_encode([
        'status' => 0,
        'username' => ''
    ]);
    exit;
}

try {

    $stmt = $con->prepare("
        SELECT user_name
        FROM user_master
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$commenter_id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            'status' => 1,
            'username' => $user['user_name']
        ]);
    } else {
        echo json_encode([
            'status' => 0,
            'username' => ''
        ]);
    }

} catch (PDOException $e) {

    error_log(
        "Get commenter name error: " . $e->getMessage()
    );

    http_response_code(500);

    echo json_encode([
        'status' => 0,
        'username' => ''
    ]);
}

exit;
?>
