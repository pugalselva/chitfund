<?php
include '../../config/database.php';

$auction_id = $_GET['auction_id'];

$q = $conn->query("
SELECT m.full_name, b.bid_amount
FROM auction_bids b
JOIN members m ON m.member_id = b.member_id
WHERE b.auction_id = $auction_id
ORDER BY b.bid_amount ASC
");

$bids = [];
while ($row = $q->fetch_assoc()) {
    $bids[] = $row;
}

echo json_encode($bids);
?>