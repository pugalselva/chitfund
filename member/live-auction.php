<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../index.php');
    exit();
}

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

/* Get ACTIVE auction */
$q = $conn->query("
SELECT a.*, cg.group_name
FROM auctions a
JOIN chit_groups cg ON cg.id = a.chit_group_id
WHERE a.status = 'active'
LIMIT 1
");

if ($q->num_rows == 0) {
    echo "<h3 style='padding:20px'>No live auction right now</h3>";
    exit();
}

$auction = $q->fetch_assoc();
$auctionId = $auction['id'];
$poolAmount = $auction['starting_bid_amount'];

?>


<!DOCTYPE html>
<html>

<head>
    <title>Live Auction</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include 'layout/sidebar.php'; ?>
        <div class="main">
            <div class="topbar">
                <div>
                    <div class="page-title">Live Auction</div>
                    <div class="page-subtitle">Place your bid now</div>
                </div>

                <div style="text-align:right;">
                    <b><?= htmlspecialchars($name) ?></b><br>
                    <small><?= htmlspecialchars($email) ?></small><br>

                    <a href="../logout.php" class="btn btn-danger" style="margin-top:6px;">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>

            <div class="content">
                <div class="auction-box">
                    <h4>🔨 <?= $auction['group_name'] ?> - Month <?= $auction['auction_month'] ?></h4>
                    <small>Pool Amount: ₹<?= number_format($poolAmount) ?></small>
                    <!-- CURRENT LOWEST BID -->
                    <div class="bid-highlight">
                        <small>Current Lowest Bid</small>
                        <h2 id="lowestBid">₹ —</h2>
                        <small id="lowestBidBy">No bids yet</small>
                    </div>
                    <!-- BID INPUT -->
                    <label>Your Bid Amount (₹)</label><br>
                    <input type="number" class="bid-input" id="bidAmount"
                        placeholder="Enter amount less than <?= $poolAmount ?>">

                    <div class="bid-note">
                        Lower bid amount = Higher discount for all members
                    </div>
                    <button class="place-bid-btn" onclick="placeBid()">
                        Place Bid
                    </button>
                </div>
                <br>
                <!-- ALL BIDS -->
                <div class="auction-box">
                    <b>All Bids</b>
                    <div id="bidList" style="margin-top:10px">
                        <small>No bids placed yet</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    const auctionId = <?= $auctionId ?>;

    /* Load lowest bid */
    function loadLowestBid() {
        fetch(`ajax/get_lowest_bid.php?auction_id=${auctionId}`)
            .then(res => res.json())
            .then(data => {
                if (data.bid_amount) {
                    document.getElementById('lowestBid').innerText = '₹' + data.bid_amount;
                    document.getElementById('lowestBidBy').innerText =
                        'by ' + data.full_name;
                }
            });
    }

    /* Load all bids */
    function loadBidList() {
        fetch(`ajax/get_all_bids.php?auction_id=${auctionId}`)
            .then(res => res.json())
            .then(bids => {
                let html = '';
                if (bids.length === 0) {
                    html = '<small>No bids placed yet</small>';
                } else {
                    bids.forEach(b => {
                        html += `<div style="padding:6px 0">
                                ${b.full_name}
                                <span style="float:right">₹${b.bid_amount}</span>
                             </div>`;
                    });
                }
                document.getElementById('bidList').innerHTML = html;
            });
    }

    /* Place bid */
    function placeBid() {
        const amount = document.getElementById('bidAmount').value;

        if (!amount) {
            alert('Enter bid amount');
            return;
        }

        fetch('ajax/place_bid.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `auction_id=${auctionId}&bid_amount=${amount}`
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.error) {
                    alert(resp.error);
                } else {
                    document.getElementById('bidAmount').value = '';
                    loadLowestBid();
                    loadBidList();
                }
            });
    }

    /* AUTO REFRESH EVERY 5 SECONDS */
    setInterval(() => {
        loadLowestBid();
        loadBidList();
        fetch('ajax/close_auction.php');
    }, 5000);

    /* Initial load */
    loadLowestBid();
    loadBidList();
</script>

</html>