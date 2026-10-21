
<?php
require_once("./connect.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conceptid = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

// Fetch the event only once
$query11 = $con->prepare(
    "SELECT * FROM daily_event WHERE id = ? LIMIT 1"
);
$query11->execute([$conceptid]);
$row = $query11->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "<div class='alert alert-danger mt-4'>Event not found.</div>";
    exit;
}

$eventid = (int)$row['id'];
$totalamount = (float)($row['amount'] ?? 0);

// Calculate payments received
$amtsql = $con->prepare(
    "SELECT COALESCE(SUM(amount), 0)
     FROM eventpayment
     WHERE eventid = ?"
);
$amtsql->execute([$eventid]);

$totalpaidamt = (float)$amtsql->fetchColumn();
$pendingamt = $totalamount - $totalpaidamt;
?>

<style>
.event-details-container {
    width: 100% !important;
    max-width: 500px;
    margin: 0 !important;
    padding: 0 20px 20px !important;
    box-sizing: border-box;
}

.event-details-container .card {
    width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    padding-top: 0 !important;

}

@media (max-width: 768px) {
    .event-details-container {
        padding: 0 12px 20px !important;

    }
}
</style>

<div class="event-details-container">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        <!-- Header -->
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0 fw-bold text-pink">
                <i class="fa fa-info-circle me-2"></i>Event Details
            </h4>

            <button type="button"
                    onclick="showevent()"
                    class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fa fa-arrow-left me-1"></i> Back
            </button>
        </div>

        <div class="card-body p-4">

            <!-- Primary Information -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light small fw-bold text-uppercase">
                        <tr>
                            <th colspan="2" class="text-pink px-3">
                                Primary Information
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="bg-light ps-3" style="width: 25%;">
                                Event Name
                            </th>
                            <td class="fw-bold ps-3">
                                <?= ucfirst(htmlspecialchars($row['title'] ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-3">Date &amp; Time</th>
                            <td class="ps-3">
                                <i class="fa fa-calendar-alt text-pink me-1"></i>
                                <?= htmlspecialchars($row['date'] ?? '') ?>

                                <i class="fa fa-clock text-pink ms-3 me-1"></i>
                                <?= htmlspecialchars($row['time'] ?? '') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-3">Organizer</th>
                            <td class="ps-3">
                                <?= ucfirst(htmlspecialchars($row['organizer'] ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-3">Location</th>
                            <td class="ps-3">
                                <?= htmlspecialchars($row['location'] ?? 'Not specified') ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light ps-3">Description</th>
                            <td class="ps-3 text-wrap">
                                <?= ucfirst(htmlspecialchars($row['description'] ?? '')) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Program Songs -->
            <h6 class="fw-bold text-pink mt-4 mb-3">
                <i class="fa fa-music me-2"></i>Program Songs
            </h6>

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
                        $sql1 = $con->prepare(
                            "SELECT * FROM addsongsinevent WHERE eventid = ?"
                        );
                        $sql1->execute([$eventid]);

                        $hasSongs = false;
                        $cnt = 1;

                        while ($row1 = $sql1->fetch(PDO::FETCH_ASSOC)) {
                            if (empty($row1['songslistid'])) {
                                continue;
                            }

                            $songIds = array_values(array_unique(array_filter(
                                array_map(
                                    'intval',
                                    explode(',', $row1['songslistid'])
                                ),
                                fn($id) => $id > 0
                            )));

                            if (empty($songIds)) {
                                continue;
                            }

                            $placeholders = implode(
                                ',',
                                array_fill(0, count($songIds), '?')
                            );

                            $sql2 = $con->prepare(
                                "SELECT title FROM song_master
                                 WHERE id IN ($placeholders)"
                            );
                            $sql2->execute($songIds);

                            $pairType = ucfirst($row1['pairtype'] ?? 'N/A');
                            $pairNames = $row1['pairname'] ?? '-';

                            while ($row2 = $sql2->fetch(PDO::FETCH_ASSOC)) {
                                $hasSongs = true;
                                ?>
                                <tr>
                                    <td class="ps-3"><?= $cnt ?></td>
                                    <td><?= htmlspecialchars($pairType) ?></td>
                                    <td class="small text-muted">
                                        <?= htmlspecialchars($pairNames) ?>
                                    </td>
                                    <td class="fw-bold">
                                        <?= htmlspecialchars(ucfirst($row2['title'] ?? '')) ?>
                                    </td>
                                </tr>
                                <?php
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

            <!-- Financial Summary -->
            <h6 class="fw-bold text-pink mt-4 mb-3">
                <i class="fa fa-wallet me-2"></i>Financial Summary
            </h6>

            <div class="row g-3 mb-4 text-center">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 border">
                        <small class="text-uppercase text-muted fw-bold d-block mb-1">
                            Total Budget
                        </small>
                        <h5 class="mb-0 text-dark fw-bold">
                            ₹<?= number_format($totalamount, 2) ?>
                        </h5>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 border">
                        <small class="text-uppercase text-muted fw-bold d-block mb-1">
                            Received
                        </small>
                        <h5 class="mb-0 text-success fw-bold">
                            ₹<?= number_format($totalpaidamt, 2) ?>
                        </h5>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 border">
                        <small class="text-uppercase text-muted fw-bold d-block mb-1">
                            Pending
                        </small>
                        <h5 class="mb-0 text-danger fw-bold">
                            ₹<?= number_format($pendingamt, 2) ?>
                        </h5>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <h6 class="fw-bold text-pink mb-3">
                <i class="fa fa-history me-2"></i>Payment History
            </h6>

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
                        $amtsql1 = $con->prepare(
                            "SELECT * FROM eventpayment
                             WHERE eventid = ?
                             ORDER BY date DESC"
                        );
                        $amtsql1->execute([$eventid]);

                        $i = 0;

                        while ($data2 = $amtsql1->fetch(PDO::FETCH_ASSOC)) {
                            $i++;
                            
$paymentType = $data2['type'] ?? null;

$type = ($paymentType === null || $paymentType === '')
    ? 'Not specified'
    : (($paymentType == '1') ? 'Cash' : 'Bank Transfer');

                            ?>
                            <tr>
                                <td class="ps-3"><?= $i ?></td>
                                <td><?= htmlspecialchars($data2['date'] ?? '') ?></td>
                                <td class="fw-bold">
                                    ₹<?= number_format((float)$data2['amount'], 2) ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary rounded-pill">
                                        <?= htmlspecialchars($type) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php
                        }

                        if ($i === 0) {
                            echo "<tr>
                                <td colspan='4' class='text-center text-muted p-3'>
                                    No payments recorded yet.
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
