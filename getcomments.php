<?php
session_start();
/**
 * Rythm Get Comments AJAX
 */
require_once("includes/config.php");

$userId = $_SESSION['users_id'] ?? 0;

if (!isset($_POST['post_id'])) {
    exit("Invalid Post ID");
}

$postId = $_POST['post_id'];


try {

    // Get the logged-in user's ID
    $userId = $_SESSION['users_id'] ?? 0;

    // Fetch comments, total likes, and current user's like status
    $sql = "
        SELECT
            c.*,
            u.user_name,
            u.profile_img,

            COALESCE(like_counts.total_likes, 0) AS comment_likes_count,

            CASE
                WHEN my_like.user_id IS NOT NULL THEN 1
                ELSE 0
            END AS liked_by_me

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

    // Display each comment once
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $now = time();
        $datediff = $now - strtotime($row['created_on']);
        $numofdays = round($datediff / (60 * 60 * 24));
        $date = ($numofdays == 0) ? 'Today' : $numofdays . 'd';

        $profile_pic = (!empty($row['profile_img']))
            ? $row['profile_img']
            : '/rythm/assets/images/lion.png';
?>

        <div class="comment-item d-flex gap-3 mb-4">

            <img
                src="<?php echo htmlspecialchars($profile_pic, ENT_QUOTES, 'UTF-8'); ?>"
                alt="User"
                class="rounded-circle border"
                style="width: 35px; height: 35px; object-fit: cover;"
            >

            <div class="comment-content">

                <p class="mb-1 small">
                    <span class="fw-bold me-2">
                        <?php echo htmlspecialchars($row['user_name']); ?>
                    </span>

                    <?php echo htmlspecialchars($row['commands']); ?>
                </p>

                <div class="d-flex gap-3 align-items-center x-small text-muted">

                    <span><?php echo $date; ?></span>

                    <div class="d-flex align-items-center gap-1">

                        <i
                            class="fa-<?php echo ($row['liked_by_me'] == 1) ? 'solid text-danger' : 'regular'; ?> fa-heart cursor-pointer toggle-comment-like"
                            data-id="<?php echo $row['id']; ?>"
                            data-post-id="<?php echo $postId; ?>"
                            data-status="<?php echo $row['liked_by_me']; ?>"
                            style="font-size: 14px;"
                        ></i>

                        <span
                            id="comment-like-count-<?php echo $row['id']; ?>"
                            class="fw-bold"
                        >
                            <?php
                            echo ($row['comment_likes_count'] > 0)
                                ? $row['comment_likes_count']
                                : '';
                            ?>
                        </span>

                    </div>

                    <button
                        type="button"
                        class="fw-bold cursor-pointer reply-comment-btn"
                        data-username="<?php echo htmlspecialchars($row['user_name'], ENT_QUOTES, 'UTF-8'); ?>"
                        style="border:none;background:none;padding:0;color:inherit;"
                    >
                        Reply
                    </button>

                </div>

            </div>

        </div>

<?php
    } // End of comments loop

    if ($stmt->rowCount() == 0) {
        echo "<div class='text-center text-muted py-5'><small>No comments yet. Be the first to comment!</small></div>";
    }

} catch (PDOException $e) {

    error_log($e->getMessage());
    echo "Error loading comments.";

}
?>
