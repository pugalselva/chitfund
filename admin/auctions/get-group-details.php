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
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT auction_type
    FROM chit_groups
    WHERE id = ? AND is_active = 1
");
$stmt->bind_param("i", $groupId);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();

echo json_encode($row ?: []);
