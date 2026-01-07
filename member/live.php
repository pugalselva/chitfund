<?php
session_start();
include '../../config/database.php';

$memberId = $_SESSION['user_id'];

/* Get active auction for this member */
$auction = $conn->query("
    SELECT a.*, g.group_name, g.total_value
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    WHERE a.status='active'
    LIMIT 1
")->fetch_assoc();

if (!$auction) {
    die('No live auction');
}

/* Pool amount */
$poolAmount = $auction['total_value'];

/* Lowest bid */
$lowestBid = $conn->query("
    SELECT b.bid_amount, b.discount_percent, m.full_name
    FROM bids b
    JOIN members m ON m.member_id = b.member_id
    WHERE b.auction_id = {$auction['id']}
    ORDER BY b.bid_amount ASC
    LIMIT 1
")->fetch_assoc();

/* Recent 3 lowest bids */
$bids = $conn->query("
    SELECT m.full_name, b.bid_amount, b.discount_percent
    FROM bids b
    JOIN members m ON m.member_id = b.member_id
    WHERE b.auction_id = {$auction['id']}
    ORDER BY b.bid_amount ASC
    LIMIT 3
");
?>
