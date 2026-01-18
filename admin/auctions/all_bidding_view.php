<?php
include '../auth.php';
include '../../config/database.php';

$auctionId = (int)($_GET['auction_id'] ?? 0);
if (!$auctionId) die('Auction ID missing');

/* Auction details */
$stmt = $conn->prepare("
    SELECT a.*, g.group_name
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    WHERE a.id = ?
");
$stmt->bind_param("i", $auctionId);
$stmt->execute();
$auction = $stmt->get_result()->fetch_assoc();

if (!$auction) die('Auction not found');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Live Bidding View</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            Auction ID #<?= $auctionId ?> · Status: <?= ucfirst($auction['status']) ?>
        </div>
    </div>
    <?php include '../layout/header.php'; ?>
</div>

<div class="content">

<!-- CURRENT STATUS -->
<div class="box">
    <h3>Current Lowest Bid</h3>
    <h1 id="lowestBid">₹ —</h1>
    <small id="lowestBidBy">Waiting for bids</small>
</div>

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
            <tr><td colspan="3">Loading…</td></tr>
        </tbody>
    </table>
</div>

<!-- WINNER -->
<div class="box" id="winnerBox" style="display:none;">
    <h3>🏆 Winning Member</h3>
    <h2 id="winnerName"></h2>
    <h3 id="winnerAmount"></h3>
</div>

</div>
</div>
</div>

<script>
const auctionId = <?= $auctionId ?>;

/* Load bids */
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

/* Winner after completion */
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
</script>
</body>
</html>
