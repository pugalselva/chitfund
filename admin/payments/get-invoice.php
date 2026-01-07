<?php
include '../../config/database.php';

if (!isset($_GET['receipt'])) {
    http_response_code(400);
    exit;
}
$receipt = $_GET['receipt'];

$stmt = $conn->prepare("
    SELECT p.*, m.full_name, g.group_name
    FROM payments p
    JOIN members m ON m.member_id = p.member_id
    JOIN chit_groups g ON g.id = p.chit_group_id
    WHERE p.receipt_no = ?
");
$stmt->bind_param("s", $receipt);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    http_response_code(404);
    exit;
}

echo json_encode($data);
?>