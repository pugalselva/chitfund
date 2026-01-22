<?php
session_name('chitfund_admin');
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$groupId = (int) ($_GET['group_id'] ?? 0);
if (!$groupId) {
    echo json_encode(['amount' => 0]);
    exit;
}

/* Last completed auction + group members */
$stmt = $conn->prepare("
    SELECT 
        a.winning_bid_amount,
        g.total_members
    FROM auctions a
    JOIN chit_groups g ON g.id = a.chit_group_id
    WHERE a.chit_group_id = ?
      AND a.status = 'completed'
      AND a.winning_bid_amount IS NOT NULL
    ORDER BY a.auction_month DESC
    LIMIT 1
");
$stmt->bind_param("i", $groupId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(['amount' => 0]);
    exit;
}

$perMember = round($row['winning_bid_amount'] / $row['total_members']);

echo json_encode([
    'per_member' => $perMember,
    'total' => $row['winning_bid_amount'],
    'members' => $row['total_members']
]);
?>