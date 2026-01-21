<?php
include '../../../config/database.php';

$auctionId = (int)$_GET['auction_id'];

/* Get auction */
$a = $conn->query("
    SELECT chit_group_id, starting_bid_amount
    FROM auctions
    WHERE id=$auctionId
")->fetch_assoc();

if (!$a) {
    echo json_encode([]);
    exit;
}

/* Pick 4 random members */
$q = $conn->query("
    SELECT m.member_id, m.full_name
    FROM chit_group_members gm
    JOIN members m ON m.member_id = gm.member_id
    WHERE gm.group_id = {$a['chit_group_id']}
    ORDER BY RAND()
    LIMIT 4
");

$members = [];
while ($row = $q->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode($members);
?>