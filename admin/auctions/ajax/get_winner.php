<?php
include '../../../config/database.php';

$auctionId = (int) $_GET['auction_id'];

$q = $conn->prepare("
    SELECT a.winner_member_id, m.full_name AS winner_name, a.winning_bid_amount AS winning_amount
    FROM auctions a
    LEFT JOIN members m ON m.member_id = a.winner_member_id
    WHERE a.id=? AND a.status='completed'
");
$q->bind_param("i", $auctionId);
$q->execute();

$result = $q->get_result()->fetch_assoc();
echo json_encode($result ?: []);
?>