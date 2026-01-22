<?php
include '../auth.php';
include '../../config/database.php';

$auctionId = (int) ($_GET['auction_id'] ?? 0);
if (!$auctionId)
    die('Auction ID missing');

/* Auction details with winner name */
$stmt = $conn->prepare("
    SELECT a.*, g.group_name, m.full_name as winner_name
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    LEFT JOIN members m ON m.member_id = a.winner_member_id
    WHERE a.id = ?
");
$stmt->bind_param("i", $auctionId);
$stmt->execute();
$auction = $stmt->get_result()->fetch_assoc();

if (!$auction)
    die('Auction not found');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Live Bidding View</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">
            <div class="topbar">
                <div>
                    <div class="page-title">
                        <?= htmlspecialchars($auction['group_name']) ?> – Month <?= $auction['auction_month'] ?>
                    </div>
                    <div class="page-subtitle">
                        Auction ID #<?= $auctionId ?> · Status: <?= ucfirst($auction['status']) ?> · Type:
                        <?= $auction['auction_type'] ?>
                    </div>
                </div>
                <?php include '../layout/header.php'; ?>
            </div>

            <div class="content">

                <?php if ($auction['auction_type'] === 'Open'): ?>

                    <!-- OPEN AUCTION (KULUKKAL) LAYOUT -->
                    <div class="box">
                        <h3>🎰 Kulukkal Lottery Status</h3>

                        <?php if ($auction['status'] === 'active'): ?>
                            <h1 style="color:#764ba2">Active</h1>
                            <p>Ready to spin the wheel.</p>
                            <a href="kulukkal_spin.php?auction_id=<?= $auctionId ?>" class="btn-primary">Go to Spin Page</a>
                        <?php elseif ($auction['status'] === 'completed'): ?>
                            <h1 style="color:#16a34a">Completed</h1>
                            <p>Winner has been announced.</p>
                        <?php else: ?>
                            <h1>Status: <?= $auction['status'] ?></h1>
                        <?php endif; ?>
                    </div>

                    <!-- WINNER BOX (Always show if completed, even if AJAX delayed) -->
                    <?php if ($auction['status'] === 'completed'): ?>
                        <div class="box" id="winnerBoxStatic" style="border-left: 5px solid #16a34a;">
                            <h3>🏆 Winning Member</h3>
                            <h2><?= htmlspecialchars($auction['winner_name'] ?? $auction['winner_member_id']) ?></h2>
                            <p>Winning Amount: <b>₹<?= number_format($auction['winning_bid_amount']) ?></b></p>
                        </div>
                    <?php endif; ?>

                <?php else: ?>

                    <!-- REVERSE AUCTION LAYOUT -->

                    <?php if ($auction['status'] === 'completed'): ?>
                        <!-- COMPLETED REVERSE -->
                        <div class="box" style="border-left: 5px solid #16a34a;">
                            <h3>🏆 Winning Member</h3>
                            <h2><?= htmlspecialchars($auction['winner_name'] ?? $auction['winner_member_id']) ?></h2>
                            <p>Winning Amount: <b>₹<?= number_format($auction['winning_bid_amount']) ?></b></p>
                            <p>Date: <?= date('d M Y, h:i A', strtotime($auction['auction_end_datetime'])) ?></p>
                        </div>
                    <?php else: ?>
                        <!-- ACTIVE/UPCOMING -->
                        <div class="box">
                            <h3>Current Lowest Bid</h3>
                            <h1 id="lowestBid">₹ —</h1>
                            <small id="lowestBidBy">Waiting for bids</small>
                        </div>
                    <?php endif; ?>

                    <!-- ALL BIDS -->
                    <div class="table-box">
                        <h3>All Bids</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Bid Amount</th>
                                    <th>Bid Time</th>
                                </tr>
                            </thead>
                            <tbody id="bidTable">
                                <tr>
                                    <td colspan="3">Loading…</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- WINNER (for Reverse - AJAX fallback, mostly hidden if static is shown) -->
                    <div class="box" id="winnerBox" style="display:none;">
                        <h3>🏆 Winning Member</h3>
                        <h2 id="winnerName"></h2>
                        <h3 id="winnerAmount"></h3>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>

    <script>
        const auctionId = <?= $auctionId ?>;
        const type = "<?= $auction['auction_type'] ?>";

        if (type !== 'Open') {
            /* Only load bids for Reverse Auction */

            function loadBids() {
                fetch(`ajax/get_all_bids.php?auction_id=${auctionId}`)
                    .then(r => r.json())
                    .then(bids => {
                        let html = '';
                        if (bids.length === 0) {
                            html = `<tr><td colspan="3">No bids yet</td></tr>`;
                        } else {
                            bids.forEach(b => {
                                html += `
                            <tr>
                                <td>${b.full_name}</td>
                                <td>₹${b.bid_amount}</td>
                                <td>${b.created_at}</td>
                            </tr>`;
                            });
                        }
                        document.getElementById('bidTable').innerHTML = html;
                    });
            }

            /* Lowest bid */
            function loadLowest() {
                fetch(`ajax/get_lowest_bid.php?auction_id=${auctionId}`)
                    .then(r => r.json())
                    .then(b => {
                        if (b.bid_amount) {
                            lowestBid.innerText = '₹' + b.bid_amount;
                            lowestBidBy.innerText = 'by ' + b.full_name;
                        }
                    });
            }

            function loadWinner() {
                fetch(`ajax/get_winner.php?auction_id=${auctionId}`)
                    .then(r => r.json())
                    .then(w => {
                        if (w.full_name) {
                            document.getElementById('winnerBox').style.display = 'block';
                            document.getElementById('winnerName').innerText = w.full_name;
                            document.getElementById('winnerAmount').innerText =
                                'Winning Bid: ₹' + w.winning_bid_amount;
                        }
                    });
            }

            /* Auto refresh */
            setInterval(() => {
                loadBids();
                loadLowest();
                loadWinner();
            }, 4000);

            loadBids();
            loadLowest();
            loadWinner();
        }
    </script>
</body>

</html>