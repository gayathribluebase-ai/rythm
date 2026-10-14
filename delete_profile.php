<?php
session_start();
require_once("includes/config.php");

// Prevent errors from being displayed to users
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Check whether the user is logged in
if (!isset($_SESSION['users_id'])) {
    header("Location: /rythm/login/login.php");
    exit();
}

// Allow POST requests only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /rythm/profile.php");
    exit();
}

// Get the logged-in user's ID
$users_id = (int) $_SESSION['users_id'];

// Validate CSRF token
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token']
    )
) {
    http_response_code(403);
    exit("Invalid request. Please refresh and try again.");
}

try {

    /*
     * IMPORTANT:
     * This assumes $_SESSION['users_id']
     * contains the user_master.id value.
     */

    // Verify the logged-in user
    $check = $con->prepare(
        "SELECT id FROM user_master WHERE id = ?"
    );

    $check->execute([$users_id]);

    $user = $check->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_unset();
        session_destroy();

        header("Location: /rythm/login/login.php");
        exit();
    }

    // Delete the user's following/follower relationships
    $stmt = $con->prepare(
        "DELETE FROM following_details
         WHERE follower_id = ?
         OR following_id = ?"
    );

    $stmt->execute([$users_id, $users_id]);

    // Delete the user's own posts and reels
    $stmt = $con->prepare(
        "DELETE FROM posters
         WHERE username_id = ?"
    );

    $stmt->execute([$users_id]);

    // Delete the user account
    $stmt = $con->prepare(
        "DELETE FROM user_master
         WHERE id = ?"
    );

    $stmt->execute([$users_id]);

    // Clear the login session
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();

    // Redirect after successful deletion
    header(
        "Location: /rythm/login/login.php?deleted=1"
    );

    exit();

} catch (PDOException $e) {

    // Save the actual error in the Apache/PHP error log
    error_log(
        "Rythm profile deletion error: " .
        $e->getMessage()
    );

    http_response_code(500);

    exit(
        "Unable to delete your profile. " .
        "Please check the server error log."
    );
}