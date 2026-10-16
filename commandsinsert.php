
<?php
session_start();
require_once("connect.php");

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Invalid request method.');
}

$userId = (int)($_SESSION['users_id'] ?? 0);
$alldata = $_POST['alldata'] ?? '';
$parts = explode('**', $alldata, 3);

$posterId = isset($parts[0]) ? (int)$parts[0] : 0;
$comment = trim($parts[2] ?? '');

// No parent ID means this is a normal comment.
$parentValue = $_POST['parent_comment_id'] ?? '';
$parentCommentId = null;

if ($parentValue !== '') {
    if (!ctype_digit((string)$parentValue) || (int)$parentValue <= 0) {
        http_response_code(400);
        exit('Invalid parent comment ID.');
    }

    $parentCommentId = (int)$parentValue;
}

if ($userId <= 0) {
    http_response_code(401);
    exit('Please log in again.');
}

if ($posterId <= 0 || $comment === '') {
    http_response_code(400);
    exit('Invalid post or empty comment.');
}

try {
    // Confirm that the post exists.
    $checkPost = $con->prepare(
        "SELECT id FROM posters WHERE id = ? LIMIT 1"
    );
    $checkPost->execute([$posterId]);

    if (!$checkPost->fetchColumn()) {
        http_response_code(404);
        exit('Post not found.');
    }

    // If this is a reply, verify its parent belongs to this post.
    if ($parentCommentId !== null) {
        $checkParent = $con->prepare(
            "SELECT id FROM posters_commads
             WHERE id = ? AND posterid = ?
             AND parent_comment_id IS NULL
             LIMIT 1"
        );
        $checkParent->execute([$parentCommentId, $posterId]);

        if (!$checkParent->fetchColumn()) {
            http_response_code(400);
            exit('Parent comment not found.');
        }
    }

    $stmt = $con->prepare("
        INSERT INTO posters_commads
            (posterid, commander_id, commands,
             likests_cmd, likeorno, created_on, parent_comment_id)
        VALUES (?, ?, ?, 0, 0, NOW(), ?)
    ");

    $stmt->execute([
        $posterId,
        $userId,
        $comment,
        $parentCommentId
    ]);

    echo '1';

} catch (PDOException $e) {
    error_log('Comment insert error: ' . $e->getMessage());
    http_response_code(500);
    echo 'Database error while saving comment.';
}
?>
