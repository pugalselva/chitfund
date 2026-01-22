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
        /* ========================================
           AUTO RANDOM SELECTION (Reverse Auction - No Bids)
           ======================================== */
        
        // Get auction details
        $auctionDetails = $conn->query("
            SELECT chit_group_id, starting_bid_amount, auction_month
            FROM auctions WHERE id = $auction_id
        ")->fetch_assoc();
        
        $groupId = (int)$auctionDetails['chit_group_id'];
        $poolAmount = (int)$auctionDetails['starting_bid_amount'];
        $monthNo = (int)$auctionDetails['auction_month'];
        
        // Get eligible members (who haven't won in previous months)
        $eligible = $conn->query("
            SELECT gm.member_id
            FROM chit_group_members gm
            WHERE gm.group_id = $groupId
            AND gm.member_id NOT IN (
                SELECT winner_member_id FROM auctions
                WHERE chit_group_id = $groupId
                AND winner_member_id IS NOT NULL
            )
            ORDER BY RAND()
            LIMIT 1
        ");
        
        if ($eligible->num_rows > 0) {
            $winner = $eligible->fetch_assoc();
            $winnerId = $winner['member_id'];
            
            // Update auction with random winner
            $conn->query("
                UPDATE auctions
                SET status = 'completed',
                    winning_bid_amount = $poolAmount,
                    winner_member_id = '$winnerId'
                WHERE id = $auction_id
            ");
            
            // Generate payments for all group members
            $members = $conn->query("
                SELECT member_id FROM chit_group_members
                WHERE group_id = $groupId
            ");
            
            $totalMembers = $members->num_rows;
            $perMember = round($poolAmount / $totalMembers);
            
            while ($m = $members->fetch_assoc()) {
                $receiptNo = 'RCT' . date('YmdHis') . rand(100, 999);
                $conn->query("
                    INSERT INTO payments
                    (receipt_no, member_id, chit_group_id, month_no, 
                     actual_amount, discount_amount, final_amount, 
                     payment_mode, status)
                    VALUES
                    ('$receiptNo', '{$m['member_id']}', $groupId, $monthNo,
                     $poolAmount, 0, $perMember, 'auto_random', 'pending')
                ");
            }
        } else {
            // Fallback: No eligible members (all have won previously)
            $conn->query("
                UPDATE auctions
                SET status = 'completed'
                WHERE id = $auction_id
            ");
        }
    }
}
?>
