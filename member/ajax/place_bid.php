<?php
session_start();
include '../../config/database.php';

$auction_id = $_POST['auction_id'];
$bid_amount = (int)$_POST['bid_amount'];
$member_id  = $_SESSION['member_id'];

/* Check auction active */
$q = $conn->query("
SELECT * FROM auctions
WHERE id = $auction_id
AND status = 'active'
AND NOW() <= auction_end_datetime
");

if ($q->num_rows == 0) {
    echo json_encode(['error' => 'Auction closed']);
    exit;
}

/* Get current lowest bid */
$q2 = $conn->query("
SELECT MIN(bid_amount) AS lowest
FROM auction_bids
WHERE auction_id = $auction_id
");

$row = $q2->fetch_assoc();
$lowest = $row['lowest'] ?? PHP_INT_MAX;

if ($bid_amount >= $lowest) {
    echo json_encode(['error' => 'Bid must be lower than current lowest bid']);
    exit;
}

/* Save bid */
$conn->query("
INSERT INTO auction_bids (auction_id, member_id, bid_amount)
VALUES ($auction_id, '$member_id', $bid_amount)
");

echo json_encode(['success' => true]);
?>