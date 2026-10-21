
<?php
session_start();
require_once("includes/config.php");

$userId = (int)($_SESSION['users_id'] ?? 0);

if (!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
    http_response_code(400);
    exit("Invalid Post ID");
}

$postId = (int)$_POST['post_id'];

try {
    
$sql = "
    SELECT
        c.*,
        u.user_name,
        u.profile_img,
        COALESCE(like_counts.total_likes, 0) AS comment_likes_count,
        CASE WHEN my_like.user_id IS NOT NULL THEN 1 ELSE 0 END AS liked_by_me
    FROM posters_commads c
    LEFT JOIN user_master u
        ON c.commander_id = u.users_id
    LEFT JOIN (
        SELECT comment_id, COUNT(*) AS total_likes
        FROM comment_likes
        GROUP BY comment_id
    ) like_counts
        ON c.id = like_counts.comment_id
    LEFT JOIN comment_likes my_like
        ON c.id = my_like.comment_id
        AND my_like.user_id = ?
    WHERE c.posterid = ?
    ORDER BY c.created_on DESC
";

    
$stmt = $con->prepare($sql);
$stmt->execute([$userId, $postId]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);


       if (empty($comments)) {
        echo "<div class='text-center text-muted py-4'>
                <small>No comments yet. Be the first to comment!</small>
              </div>";
        exit;
    }

    foreach ($comments as $row) {
        $commentId = (int)$row['id'];
        $username = $row['user_name'] ?? 'Unknown user';
        $commentText = $row['commands'] ?? '';
        $liked = (int)$row['liked_by_me'];
        $likeCount = (int)$row['comment_likes_count'];

        $profilePic = !empty($row['profile_img'])
            ? $row['profile_img']
            : '/rythm/assets/images/lion.png';

        $created = !empty($row['created_on'])
            ? strtotime($row['created_on'])
            : false;

        $days = $created ? (int)round((time() - $created) / 86400) : 0;
        $date = $days <= 0 ? 'Today' : $days . 'd';
        ?>

        <div class="comment-item d-flex gap-3 mb-4">
            <img
                src="<?= htmlspecialchars($profilePic, ENT_QUOTES, 'UTF-8') ?>"
                alt="User"
                class="rounded-circle border"
                style="width:35px;height:35px;object-fit:cover;"
            >

            <div class="comment-content flex-grow-1">
                <p class="mb-1 small">
                    <span class="fw-bold me-2">
                        <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?= htmlspecialchars($commentText, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <div class="d-flex gap-3 align-items-center text-muted"
                     style="font-size:12px;">

                    <span><?= htmlspecialchars($date) ?></span>

                    <button
                        type="button"
                        class="toggle-comment-like"
                        data-id="<?= $commentId ?>"
                        data-post-id="<?= $postId ?>"
                        data-status="<?= $liked ?>"
                        aria-label="<?= $liked ? 'Unlike comment' : 'Like comment' ?>"
                        style="border:0;background:none;padding:0;cursor:pointer;"
                    >
                        <i class="<?= $liked ? 'fa-solid text-danger' : 'fa-regular' ?> fa-heart"
                           style="font-size:15px;"></i>
                        <span id="comment-like-count-<?= $commentId ?>">
                            <?= $likeCount > 0 ? $likeCount : '' ?>
                        </span>
                    </button>

                    <button
    type="button"
    class="fw-bold reply-comment-btn"
    data-comment-id="<?= (int)$row['id'] ?>"
    data-username="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
    style="border:0;background:none;padding:0;color:inherit;cursor:pointer;"
>
    Reply
</button>                </div>
            </div>
        </div>

        <?php
    }

} catch (PDOException $e) {
    error_log("getcomments.php error: " . $e->getMessage());
    http_response_code(500);
    echo "Database error: " .
         htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}   
?>
