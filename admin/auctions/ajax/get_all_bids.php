<?php
include '../../../config/database.php';

$auctionId = (int)($_GET['auction_id'] ?? 0);

$q = $conn->prepare("
    SELECT b.bid_amount, b.created_at, m.full_name
    FROM auction_bids b
    JOIN members m ON m.member_id = b.member_id
    WHERE b.auction_id=?
    ORDER BY b.bid_amount ASC
");
$q->bind_param("i", $auctionId);
$q->execute();

echo json_encode($q->get_result()->fetch_all(MYSQLI_ASSOC));
?>