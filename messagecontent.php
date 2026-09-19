<?php

include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postId = $_POST['post_id'] ?? '';

    if (empty($postId)) {
        echo json_encode([
            'error' => 'Invalid Post ID'
        ]);
        exit;
    }

    // Get post details + post owner's profile details
    $stmt = $con->prepare("
        SELECT
            p.id,
            p.post_type,
            p.postimg,
            p.postvideos,
            p.username,
            u.user_name,
            u.profile_img
        FROM posters p
        LEFT JOIN user_master u
            ON u.users_id = p.username_id
        WHERE p.id = ?
        LIMIT 1
    ");

    $stmt->execute([$postId]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {

        // Get latest like count
$likeStmt = $con->prepare("
    SELECT COUNT(*)
    FROM poster_likes
    WHERE post_id = ?
    AND like_status = 1
");

$likeStmt->execute([$postId]);

$likeCount = (int)$likeStmt->fetchColumn();

$response = [
    'username' => !empty($row['user_name'])
        ? $row['user_name']
        : $row['username'],

    'profileImg' => !empty($row['profile_img'])
        ? $row['profile_img']
        : '/rythm/assets/images/lion.png',

    'postType' => $row['post_type'],

    'likeCount' => $likeCount
];
        // Image post
        if ($row['post_type'] === 'image') {

            $response['content'] = $row['postimg'];

        }
        // Video post
        elseif ($row['post_type'] === 'video') {

            $response['content'] = $row['postvideos'];

        }
        // Unknown post type
        else {

            $response['content'] = '';

        }

        echo json_encode($response);

    } else {

        echo json_encode([
            'error' => 'Post not found'
        ]);

    }

} else {

    echo json_encode([
        'error' => 'Invalid request method'
    ]);

}

?>