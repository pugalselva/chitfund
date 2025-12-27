<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') die("Unauthorized");

$groupId = (int)$_POST['group_id'];
$month   = (int)$_POST['auction_month'];
$date    = $_POST['auction_datetime'];
$start   = (int)$_POST['starting_bid'];

/* Determine status */
$status = (strtotime($date) > time()) ? 'upcoming' : 'active';

$stmt = $conn->prepare("
    INSERT INTO auctions
    (chit_group_id, auction_month, auction_datetime, starting_bid_amount, status)
    VALUES (?,?,?,?,?)
");

$stmt->bind_param("iisss", $groupId, $month, $date, $start, $status);
$stmt->execute();

header("Location: index.php?group_id=$groupId");
exit;
?>