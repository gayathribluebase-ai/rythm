<?php
require("./connect.php");
session_start();
$conceptid = $_REQUEST['id'];
$query11 = $con->query("select * from `daily_event` where id='$conceptid'");
$row = $query11->fetch();

if (!$row) {
    echo "<div class='alert alert-danger mx-3 mt-4'>Event not found.</div>";
    exit;
}

$eventid = $row['id'];
$totalamount = $row['amount'];
$pendingamt = $totalamount;
$totalpaidamt = 0;

$amtsql = $con->query("SELECT sum(amount) as totalopaidamt FROM `eventpayment` WHERE eventid = $eventid");
if ($amtsql) {
    if ($amtsql->rowCount() > 0) {
        $paydata = $amtsql->fetch(PDO::FETCH_ASSOC);
        $totalpaidamt = $paydata['totalopaidamt'] ?? 0;
        $pendingamt = $totalamount - $totalpaidamt;
    }
}
?>

<div class="card border-0 shadow-sm rounded-4 mt-4 mx-3 overflow-hidden">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0 fw-bold text-pink">
            <i class="fa fa-info-circle me-2"></i>Event Details
        </h4>
        <button onclick="showevent()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fa fa-arrow-left me-1"></i> Back
        </button>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light small fw-bold text-uppercase">
                    <tr>
                        <th colspan="2" class="text-pink px-3">Primary Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="bg-light ps-3" style="width: 25%;">Event Name</th>
                        <td class="fw-bold ps-3"><?php echo ucfirst(htmlspecialchars($row['title'])); ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light ps-3">Date & Time</th>
                        <td class="ps-3">
                            <i class="fa fa-calendar-alt text-pink me-1"></i> <?php echo $row['date']; ?> 
                            <i class="fa fa-clock text-pink ms-3 me-1"></i> <?php echo $row['time']; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light ps-3">Organizer</th>
                        <td class="ps-3"><?php echo ucfirst(htmlspecialchars($row['organizer'])); ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light ps-3">Location</th>
                        <td class="ps-3"><?php echo htmlspecialchars($row['location'] ?? 'Not specified'); ?></td>
                    </tr>
                    <tr>
                        <th class="bg-light ps-3">Description</th>
                        <td class="ps-3 text-wrap"><?php echo ucfirst(htmlspecialchars($row['description'])); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Songs List Table -->
        <h6 class="fw-bold text-pink mt-4 mb-3"><i class="fa fa-music me-2"></i>Program Songs</h6>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-hover border">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 80px;">S.No</th>
                        <th>Format</th>
                        <th>Performer(s)</th>
                        <th>Song Title</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $id = $row['id'];
                    // Query the CORRECT table: addsongsinevent
                    
$sql1 = $con->prepare(
    "SELECT * FROM addsongsinevent WHERE eventid = ?"
);
$sql1->execute([$id]);

$hasSongs = false;
$cnt = 1;

while ($row1 = $sql1->fetch(PDO::FETCH_ASSOC)) {

    if (empty($row1['songslistid'])) {
        continue;
    }

    $songIds = array_filter(
        array_map('intval', explode(',', $row1['songslistid']))
    );

    if (empty($songIds)) {
        continue;
    }

    $placeholders = implode(',', array_fill(0, count($songIds), '?'));

    $sql2 = $con->prepare(
        "SELECT title FROM song_master WHERE id IN ($placeholders)"
    );
    $sql2->execute(array_values($songIds));

    $pairType = ucfirst($row1['pairtype'] ?? 'N/A');
    $pairNames = $row1['pairname'] ?? '-';

    while ($row2 = $sql2->fetch(PDO::FETCH_ASSOC)) {

        $hasSongs = true;

        echo "<tr>
                <td class='ps-3'>{$cnt}</td>
                <td>" . htmlspecialchars($pairType) . "</td>
                <td class='small text-muted'>" . htmlspecialchars($pairNames) . "</td>
                <td class='fw-bold'>" . htmlspecialchars(ucfirst($row2['title'])) . "</td>
              </tr>";

        $cnt++;
    }
}

if (!$hasSongs) {
    echo "<tr>
            <td colspan='4' class='text-center text-muted p-3'>
                No songs assigned to this event
            </td>
          </tr>";
}

                    ?>
                </tbody>
            </table>
        </div>

        <!-- Financial Details -->
        <h6 class="fw-bold text-pink mt-4 mb-3"><i class="fa fa-wallet me-2"></i>Financial Summary</h6>
        <div class="row g-3 mb-4 text-center">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 border">
                    <small class="text-uppercase text-muted fw-bold d-block mb-1">Total Budget</small>
                    <h5 class="mb-0 text-dark fw-bold">₹<?php echo number_format($totalamount); ?></h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 border">
                    <small class="text-uppercase text-muted fw-bold d-block mb-1">Received</small>
                    <h5 class="mb-0 text-success fw-bold">₹<?php echo number_format($totalpaidamt); ?></h5>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 border">
                    <small class="text-uppercase text-muted fw-bold d-block mb-1">Pending</small>
                    <h5 class="mb-0 text-danger fw-bold">₹<?php echo number_format($pendingamt); ?></h5>
                </div>
            </div>
        </div>

        <h6 class="fw-bold text-pink mb-3"><i class="fa fa-history me-2"></i>Payment History</h6>
        <div class="table-responsive">
            <table class="table table-striped border table-sm align-middle">
                <thead class="table-light small fw-bold text-uppercase">
                    <tr>
                        <th class="ps-3">SNo</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    $amtsql1 = $con->query("SELECT * FROM `eventpayment` WHERE eventid = $eventid");
                    if ($amtsql1 && $amtsql1->rowCount() > 0) {
                        while ($data2 = $amtsql1->fetch(PDO::FETCH_ASSOC)) {
                            $i++;
                            $type = ($data2['type'] == '1') ? 'Cash' : 'Bank Transfer';
                            ?>
                            <tr>
                                <td class="ps-3"><?php echo $i; ?></td>
                                <td><?php echo $data2['date']; ?></td>
                                <td class="fw-bold">₹<?php echo number_format($data2['amount']); ?></td>
                                <td><span class="badge bg-secondary rounded-pill"><?php echo $type; ?></span></td>
                            </tr>
                        <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center text-muted p-3'>No payments recorded yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
