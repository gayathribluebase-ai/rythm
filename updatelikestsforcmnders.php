
<?php
session_start();
require_once("connect.php");

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Invalid request']));
}

$userId = (int)($_SESSION['users_id'] ?? 0);
$postId = $_POST['post_id'] ?? '';
$commentId = $_POST['commder_id'] ?? '';
$likeStatus = $_POST['like_status'] ?? '';

if (
    !$userId ||
    !ctype_digit((string)$postId) ||
    !ctype_digit((string)$commentId) ||
    !in_array((string)$likeStatus, ['0', '1'], true)
) {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid data']));
}

try {
    $check = $con->prepare(
        "SELECT id FROM posters_commads
         WHERE id = ? AND posterid = ?"
    );
    $check->execute([(int)$commentId, (int)$postId]);

    if (!$check->fetch()) {
        http_response_code(404);
        exit(json_encode(['error' => 'Comment not found']));
    }

    if ($likeStatus === '1') {
        $stmt = $con->prepare(
            "INSERT IGNORE INTO comment_likes (comment_id, user_id)
             VALUES (?, ?)"
        );
        $stmt->execute([(int)$commentId, $userId]);
    } else {
        $stmt = $con->prepare(
            "DELETE FROM comment_likes
             WHERE comment_id = ? AND user_id = ?"
        );
        $stmt->execute([(int)$commentId, $userId]);
    }

    $countQuery = $con->prepare(
        "SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?"
    );
    $countQuery->execute([(int)$commentId]);

    $count = (int)$countQuery->fetchColumn();

    echo json_encode([
        'count' => $count,
        'liked' => (int)$likeStatus
    ]);

} catch (PDOException $e) {
    error_log("Comment like error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
