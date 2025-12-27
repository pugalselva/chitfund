<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    echo "Unauthorized";
    exit;
}

$groupId = (int)$_POST['group_id'];
$members = $_POST['members'] ?? [];

if (empty($members)) {
    echo "No members selected";
    exit;
}

$stmt = $conn->prepare("
    INSERT IGNORE INTO chit_group_members (group_id, member_id)
    VALUES (?, ?)
");

foreach ($members as $memberId) {
    $stmt->bind_param("is", $groupId, $memberId);
    $stmt->execute();
}

echo "success";
?>