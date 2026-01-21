<?php
include '../../../config/database.php';

/* ===============================
   INPUT VALIDATION
================================ */
$auctionId = (int)($_POST['auction_id'] ?? 0);
$winnerId  = $_POST['member_id'] ?? '';

if (!$auctionId || !$winnerId) {
    die('Invalid request');
}

/* ===============================
   GET AUCTION DETAILS
================================ */
$stmt = $conn->prepare("
    SELECT id, chit_group_id, auction_month, starting_bid_amount, status
    FROM auctions
    WHERE id = ?
");
$stmt->bind_param("i", $auctionId);
$stmt->execute();
$auction = $stmt->get_result()->fetch_assoc();

if (!$auction) {
    die('Auction not found');
}

if ($auction['status'] === 'completed') {
    die('Auction already completed');
}

$groupId     = (int)$auction['chit_group_id'];
$monthNo     = (int)$auction['auction_month'];
$poolAmount  = (int)$auction['starting_bid_amount'];

/* ===============================
   UPDATE AUCTION (WINNER)
================================ */
$stmt = $conn->prepare("
    UPDATE auctions
    SET status='completed',
        winner_member_id=?,
        winning_bid_amount=?
    WHERE id=?
");
$stmt->bind_param("sii", $winnerId, $poolAmount, $auctionId);
$stmt->execute();

/* ===============================
   FETCH GROUP MEMBERS
================================ */
$members = $conn->query("
    SELECT member_id
    FROM chit_group_members
    WHERE group_id = $groupId
");

$totalMembers = $members->num_rows;
if ($totalMembers === 0) {
    die('No members found');
}

/* ===============================
   CALCULATE PER MEMBER SHARE
================================ */
$perMember = round($poolAmount / $totalMembers);

/* ===============================
   CREATE PAYMENTS (KULUKKAL)
================================ */
while ($m = $members->fetch_assoc()) {

    $receiptNo = 'RCT' . date('YmdHis') . rand(100, 999);

    $stmt = $conn->prepare("
        INSERT INTO payments
        (
            receipt_no,
            member_id,
            chit_group_id,
            month_no,
            actual_amount,
            discount_amount,
            final_amount,
            payment_mode,
            status
        )
        VALUES (?,?,?,?,?,?,?,?,?)
    ");

    $discount = 0;
    $mode     = 'kulukkal';
    $status   = 'pending';

    $stmt->bind_param(
        "ssiiiiiis",
        $receiptNo,
        $m['member_id'],
        $groupId,
        $monthNo,
        $poolAmount,
        $discount,
        $perMember,
        $mode,
        $status
    );

    $stmt->execute();
}

echo "success";
?>


<?php
// include '../../../config/database.php';

// $auctionId = (int)$_POST['auction_id'];
// $winnerId  = $_POST['member_id'];

// /* Get auction */
// $a = $conn->query("
//     SELECT chit_group_id, starting_bid_amount
//     FROM auctions
//     WHERE id=$auctionId
// ")->fetch_assoc();

// /* Update auction */
// $conn->query("
//     UPDATE auctions
//     SET status='completed',
//         winner_member_id='$winnerId',
//         winning_bid_amount={$a['starting_bid_amount']}
//     WHERE id=$auctionId
// ");

// /* Auto payments */
// $members = $conn->query("
//     SELECT member_id
//     FROM chit_group_members
//     WHERE group_id={$a['chit_group_id']}
// ");

// $perMember = round($a['starting_bid_amount'] / $members->num_rows);

// while ($m = $members->fetch_assoc()) {
//     $conn->query("
//         INSERT INTO payments
//         (member_id, chit_group_id, actual_amount, final_amount, status)
//         VALUES
//         ('{$m['member_id']}', {$a['chit_group_id']}, $perMember, $perMember, 'pending')
//     ");
// }

// echo "success";
?>