<?php
include 'auth.php';
include '../config/database.php';

$name = $_SESSION['name'] ?? 'Member';
$email = $_SESSION['email'] ?? '';

$auctionId = (int) ($_GET['auction_id'] ?? 0);

if ($auctionId > 0) {
    /* Fetch specific auction */
    $q = $conn->prepare("
        SELECT a.*, cg.group_name, m.full_name as winner_name
        FROM auctions a
        JOIN chit_groups cg ON cg.id = a.chit_group_id
        LEFT JOIN members m ON m.member_id = a.winner_member_id
        WHERE a.id = ?
    ");
    $q->bind_param("i", $auctionId);
    $q->execute();
    $result = $q->get_result();
} else {
    /* Get active auction */
    $result = $conn->query("
        SELECT a.*, cg.group_name
        FROM auctions a
        JOIN chit_groups cg ON cg.id = a.chit_group_id
        WHERE a.status = 'active'
        LIMIT 1
    ");
}

if ($result->num_rows == 0) {
    echo "<div style='padding:20px'>Auction not found</div>";
    exit();
}

$auction = $result->fetch_assoc();
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
                    <?php if ($auction['status'] === 'active'): ?>
                        <!-- ACTIVE AUCTION UI -->
                        <small>Pool Amount: ₹<?= number_format($poolAmount) ?></small>

                        <div class="bid-highlight">
                            <small>Current Lowest Bid</small>
                            <h2 id="lowestBid">₹ —</h2>
                            <small id="lowestBidBy">No bids yet</small>
                        </div>

                        <label>Your Bid Amount (₹)</label><br>
                        <input type="number" class="bid-input" id="bidAmount"
                            placeholder="Enter amount less than <?= $poolAmount ?>">

                        <div class="bid-note">
                            Lower bid amount = Higher discount for all members
                        </div>
                        <button class="place-bid-btn" onclick="placeBid()">
                            Place Bid
                        </button>

                    <?php else: ?>
                        <!-- COMPLETED AUCTION UI -->
                        <div class="bid-highlight" style="background:#dcfce7; border-color:#86efac;">
                            <small style="color:#166534">Winner</small>
                            <h2 style="color:#15803d"><?= htmlspecialchars($auction['winner_name'] ?? '—') ?></h2>
                            <small style="color:#166534">Winning Bid:
                                ₹<?= number_format($auction['winning_bid_amount']) ?></small>
                        </div>

                        <div style="margin-top:20px; padding:15px; background:#f8fafc; border-radius:10px;">
                            <div>Pool Amount: <b>₹<?= number_format($poolAmount) ?></b></div>
                            <div>Total Discount: <b>₹<?= number_format($poolAmount - $auction['winning_bid_amount']) ?></b>
                            </div>
                        </div>
                    <?php endif; ?>
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