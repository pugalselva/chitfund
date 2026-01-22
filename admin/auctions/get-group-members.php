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

$result = $conn->query("
    SELECT 
        m.member_id,
        m.full_name
    FROM chit_group_members gm
    JOIN members m 
        ON m.member_id = gm.member_id
    WHERE gm.group_id = $groupId
      AND m.is_active = 1
    ORDER BY m.full_name
");

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode($members);
