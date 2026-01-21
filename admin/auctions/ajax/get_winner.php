<?php
include '../../../config/database.php';

$auctionId = (int)$_GET['auction_id'];

$q = $conn->prepare("
    SELECT m.full_name, a.winning_bid_amount
    FROM auctions a
    JOIN members m ON m.member_id = a.winner_member_id
    WHERE a.id=? AND a.status='completed'
");
$q->bind_param("i", $auctionId);
$q->execute();

echo json_encode($q->get_result()->fetch_assoc());
?>
