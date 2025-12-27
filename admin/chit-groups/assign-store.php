<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') exit("Unauthorized");

$groupId = (int)$_POST['group_id'];
$members = $_POST['members'] ?? [];

/* Get group limit */
$g = $conn->query("SELECT total_members FROM chit_groups WHERE id=$groupId")->fetch_assoc();
$limit = $g['total_members'];

$current = $conn->query("
    SELECT COUNT(*) c FROM chit_group_members WHERE group_id=$groupId
")->fetch_assoc()['c'];

if ($current + count($members) > $limit) {
    exit("Member limit exceeded");
}

$stmt = $conn->prepare("
    INSERT IGNORE INTO chit_group_members (group_id, member_id)
    VALUES (?, ?)
");

foreach ($members as $mid) {
    $stmt->bind_param("is", $groupId, $mid);
    $stmt->execute();
}

echo "success";
