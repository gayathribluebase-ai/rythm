<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("connect.php");

$searchTerm = trim($_GET['searchTerm'] ?? '');

if ($searchTerm === '') {
    exit;
}

$search = "%" . $searchTerm . "%";

$stmt = $con->prepare("
    SELECT users_id, user_name, profile_img
    FROM user_master
    WHERE user_name LIKE ?
    ORDER BY user_name ASC
    LIMIT 10
");

$stmt->execute([$search]);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (empty($users)) {
    echo '<div class="search-no-result">No users found</div>';
    exit;
}


foreach ($users as $user) {

    $user_id = (int)$user['users_id'];

    $user_name = htmlspecialchars(
        $user['user_name'] ?? '',
        ENT_QUOTES,
        'UTF-8'
    );

    $profile_img = $user['profile_img'] ?? '';

    if (empty($profile_img)) {
        $profile_img = '/rythm/assets/profile.png';
    }

    $profile_img = htmlspecialchars(
        $profile_img,
        ENT_QUOTES,
        'UTF-8'
    );
?>

    <div
    class="search-result-user"
    onclick="loadSearchProfile(<?php echo $user_id; ?>);"
    >

        <img
            src="<?php echo $profile_img; ?>"
            alt="Profile"
            onerror="this.src='/rythm/assets/profile.png';"
        >

        <div class="search-result-user-name">
            <?php echo $user_name; ?>
        </div>

    </div>

<?php
}
?>