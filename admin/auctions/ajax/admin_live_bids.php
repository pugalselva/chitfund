<?php
include '../../../config/database.php';

$auctionId = (int)($_GET['auction_id'] ?? 0);
if (!$auctionId) exit;

$bids = $conn->query("
    SELECT b.bid_amount, m.full_name
    FROM auction_bids b
    JOIN members m ON m.member_id = b.member_id
    WHERE b.auction_id = $auctionId
    ORDER BY b.bid_amount ASC
");

$data = [];
while ($b = $bids->fetch_assoc()) {
    $data[] = $b;
}

echo json_encode([
    'bids' => $data,
    'lowest' => $data[0] ?? null
]);
?>