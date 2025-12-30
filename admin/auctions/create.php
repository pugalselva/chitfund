
<?php

// include '../../config/database.php';
// $groupId = (int)$_GET['group_id'];
session_start();
require_once '../../config/database.php';

$groupId = $_GET['group_id'] ?? null;
if (!$groupId) die("Group ID missing");
$groupId = (int)$groupId;

$nextMonth = $conn->query("
    SELECT IFNULL(MAX(auction_month),0)+1 AS next_month
    FROM auctions
    WHERE chit_group_id = $groupId
")->fetch_assoc()['next_month'];

?>

<!DOCTYPE html>
<html>
<head>
<title>Create Auction</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="wrapper">
<?php include '../layout/sidebar.php'; ?>

<div class="main">

<div class="topbar">
    <div>
        <div class="page-title">Create Auction</div>
        <div class="page-subtitle">Schedule a new auction for chit group</div>
        <form method="post" action="store.php">
    <input type="hidden" name="group_id" value="<?= $groupId ?>">
    <input type="hidden" name="auction_month" value="<?= $nextMonth ?>">

</form>
    </div>
</div>

<div class="content">

<div class="form-box" style="max-width:600px;">

<!-- <h4>Auction Details</h4><br> -->

<!-- <form method="post">

<div class="form-group">
    <label>Chit Group *</label>
    <select class="form-control" name="chit_group" required>
        <option value="">Select chit group</option>
        <option value="CG001">Elite Savings Group</option>
        <option value="CG002">Business Circle</option>
        <option value="CG003">Community Fund</option>
    </select>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Month *</label>
        <input type="number" class="form-control" name="month" placeholder="e.g. 8" required>
    </div>

    <div class="form-group">
        <label>Pool Amount (₹) *</label>
        <input type="number" class="form-control" name="pool_amount" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label>Scheduled Date *</label>
        <input type="date" class="form-control" name="date" required>
    </div>

    <div class="form-group">
        <label>Scheduled Time *</label>
        <input type="time" class="form-control" name="time" required>
    </div>
</div>

<div class="form-group">
    <label>Status *</label>
    <select class="form-control" name="status">
        <option value="upcoming">Upcoming</option>
        <option value="live">Live</option>
    </select>
</div>

<br>

<button class="btn-primary">Create Auction</button>
<a href="index.php">
    <button type="button" class="btn-secondary">Cancel</button>
</a>

</form> -->
<h4>Create Auction – Month <?= $nextMonth ?></h4>

<form method="post" action="store.php">

<input type="hidden" name="group_id" value="<?= $groupId ?>">
<input type="hidden" name="auction_month" value="<?= $nextMonth ?>">

<label>Auction Date & Time</label>
<input type="datetime-local" name="auction_datetime" required>

<label>Starting Bid Amount</label>
<input type="number" name="starting_bid" required>

<button>Create Auction</button>

</form>


</div>

</div>
</div>
</div>

</body>
</html>
