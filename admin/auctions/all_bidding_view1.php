<?php
session_name('chitfund_admin');
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

/* Fetch all auctions with winner name */
$auctions = $conn->query("
    SELECT a.*, g.group_name, m.full_name AS winner_name
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    LEFT JOIN members m ON m.member_id = a.winner_member_id
    ORDER BY a.created_at DESC
");
?>
<!DOCTYPE html>
<html>

<head>
    <title>All Auction Bids</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="page-title">Live & Completed Auction Bids</div>
                    <div class="page-subtitle">Real-time bid monitoring</div>
                </div>
                <?php include '../layout/header.php'; ?>

            </div>

            <div class="content">

                <?php while ($a = $auctions->fetch_assoc()): ?>

                    <?php
                    /* Fetch bids for this auction */
                    $bids = $conn->query("
                        SELECT b.*, m.full_name
                        FROM auction_bids b
                        JOIN members m ON m.member_id = b.member_id
                        WHERE b.auction_id = {$a['id']}
                        ORDER BY b.bid_amount ASC
                    ");
                    ?>

                    <div class="auction-history-card">

                        <!-- HEADER -->
                        <div class="auction-header">
                            <div>
                                <div class="auction-title">
                                    <?= htmlspecialchars($a['group_name']) ?> – Month <?= $a['auction_month'] ?>
                                </div>
                                <small>AUC<?= str_pad($a['id'], 3, '0', STR_PAD_LEFT) ?> • <?= $a['auction_type'] ?></small>
                            </div>

                            <span class="badge <?= $a['status'] ?>">
                                <?= strtoupper($a['status']) ?>
                            </span>
                        </div>

                        <?php if ($a['auction_type'] === 'Open'): ?>
                            <!-- KULUKKAL (OPEN) AUCTION -->

                            <?php if ($a['status'] === 'active'): ?>
                                <div class="bid-highlight">
                                    <h2 style="color:#764ba2">🎰 Kulukkal Active</h2>
                                    <small>Waiting for spin...</small>
                                    <br>
                                    <a href="kulukkal_spin.php?auction_id=<?= $a['id'] ?>" class="btn-primary"
                                        style="margin-top:10px;display:inline-block">Go to Spin</a>
                                </div>
                            <?php else: ?>
                                <div class="auction-note">
                                    <b>Winner:</b> <?= htmlspecialchars($a['winner_name'] ?? $a['winner_member_id']) ?>
                                    (<?= $a['winner_member_id'] ?>)<br>
                                    <b>Winning Amount:</b> ₹<?= number_format($a['winning_bid_amount']) ?><br>
                                    <b>Kulukkal Date:</b> <?= date('d M Y, h:i A', strtotime($a['updated_at'])) ?>
                                </div>
                                <div class="bid-highlight">
                                    <h2 style="color:#16a34a">Winner Announced</h2>
                                    <small><?= htmlspecialchars($a['winner_name']) ?></small>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- REVERSE AUCTION -->

                            <!-- LIVE AUCTION -->
                            <?php if ($a['status'] === 'active'): ?>

                                <div class="bid-highlight">
                                    <small>Current Lowest Bid</small>
                                    <h2 id="lowest_<?= $a['id'] ?>">₹ —</h2>
                                    <small id="lowestBy_<?= $a['id'] ?>">Waiting for bids…</small>
                                </div>

                                <div id="bidList_<?= $a['id'] ?>"></div>

                            <?php else: ?>

                                <!-- COMPLETED AUCTION -->
                                <div class="auction-note">
                                    <b>Winner:</b> <?= htmlspecialchars($a['winner_name'] ?? $a['winner_member_id']) ?>
                                    (<?= $a['winner_member_id'] ?>)<br>
                                    <b>Winning Bid:</b> ₹<?= number_format($a['winning_bid_amount']) ?><br>
                                    <b>Total Discount:</b>
                                    ₹<?= number_format($a['starting_bid_amount'] - $a['winning_bid_amount']) ?>
                                </div>

                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Member</th>
                                            <th>Bid Amount</th>
                                            <th>Bid Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($b = $bids->fetch_assoc()): ?>
                                            <tr <?= $b['bid_amount'] == $a['winning_bid_amount'] ? 'style="background:#f0fdf4"' : '' ?>>
                                                <td><?= htmlspecialchars($b['full_name']) ?></td>
                                                <td>₹<?= number_format($b['bid_amount']) ?></td>
                                                <td><?= date('d/m/Y h:i:s a', strtotime($b['created_at'])) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>

                            <?php endif; ?>

                        <?php endif; ?>

                    </div>

                <?php endwhile; ?>

            </div>
        </div>
    </div>

    <script>
        /* ===============================
       LIVE REFRESH (ADMIN)
    ================================ */
        function loadLiveBids(auctionId) {
            fetch(`ajax/admin_live_bids.php?auction_id=${auctionId}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.bids) return;

                    let html = '';
                    data.bids.forEach((b, i) => {
                        html += `
                <div style="padding:6px 0">
                    ${b.full_name}
                    <span style="float:right">₹${b.bid_amount}</span>
                </div>`;
                    });

                    document.getElementById('bidList_' + auctionId).innerHTML = html;

                    if (data.lowest) {
                        document.getElementById('lowest_' + auctionId).innerText =
                            '₹' + data.lowest.bid_amount;
                        document.getElementById('lowestBy_' + auctionId).innerText =
                            'by ' + data.lowest.full_name;
                    }
                });
        }

        /* Auto refresh every 3 sec */
        setInterval(() => {
            <?php
            $auctions->data_seek(0);
            while ($a = $auctions->fetch_assoc()):
                if ($a['status'] === 'active'):
                    ?>
                    loadLiveBids(<?= $a['id'] ?>);
                <?php endif; endwhile; ?>
        }, 3000);
    </script>

</body>

</html>