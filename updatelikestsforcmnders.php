
<?php
session_start();
require_once("connect.php");

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Invalid request");
}

// Logged-in user's ID
$user_id = $_SESSION['users_id'] ?? 0;

$postId = $_POST['post_id'] ?? '';
$commentId = $_POST['commder_id'] ?? '';
$likeStatus = $_POST['like_status'] ?? '';

if (
    !$user_id ||
    !ctype_digit((string)$postId) ||
    !ctype_digit((string)$commentId) ||
    !in_array((string)$likeStatus, ['0', '1'], true)
) {
    http_response_code(400);
    exit("Invalid data");
}

try {

    // Verify that the comment belongs to the post
    $checkComment = $con->prepare(
        "SELECT id FROM posters_commads
         WHERE id = ? AND posterid = ?"
    );
    $checkComment->execute([$commentId, $postId]);

    if (!$checkComment->fetch()) {
        http_response_code(404);
        exit("Comment not found");
    }

    // Add or remove the logged-in user's like
if ((string)$likeStatus === '1') {

    $stmt = $con->prepare(
        "INSERT IGNORE INTO comment_likes
         (comment_id, user_id)
         VALUES (?, ?)"
    );

    $stmt->execute([$commentId, $user_id]);

} else {

    $stmt = $con->prepare(
        "DELETE FROM comment_likes
         WHERE comment_id = ? AND user_id = ?"
    );

    $stmt->execute([$commentId, $user_id]);
}

// Get the updated total count
$countQuery = $con->prepare(
    "SELECT COUNT(*)
     FROM comment_likes
     WHERE comment_id = ?"
);

$countQuery->execute([$commentId]);

$count = (int)$countQuery->fetchColumn();

// Return count and current user's like status
echo json_encode([
    'count' => $count,
    'liked' => (int)$likeStatus
]);

exit;

    // Count all users who liked this comment
    $countQuery = $con->prepare(
        "SELECT COUNT(*) FROM comment_likes
         WHERE comment_id = ?"
    );
    $countQuery->execute([$commentId]);

    echo $countQuery->fetchColumn() . "likes";

} catch (PDOException $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo "Database error";
}
?>
