<?php

/* =========================================================
   RYTHM - SEARCH RESULTS
   Searches:
   1. User name
   2. Post username
   3. Post caption
   4. Hashtag
   ========================================================= */

// Show PHP errors while testing
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Database connection
require_once("connect.php");


// Current logged-in user
$rolemaster_id = $_SESSION['role_master_id'] ?? 0;
$user_name     = $_SESSION['user_name'] ?? '';
$username      = $_SESSION['username'] ?? '';


// Get search value
$searchTerm = trim($_GET['searchTerm'] ?? '');


// ---------------------------------------------------------
// Empty search
// ---------------------------------------------------------
if ($searchTerm === '') {

    include("includes/header.php");
    ?>

    <div class="container py-5">
        <div class="search-result-box text-center">
            <h3>Please enter something to search.</h3>
            <p>Search for a username, music, caption or hashtag.</p>
        </div>
    </div>

    <?php
    include("includes/footer.php");
    exit;
}


// Search value for SQL LIKE
$search = "%" . $searchTerm . "%";


// Search users from user_master
$userStmt = $con->prepare("
    SELECT *
    FROM user_master
    WHERE user_name LIKE ?
");

$userStmt->execute([$search]);

$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);


// ---------------------------------------------------------
// Search users/posts
// ---------------------------------------------------------

$search = "%" . $searchTerm . "%";


$userStmt = $con->prepare("
    SELECT *
    FROM user_master
    WHERE user_name LIKE ?
");

$userStmt->execute([$search]);
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

$results = [];


// ---------------------------------------------------------
// Helper function
// ---------------------------------------------------------
function search_escape($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}


// ---------------------------------------------------------
// Header
// ---------------------------------------------------------
include("includes/header.php");

?>

<style>

.search-page {
    max-width: 1000px;
    margin: 30px auto;
    padding: 0 20px;
}

.search-title {
    margin-bottom: 25px;
}

.search-title h2 {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
}

.search-title p {
    margin-top: 7px;
    color: #777;
}

.search-result-card {
    background: #fff;
    border-radius: 14px;
    margin-bottom: 25px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.search-user {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
}

.search-profile-img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    background: #eee;
}

.search-user-name {
    font-size: 17px;
    font-weight: 600;
}

.search-post-image {
    width: 100%;
    max-height: 550px;
    object-fit: contain;
    border-radius: 12px;
    background: #f5f5f5;
    margin-bottom: 15px;
}

.search-post-video {
    width: 100%;
    max-height: 550px;
    border-radius: 12px;
    background: #000;
    margin-bottom: 15px;
}

.search-caption {
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 10px;
}

.search-hashtag {
    color: #007bff;
    font-weight: 600;
    margin-bottom: 10px;
}

.search-empty {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.search-empty h3 {
    margin-bottom: 10px;
}

.search-empty p {
    color: #777;
}

</style>


<div class="search-page">

    <!-- =====================================================
         SEARCH TITLE
         ===================================================== -->

    <div class="search-title">

        <h2>
            Search Results
        </h2>

        <p>
            Results for:
            <strong>
                "<?php echo search_escape($searchTerm); ?>"
            </strong>
        </p>

    </div>


    <?php

    // -------------------------------------------------------
// Display users
// -------------------------------------------------------

if (!empty($users)) {
?>

    <div class="search-result-card">

        <h3 style="margin-bottom: 20px;">Users</h3>

        <?php foreach ($users as $user) {

            $profile_img = $user['profile_img'] ?? '';

            if (empty($profile_img)) {
                $profile_img = '/rythm/assets/profile.png';
            }

$searched_user_name = $user['user_name'] ?? '';
?>
<div
    class="search-user"
    style="cursor: pointer;"
    onclick="window.location.href='/rythm/account_details.php?id=<?php echo (int)$user['users_id']; ?>';"
>

    <img
        src="<?php echo search_escape($profile_img); ?>"
        class="search-profile-img"
        alt="Profile"
        onerror="this.src='/rythm/assets/profile.png';"
    >

    <div class="search-user-name">
        <?php echo search_escape($searched_user_name); ?>
    </div>

</div>

        <?php } ?>

    </div>

<?php
}


// -------------------------------------------------------
// No users and no posts
// -------------------------------------------------------

if (empty($users) && empty($results)) {
?>

    <div class="search-empty">

        <h3>No results found</h3>

        <p>
            We couldn't find any users, posts or hashtags
            matching
            <strong>
                "<?php echo search_escape($searchTerm); ?>"
            </strong>
        </p>

    </div>

<?php
} else {

    // ---------------------------------------------------
    // Display posts / reels
    // ---------------------------------------------------

    foreach ($results as $data) {

            // Post ID
            $post_id = $data['id'] ?? 0;


            // ------------------------------------------------
            // Username
            // ------------------------------------------------

            $searched_user_name =
                $data['searched_user_name']
                ?? '';

            $post_username =
                $data['username']
                ?? '';


            if ($searched_user_name !== '') {

                $display_username =
                    $searched_user_name;

            } elseif ($post_username !== '') {

                $display_username =
                    $post_username;

            } else {

                $display_username =
                    'Unknown User';
            }


            // ------------------------------------------------
            // Profile image
            // ------------------------------------------------

            $profile_img =
                $data['searched_profile_img']
                ?? '';


            // Default profile image
            if (empty($profile_img)) {

                $profile_img =
                    '/rythm/assets/images/default-profile.png';
            }


            // ------------------------------------------------
            // Caption
            // ------------------------------------------------

            $caption =
                $data['posters_caption']
                ?? '';


            // ------------------------------------------------
            // Hashtag
            // ------------------------------------------------

            $hashtag =
                $data['posters_hashtag']
                ?? '';


            // ------------------------------------------------
            // Post image
            // ------------------------------------------------

            $post_image =
                $data['postimg']
                ?? '';


            // ------------------------------------------------
            // Post video
            // ------------------------------------------------

            $post_video =
                $data['postvideos']
                ?? '';

            ?>

            <div class="search-result-card">

                <!-- =========================================
                     USER
                     ========================================= -->

                <div class="search-user">

                    <img
                        src="<?php echo search_escape($profile_img); ?>"
                        class="search-profile-img"
                        alt="Profile"
                        onerror="this.style.display='none';"
                    >

                    <div class="search-user-name">

                        <?php
                        echo search_escape(
                            $display_username
                        );
                        ?>

                    </div>

                </div>


                <!-- =========================================
                     POST IMAGE
                     ========================================= -->

                <?php if (!empty($post_image)) { ?>

                    <img
                        src="<?php echo search_escape($post_image); ?>"
                        class="search-post-image"
                        alt="Post"
                        onerror="this.style.display='none';"
                    >

                <?php } ?>


                <!-- =========================================
                     POST VIDEO
                     ========================================= -->

                <?php if (!empty($post_video)) { ?>

                    <video
                        class="search-post-video"
                        controls
                    >

                        <source
                            src="<?php echo search_escape($post_video); ?>"
                        >

                        Your browser does not support video.

                    </video>

                <?php } ?>


                <!-- =========================================
                     CAPTION
                     ========================================= -->

                <?php if (!empty($caption)) { ?>

                    <div class="search-caption">

                        <?php
                        echo nl2br(
                            search_escape($caption)
                        );
                        ?>

                    </div>

                <?php } ?>


                <!-- =========================================
                     HASHTAG
                     ========================================= -->

                <?php if (!empty($hashtag)) { ?>

                    <div class="search-hashtag">

                        <?php
                        echo search_escape($hashtag);
                        ?>

                    </div>

                <?php } ?>


            </div>

            <?php

        }
    }

    ?>

</div>


<?php

// Footer
include("includes/footer.php");

?>
