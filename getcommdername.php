<?php

session_start();
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $commder_id = $_POST['commder_id'] ?? '';

    if (empty($commder_id)) {
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
            WHERE role_master_id = ?
            LIMIT 1
        ");

        $stmt->execute([$commder_id]);

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

        error_log("Get commenter name error: " . $e->getMessage());

        echo json_encode([
            'status' => 0,
            'username' => ''
        ]);
    }

} else {

    echo json_encode([
        'status' => 0,
        'username' => ''
    ]);
}

?>