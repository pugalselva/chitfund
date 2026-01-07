<?php
include '../../config/database.php';

$auction_id = $_GET['auction_id'];

$q = $conn->query("
SELECT b.bid_amount, m.full_name
FROM auction_bids b
JOIN members m ON m.member_id = b.member_id
WHERE b.auction_id = $auction_id
ORDER BY b.bid_amount ASC
LIMIT 1
");

if ($q->num_rows == 0) {
    echo json_encode(['amount' => null]);
    exit;
}

echo json_encode($q->fetch_assoc());
