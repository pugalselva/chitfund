<?php
include '../../../config/database.php';

$groupId = (int)$_GET['group_id'];

/* Get auction type */
$group = $conn->query("
    SELECT auction_type
    FROM chit_groups
    WHERE id=$groupId
")->fetch_assoc();

/* Members */
$q = $conn->query("
    SELECT m.full_name
    FROM chit_group_members gm
    JOIN members m ON m.member_id = gm.member_id
    WHERE gm.group_id=$groupId
");

$members = [];
while ($m = $q->fetch_assoc()) {
    $members[] = $m;
}

echo json_encode([
    'auction_type' => $group['auction_type'],
    'members' => $members
]);
?>