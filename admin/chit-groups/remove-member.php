<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') die("Unauthorized");

$groupId = (int)$_GET['group_id'];
$memberId = $_GET['member_id'];

$stmt = $conn->prepare("
    DELETE FROM chit_group_members
    WHERE group_id=? AND member_id=?
");
$stmt->bind_param("is", $groupId, $memberId);
$stmt->execute();

header("Location: assign-members.php?group_id=$groupId");
exit;
?>