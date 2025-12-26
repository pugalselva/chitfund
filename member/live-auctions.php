<!DOCTYPE html>
<html>
<head>
<title>Live Auction</title>
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
        <b>Member User</b><br>
        sandy@gmail.com
    </div>
</div>

<div class="content">

<div class="auction-box">

    <h4>🔨 Elite Savings Group - Month 13</h4>
    <small>Pool Amount: ₹2,50,000</small>

    <!-- CURRENT LOWEST BID -->
    <div class="bid-highlight">
        <small>Current Lowest Bid</small>
        <h2>₹ —</h2>
        <small>by • % discount</small>
    </div>

    <!-- BID INPUT -->
    <label>Your Bid Amount (₹)</label><br>
    <input
        type="number"
        class="bid-input"
        placeholder="Enter amount less than 2,50,000"
    >

    <div class="bid-note">
        Lower bid amount = Higher discount for all members
    </div>

    <button class="place-bid-btn">
        Place Bid
    </button>

</div>

<br>

<!-- ALL BIDS -->
<div class="auction-box">
    <b>All Bids (0)</b>
    <br><br>
    <small>No bids placed yet</small>
</div>

</div>
</div>
</div>

</body>
</html>
