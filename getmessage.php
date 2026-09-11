<?php
session_start();
include("includes/config.php");

if (!isset($_SESSION['users_id'])) {
    exit("unauthorized");
}

$sender = $_SESSION['users_id'];
$receiver = $_POST['user_id'] ?? 0;

if ($receiver == 0) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Get normal messages
|--------------------------------------------------------------------------
*/

$stmt = $con->prepare("
    SELECT 
        id,
        sender_id,
        receiver_id,
        message,
        timestamp,
        'message' AS content_type
    FROM messages
    WHERE (sender_id=? AND receiver_id=?)
       OR (sender_id=? AND receiver_id=?)
");

$stmt->execute([
    $sender,
    $receiver,
    $receiver,
    $sender
]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Get shared posts
|--------------------------------------------------------------------------
*/

$stmtShare = $con->prepare("
    SELECT
        sp.id,
        sp.posters_id,
        sp.postfrom_id,
        sp.postto_id,
        sp.message_content,
        sp.created_on,
        p.post_type,
        p.postimg,
        p.postvideos,
        p.username,
        'shared_post' AS content_type
    FROM shareposter sp
    LEFT JOIN posters p
        ON p.id = sp.posters_id
    WHERE (sp.postfrom_id=? AND sp.postto_id=?)
       OR (sp.postfrom_id=? AND sp.postto_id=?)
");

$stmtShare->execute([
    $sender,
    $receiver,
    $receiver,
    $sender
]);

$sharedPosts = $stmtShare->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Combine messages + shared posts
|--------------------------------------------------------------------------
*/

foreach ($items as &$item) {
    $item['sort_time'] = $item['timestamp'];
}
unset($item);

foreach ($sharedPosts as &$item) {
    $item['sort_time'] = $item['created_on'];
}
unset($item);

$allItems = array_merge($items, $sharedPosts);

usort($allItems, function ($a, $b) {
    return strtotime($a['sort_time']) <=> strtotime($b['sort_time']);
});

/*
|--------------------------------------------------------------------------
| Display
|--------------------------------------------------------------------------
*/

foreach ($allItems as $row) {

    /*
    |--------------------------------------------------------------------------
    | Normal message
    |--------------------------------------------------------------------------
    */

    if ($row['content_type'] === 'message') {

        $msg = htmlspecialchars(
            $row['message'],
            ENT_QUOTES,
            'UTF-8'
        );

        $time = date(
            'h:i A',
            strtotime($row['timestamp'])
        );

        if ($row['sender_id'] == $sender) {

            // SENDER - RIGHT

            echo '
            <div class="d-flex flex-column align-items-end mb-2">

                <div style="
                    background: var(--rythm-deep-pink);
                    color: #fff;
                    padding: 10px 18px;
                    border-radius: 20px 20px 0 20px;
                    max-width: 75%;
                    box-shadow: 0 4px 10px rgba(255, 0, 127, 0.1);
                ">
                    '.$msg.'
                </div>

                <small style="
                    font-size: 10px;
                    color: #aaa;
                    margin-top: 4px;
                    margin-right: 5px;
                ">
                    '.$time.'
                </small>

            </div>';
        }

        else {

            // RECEIVER - LEFT

            echo '
            <div class="d-flex flex-column align-items-start mb-2">

                <div style="
                    background: #f0f0f0;
                    color: #333;
                    padding: 10px 18px;
                    border-radius: 20px 20px 20px 0;
                    max-width: 75%;
                    border: 1px solid #eee;
                ">
                    '.$msg.'
                </div>

                <small style="
                    font-size: 10px;
                    color: #aaa;
                    margin-top: 4px;
                    margin-left: 5px;
                ">
                    '.$time.'
                </small>

            </div>';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Shared Post
    |--------------------------------------------------------------------------
    */

    else {

        $shareMessage = htmlspecialchars(
            $row['message_content'],
            ENT_QUOTES,
            'UTF-8'
        );

        $time = date(
            'h:i A',
            strtotime($row['created_on'])
        );

        $postMedia = '';

        if (!empty($row['postimg'])) {

        $postMedia = '
        <img src="' . htmlspecialchars(
        $row['postimg'],
        ENT_QUOTES,
        'UTF-8'
    ) . '"
    style="
        width:100%;
        max-height:350px;
        object-fit:cover;
        border-radius:12px;
        margin-top:10px;
    ">';

} elseif (!empty($row['postvideos'])) {

    $postMedia = '
    <video controls
        style="
            width:100%;
            max-height:350px;
            border-radius:12px;
            margin-top:10px;
        ">
        <source src="' . htmlspecialchars(
            $row['postvideos'],
            ENT_QUOTES,
            'UTF-8'
        ) . '" type="video/mp4">
    </video>';
}
            // SHARED POST SENT BY CURRENT USER - RIGHT

            if ($row['postfrom_id'] == $sender) {

            echo '
            <div style="
                    background:#fff;
                    padding:12px;
                    border-radius:15px 15px 0 15px;
                    max-width:75%;
                    border:1px solid #eee;
                    box-shadow:0 3px 10px rgba(0,0,0,0.08);
                ">

                    <div style="
                        font-size:12px;
                        color:#999;
                        margin-bottom:6px;
                    ">
                        📤 Shared a post from
                        <strong>'.htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8').'</strong>
                    </div>

                    <div style="
                        font-size:14px;
                        color:#333;
                    ">
                        '.$shareMessage.'
                    </div>

                    '.$postMedia.'

                </div>

                <small style="
                    font-size:10px;
                    color:#aaa;
                    margin-top:4px;
                    margin-right:5px;
                ">
                    '.$time.'
                </small>

            </div>';
            }
else {   

        // SHARED POST RECEIVED - LEFT

            echo '
            
            <div class="d-flex flex-column align-items-start mb-3">
                <div style="
                    background:#fff;
                    padding:12px;
                    border-radius:15px 15px 15px 0;
                    max-width:75%;
                    border:1px solid #eee;
                    box-shadow:0 3px 10px rgba(0,0,0,0.08);
                ">

                    <div style="
                        font-size:12px;
                        color:#999;
                        margin-bottom:6px;
                    ">
                        📥 Shared a post from 
                        <strong>'.htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8').'</strong>
                    </div>

                    <div style="
                        font-size:14px;
                        color:#333;
                    ">
                        '.$shareMessage.'
                    </div>

                    '.$postMedia.'

                </div>

                <small style="
                    font-size:10px;
                    color:#aaa;
                    margin-top:4px;
                    margin-left:5px;
                ">
                    '.$time.'
                </small>

            </div>';
        }
    }
}    
?>