<?php
include '../../config/database.php';

$q = $conn->query("
SELECT * FROM auctions
WHERE status = 'active'
AND NOW() > auction_end_datetime
");

while ($a = $q->fetch_assoc()) {

    $auction_id = $a['id'];

    /* Find winner */
    $w = $conn->query("
    SELECT member_id, bid_amount
    FROM auction_bids
    WHERE auction_id = $auction_id
    ORDER BY bid_amount ASC
    LIMIT 1
    ");

    if ($w->num_rows > 0) {
        $win = $w->fetch_assoc();

        $conn->query("
        UPDATE auctions
        SET
            status = 'completed',
            winning_bid_amount = {$win['bid_amount']},
            winner_member_id = '{$win['member_id']}'
        WHERE id = $auction_id
        ");
    } else {
        $conn->query("
        UPDATE auctions
        SET status = 'completed'
        WHERE id = $auction_id
        ");
    }
}
