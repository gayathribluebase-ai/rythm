<?php
/**
 * Rythm Unified Header - Professional Pink System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . "/config.php");

$username = $_SESSION['username'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Rythm'; ?></title>

    <!-- UI Core -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    
    <!-- Unified Assets -->
    <link rel="stylesheet" href="/rythm/assets/css/style.css">
    <?php if (isset($extraCSS)): ?>
        <link rel="stylesheet" href="<?php echo $extraCSS; ?>">
    <?php endif; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="navbar-custom d-flex align-items-center px-4 sticky-top">
        <div class="welcome-txt">Welcome!</div>

        <div class="search-box mx-auto d-none d-md-block search-wrapper">
        <input 
            type="text" 
            id="mainSearch" 
            placeholder="Search Users" 
            autocomplete="off">

        <div id="searchResultsDropdown"></div>
        </div>


        <div class="d-flex align-items-center gap-3">
            <span class="fw-bold d-none d-sm-inline"><?php echo htmlspecialchars($user_name); ?></span>
            <button class="btn btn-logout" onclick="location.href='/rythm/logout.php'">
                <i class="fa fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </header>

    <div class="app-wrapper">
        <?php include_once(__DIR__ . "/sidebar.php"); ?>
        <main class="main-content">


<style>

/* Search box container */
.search-wrapper {
    position: relative;
    width: 300px;
}

/* Search result dropdown */
#searchResultsDropdown {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    width: 100%;
    background: #ffffff;
    border: 1px solid #eeeeee;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    display: none;
    z-index: 99999;
    overflow: hidden;
}

/* Each user */
.search-result-user {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 10px 14px;
    gap: 12px;
    cursor: pointer;
    background: #ffffff;
    box-sizing: border-box;
}

/* Hover */
.search-result-user:hover {
    background: #f7f7f7;
}

/* Profile image */
.search-result-user img {
    width: 42px;
    height: 42px;
    min-width: 42px;
    border-radius: 50%;
    object-fit: cover;
    display: block;
}

/* Username */
.search-result-user-name {
    font-size: 14px;
    font-weight: 600;
    color: #222222;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* No result */
.search-no-result {
    padding: 15px;
    text-align: center;
    color: #777777;
    font-size: 14px;
    background: #ffffff;
}

</style>

<script>

function loadSearchProfile(userId) {

    $('#searchResultsDropdown').hide();
    $('#mainSearch').val('');

    $.ajax({
        type: 'GET',
        url: '/rythm/account_details.php',
        data: {
            id: userId
        },
        success: function(response) {

            $('#centerconteid').html(response);

        },
        error: function(xhr, status, error) {

            console.log('Profile loading failed:', error);
            console.log(xhr.responseText);

        }
    });
}    



$(document).ready(function () {

    $('#mainSearch').on('input', function () {

        let searchTerm = $(this).val().trim();

        if (searchTerm === '') {
            $('#searchResultsDropdown').hide().html('');
            return;
        }

        $.ajax({
            url: '/rythm/search_users.php',
            type: 'GET',
            data: {
                searchTerm: searchTerm
            },
            success: function (data) {

                $('#searchResultsDropdown')
                    .html(data)
                    .show();

            },
            error: function () {

                $('#searchResultsDropdown')
                    .html('<div class="search-no-result">Something went wrong</div>')
                    .show();

            }
        });

    });


    // Close dropdown when clicking outside
    $(document).on('click', function (e) {

        if (!$(e.target).closest('.search-wrapper').length) {
            $('#searchResultsDropdown').hide();
        }

    });

});

</script>

</body>
</html>
