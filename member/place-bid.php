<?php
session_start();
include '../config/database.php';

$memberId = $_SESSION['member_id'];
$auctionId = (int)$_POST['auction_id'];
$amount = (int)$_POST['bid_amount'];

/* LOWEST CHECK */
$min = $conn->query("
    SELECT MIN(bid_amount) AS m
    FROM bids
    WHERE auction_id=$auctionId
")->fetch_assoc()['m'];

if ($min && $amount >= $min) {
    die("Bid must be lower");
}

/* INSERT */
$stmt = $conn->prepare("
    INSERT INTO bids (auction_id, member_id, bid_amount)
    VALUES (?,?,?)
");
$stmt->bind_param("isi", $auctionId, $memberId, $amount);
$stmt->execute();

echo "success";
?>