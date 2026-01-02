<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') die('Unauthorized');

$groupId = (int)$_POST['group_id'];
$members = $_POST['members'] ?? [];

if (!$groupId || empty($members)) {
    die('No members selected');
}

$stmt = $conn->prepare("
    INSERT IGNORE INTO chit_group_members (group_id, member_id)
    VALUES (?, ?)
");

foreach ($members as $memberId) {
    $stmt->bind_param("is", $groupId, $memberId);
    $stmt->execute();
}

echo 'success';
