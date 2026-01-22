<?php
session_name('chitfund_admin');
session_start();
include '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('Unauthorized');
}

if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
    http_response_code(400);
    die('Invalid group ID');
}

$groupId = (int) $_POST['id'];

/* Check group exists */
$check = $conn->prepare("SELECT id FROM chit_groups WHERE id=?");
$check->bind_param("i", $groupId);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    die('Group not found');
}

/* Block delete if auctions exist */
$q = $conn->prepare("
    SELECT COUNT(*) AS cnt 
    FROM auctions 
    WHERE chit_group_id=?
    AND status IN ('active','upcoming')
");
$q->bind_param("i", $groupId);
$q->execute();

$cnt = $q->get_result()->fetch_assoc()['cnt'];

if ($cnt > 0) {
    die('Cannot delete group with active or upcoming auctions');
}


/* Soft delete */
$stmt = $conn->prepare("
    UPDATE chit_groups
    SET is_active=0, status='completed'
    WHERE id=?
");
$stmt->bind_param("i", $groupId);
$stmt->execute();

echo 'success';
?>