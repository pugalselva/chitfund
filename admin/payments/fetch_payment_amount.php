    <?php
include '../../config/database.php';

$groupId  = (int)$_GET['group_id'];
$memberId = $_GET['member_id'];

/* last completed auction */
$a = $conn->query("
    SELECT winning_bid_amount
    FROM auctions
    WHERE chit_group_id=$groupId AND status='completed'
    ORDER BY auction_month DESC
    LIMIT 1
")->fetch_assoc();

if (!$a) {
    echo json_encode(['error' => 'No completed auction']);
    exit;
}

/* total members */
$members = $conn->query("
    SELECT COUNT(*) AS c FROM chit_group_members WHERE group_id=$groupId
")->fetch_assoc()['c'];

$perHead = round($a['winning_bid_amount'] / $members);

/* month */
$month = $conn->query("
    SELECT COUNT(*)+1 AS m
    FROM payments
    WHERE member_id='$memberId' AND chit_group_id=$groupId
")->fetch_assoc()['m'];

echo json_encode([
    'amount' => $perHead,
    'month'  => $month,
    'total'  => $a['winning_bid_amount']
]);
