<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$groupId    = (int)$_POST['chit_group_id'];
$startTime = $_POST['auction_datetime'];
$endTime   = $_POST['auction_end_datetime'];
$startBid  = (int)$_POST['starting_bid_amount'];
$status    = $_POST['status'];

/* ❌ Invalid time protection */
if (strtotime($endTime) <= strtotime($startTime)) {
    die('Auction end time must be after start time');
}

/* 🔒 Check group status */
$check = $conn->prepare("
    SELECT status, duration_months
    FROM chit_groups
    WHERE id=?
");
$check->bind_param("i", $groupId);
$check->execute();
$group = $check->get_result()->fetch_assoc();

if ($group['status'] === 'completed') {
    die('Auction cannot be created. Group is completed.');
}

/* 🔢 Calculate next auction month */
$stmt = $conn->prepare("
    SELECT IFNULL(MAX(auction_month),0)+1 AS next_month
    FROM auctions
    WHERE chit_group_id=?
");
$stmt->bind_param("i", $groupId);
$stmt->execute();
$month = (int)$stmt->get_result()->fetch_assoc()['next_month'];

/* ❌ Prevent exceeding duration */
if ($month > $group['duration_months']) {
    die('Auction months exceed group duration');
}

/* ✅ INSERT AUCTION */
$stmt = $conn->prepare("
    INSERT INTO auctions
    (chit_group_id, auction_month,
     auction_datetime, auction_end_datetime,
     starting_bid_amount, status)
    VALUES (?,?,?,?,?,?)
");

$stmt->bind_param(
    "iissis",
    $groupId,
    $month,
    $startTime,
    $endTime,
    $startBid,
    $status
);

$stmt->execute();

header("Location: index.php?success=1");
exit;
