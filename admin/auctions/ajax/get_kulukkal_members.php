<?php
include '../../../config/database.php';

$auctionId = (int) $_GET['auction_id'];

/* Get auction and group */
$a = $conn->query("
    SELECT chit_group_id, starting_bid_amount
    FROM auctions
    WHERE id=$auctionId
")->fetch_assoc();

if (!$a) {
    echo json_encode([]);
    exit;
}

$groupId = $a['chit_group_id'];

/* Pick 4 random ELIGIBLE members (those who haven't won yet in this group) */
$q = $conn->query("
    SELECT m.member_id, m.full_name
    FROM chit_group_members gm
    JOIN members m ON m.member_id = gm.member_id
    WHERE gm.group_id = $groupId
      AND m.member_id NOT IN (
          SELECT winner_member_id 
          FROM auctions 
          WHERE chit_group_id = $groupId 
            AND status = 'completed' 
            AND winner_member_id IS NOT NULL
            AND id != $auctionId
      )
    ORDER BY RAND()
    LIMIT 4
");

$members = [];
while ($row = $q->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode($members);
?>