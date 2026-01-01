<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('Unauthorized');
}

if (empty($_POST['member_id'])) {
    http_response_code(400);
    die('Invalid member ID');
}

$memberId = $_POST['member_id'];

/* Check member exists */
$check = $conn->prepare("
    SELECT member_id FROM members WHERE member_id=?
");
$check->bind_param("s", $memberId);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    die('Member not found');
}

/* Prevent delete if member has payments */
$q = $conn->prepare("
    SELECT COUNT(*) AS cnt 
    FROM payments 
    WHERE member_id=?
");
$q->bind_param("s", $memberId);
$q->execute();

$cnt = $q->get_result()->fetch_assoc()['cnt'];

if ($cnt > 0) {
    die('Cannot delete member with payments');
}

/* Soft delete member */
$stmt = $conn->prepare("
    UPDATE members 
    SET is_active=0 
    WHERE member_id=?
");
$stmt->bind_param("s", $memberId);
$stmt->execute();

echo 'success';
?>