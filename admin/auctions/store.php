<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$groupId = (int)$_POST['chit_group_id'];
$datetime = $_POST['auction_datetime'];
$startingBid = (int)$_POST['starting_bid_amount'];
$status = $_POST['status'];

/* Calculate next month */
$stmt = $conn->prepare("
    SELECT IFNULL(MAX(auction_month),0)+1 AS next_month
    FROM auctions
    WHERE chit_group_id=?
");
$stmt->bind_param("i", $groupId);
$stmt->execute();
$month = $stmt->get_result()->fetch_assoc()['next_month'];

/* Insert */
$stmt = $conn->prepare("
    INSERT INTO auctions
    (chit_group_id, auction_month, auction_datetime, starting_bid_amount, status)
    VALUES (?,?,?,?,?)
");
$stmt->bind_param("iisis", $groupId, $month, $datetime, $startingBid, $status);
$stmt->execute();

$check = $conn->prepare("
    SELECT status FROM chit_groups WHERE id=?
");
$check->bind_param("i", $groupId);
$check->execute();
$group = $check->get_result()->fetch_assoc();

if ($group['status'] === 'completed') {
    die('Auction cannot be created. Group is completed.');
}


header("Location: index.php?success=1");
exit;

