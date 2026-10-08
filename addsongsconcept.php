<?php
require("./connect.php");
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .song-management-card {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 45px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 128, 179, 0.15);
            margin: 30px auto;
            max-width: 1100px; /* Increased width */
            width: 95%;
            min-height: 700px;
            transition: all 0.3s ease;
        }
        .song-card-header {
            background: linear-gradient(135deg, var(--rythm-pink), var(--rythm-deep-pink));
            padding: 30px 40px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .event-info-strip {
            background: #fffafa;
            border-bottom: 2px solid #fff2f6;
            padding: 20px 40px;
            display: flex; /* Changed to flex for better stability */
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 25px;
        }
        .info-item {
            flex: 1;
            min-width: 200px;
        }
        .info-item label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: var(--rythm-deep-pink);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 6px;
        }
        .info-item span {
            font-size: 18px;
            font-weight: 600;
            color: #222;
        }
        .song-section-title {
            text-align: center;
            margin: 40px 0 30px;
            position: relative;
        }
        .song-section-title h4 {
            background: white;
            display: inline-block;
            padding: 0 30px;
            position: relative;
            z-index: 1;
            color: var(--rythm-deep-pink);
            font-weight: 800;
            text-transform: uppercase;
            font-size: 20px;
        }
        .song-section-title::after {
            content: '';
            position: absolute;
            left: 10%;
            right: 10%;
            top: 50%;
            height: 1px;
            background: #eee;
        }
        .selection-panel {
            padding: 0 40px 40px;
        }
        .song-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Larger items */
            gap: 15px;
            margin-top: 25px;
        }
        .song-checkbox-item {
            background: #ffffff;
            border: 1.5px solid #f0f0f0;
            padding: 15px 25px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .song-checkbox-item:hover {
            border-color: var(--rythm-pink);
            background: #fffbfc;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 128, 179, 0.1);
        }
        .song-checkbox-item input[type="checkbox"]:checked + span {
            color: var(--rythm-deep-pink);
            font-weight: 700;
        }
        .song-checkbox-item input[type="checkbox"] {
            width: 22px;
            height: 22px;
            accent-color: var(--rythm-deep-pink);
        }
        .song-checkbox-item span {
            font-size: 16px;
            font-weight: 500;
            color: #444;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .custom-select-pink {
            border-radius: 10px;
            border: 2px solid #eee !important;
            padding: 8px 15px;
            outline: none;
            transition: border-color 0.2s;
        }
        .btn-premium {
            border-radius: 12px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-premium-save {
            background: var(--rythm-deep-pink);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 0, 127, 0.3);
        }
        .btn-premium-save:hover {
            background: #e60072;
            transform: scale(1.02);
            color: white;
        }
        .btn-premium-back {
            background: rgba(255,255,255,0.2);
            color: white;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.3);
        }
        .pair-input-container {
            background: #fdfdfd;
            border: 1px dashed #ddd;
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
        }
        .pair-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        .pair-row input {
            flex: 1;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 8px 12px;
        }
    </style>
</head>
<body>
    <?php
    $id = (int)($_REQUEST['id'] ?? 0);
$stmt = $con->prepare("SELECT * FROM daily_event WHERE id = ?");
$stmt->execute([$id]);
$eventdata = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$eventdata) {
    echo "<div style='padding:30px;color:#c00;'>Event not found. Please open this page from the Event List.</div>";
    exit;
}

$eventid     = $eventdata['id'];
$conceptname = $eventdata['title'];
$date        = $eventdata['date'];
$time        = $eventdata['time'];
$type = $_SESSION['title'] ?? '';
    ?>

    <div class="song-management-card">
        <form id="addSongForm" method="post" onsubmit="return saveSongs(event);">
            <input type="hidden" name="eventid" value="<?php echo $eventid; ?>">
            <input type="hidden" name="eventname" value="<?php echo $conceptname; ?>">
            <input type="hidden" name="eventdate" value="<?php echo $date; ?>">
            <input type="hidden" name="eventtime" value="<?php echo $time; ?>">

            <div class="song-card-header">
                <h3 class="mb-0 fw-bold">Add Songs To Event</h3>
                <a onclick="return_back()" class="btn btn-premium btn-premium-back text-white">
                    <i class="fa fa-arrow-left me-2"></i>Back
                </a>
            </div>

            <div class="event-info-strip text-dark">
                <div class="info-item">
                    <label>Event Name</label>
                    <span><?php echo strtoupper($conceptname); ?></span>
                </div>
                <div class="info-item">
                    <label>Event Date</label>
                    <span><?php echo $date; ?></span>
                </div>
                <div class="info-item">
                    <label>Scheduled Time</label>
                    <span><?php echo $time; ?></span>
                </div>
            </div>

            <div class="selection-panel">
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">Song Format</label>
                        <select id="rowCountSelect" name="soloDuteSelect" class="form-select custom-select-pink" onchange="changeoption(this.value)">
                            <option value="nd">-- Choose Format --</option>
                            <option value="solo">Solo Performance</option>
                            <option value="dute">Duet Performance</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3" id="numberofpair" style="display:none">
                        <label class="form-label small fw-bold text-muted">Number of Pairs</label>
                        <select name="pairCountSelect" class="form-select custom-select-pink" onchange="painnameshow(this.value)">
                            <option value="nd">-- Select Count --</option>
                            <?php for($i=1; $i<=10; $i++) echo "<option value='$i'>$i Pair".($i>1?'s':'')."</option>"; ?>
                        </select>
                    </div>
                </div>

                <div id="inputContainer" class="pair-input-container" style="display:none">
                    <!-- Dynamic inputs -->
                </div>

                <div class="song-section-title">
                    <h4>Available Song Catalog</h4>
                </div>

                <div class="song-grid">
                    <?php
                    $sql1 = $con->prepare("SELECT * FROM song_master WHERE singer_type = ? ORDER BY title");
                    $sql1->execute([$type]);
                    while ($row = $sql1->fetch()) {
                        ?>
                        <label class="song-checkbox-item">
                            <input type="checkbox" name="selectedSongs[]" value="<?php echo $row['id']; ?>">
                            <span><?php echo ucfirst($row['title']); ?></span>
                        </label>
                        <?php
                    }
                    ?>
                </div>

                    <div style="text-align:center; margin-top:25px; margin-bottom:20px;">
                    <button type="submit"
                            class="btn btn-premium btn-premium-save"
                            style="background:#ff1493; color:white; border:none; border-radius:25px; padding:10px 30px;">
                        <i class="fa fa-save me-2"></i>Update Songs
                    </button>
                </div>
            </div>   <!-- closes selection-panel -->
        </form>
    </div>           <!-- closes song-management-card -->

<script>
if (typeof jQuery === 'undefined') {
    document.write('<script src="https://code.jquery.com/jquery-3.6.4.min.js"><\/script>');
}
if (typeof showevent === 'undefined') {
    window.showevent = function () { window.location.href = '/rythm/home.php'; };
}
</script>

<script>
function return_back() {
    showevent();
}

function changeoption(val) {
    if (val == 'dute') {
        $("#numberofpair").fadeIn();
    } else {
        $("#numberofpair").fadeOut();
        $("#inputContainer").fadeOut();
    }
}

function painnameshow(val) {
    var container = $("#inputContainer");
    container.empty();
    if (val > 0 && val !== 'nd') {
        for (var i = 1; i <= val; i++) {
            container.append(
                '<div class="pair-row">' +
                '<div class="fw-bold small" style="width:100px;">Pair ' + i + '</div>' +
                '<input type="text" name="pairname[]" placeholder="Enter Performer Names" required>' +
                '</div>'
            );
        }
        container.fadeIn();
    } else {
        container.fadeOut();
    }
}

function saveSongs(e) {
    e.preventDefault();
    if ($('#rowCountSelect').val() === 'nd') {
        alert('Please choose Solo or Duet format');
        return false;
    }
    $.ajax({
        type: "POST",
        url: "/rythm/professional_singer/addsonginsert.php",
        data: $('#addSongForm').serialize(),
        success: function (response) {
            if (response.trim() === 'ok') {
                alert('Songs saved successfully!');
                showevent();
            } else {
                alert('Could not save: ' + response);
            }
        },
        error: function () {
            alert('Failed to reach the server.');
        }
    });
    return false;
}
</script>
</body>
</html>