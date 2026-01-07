<?php
include '../../config/database.php';

$auctionId = (int)$_POST['auction_id'];

/* LOWEST BID */
$bid = $conn->query("
    SELECT * FROM bids
    WHERE auction_id=$auctionId
    ORDER BY bid_amount ASC
    LIMIT 1
")->fetch_assoc();

if (!$bid) die("No bids");

/* UPDATE AUCTION */
$conn->query("
    UPDATE auctions
    SET status='completed',
        winning_bid_amount={$bid['bid_amount']},
        winner_member_id='{$bid['member_id']}'
    WHERE id=$auctionId
");

/* AUTO PAYMENTS */
$auction = $conn->query("
    SELECT chit_group_id FROM auctions WHERE id=$auctionId
")->fetch_assoc();

$members = $conn->query("
    SELECT member_id FROM chit_group_members
    WHERE group_id={$auction['chit_group_id']}
");

$perMember = $bid['bid_amount'] / $members->num_rows;

while($m = $members->fetch_assoc()){
    $conn->query("
        INSERT INTO payments
        (member_id, chit_group_id, actual_amount, final_amount, status)
        VALUES
        ('{$m['member_id']}', {$auction['chit_group_id']}, $perMember, $perMember, 'pending')
    ");
}

echo "success";
?>