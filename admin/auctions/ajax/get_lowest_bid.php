<?php
include '../../../config/database.php';

$auctionId = (int)$_GET['auction_id'];

$q = $conn->prepare("
    SELECT b.bid_amount, m.full_name
    FROM auction_bids b
    JOIN members m ON m.member_id=b.member_id
    WHERE b.auction_id=?
    ORDER BY b.bid_amount ASC
    LIMIT 1
");
$q->bind_param("i", $auctionId);
$q->execute();

echo json_encode($q->get_result()->fetch_assoc());
?>