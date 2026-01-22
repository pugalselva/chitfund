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
    echo '';
    exit;
}

/* Fetch last completed auction */
$stmt = $conn->prepare("
    SELECT winning_bid_amount
    FROM auctions
    WHERE chit_group_id = ?
      AND status = 'completed'
      AND winning_bid_amount IS NOT NULL
    ORDER BY auction_month DESC
    LIMIT 1
");
$stmt->bind_param("i", $groupId);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();

echo $row ? $row['winning_bid_amount'] : '';
?>