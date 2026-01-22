<?php
session_name('chitfund_admin');
session_start();
include '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$memberId = $_POST['member_id'] ?? '';
if (!$memberId)
    die('Invalid member ID');

/* 1. Prevent delete if payments exist */
$q = $conn->prepare("
    SELECT COUNT(*) AS cnt 
    FROM payments 
    WHERE member_id = ?
");
$q->bind_param("s", $memberId);
$q->execute();
$cnt = $q->get_result()->fetch_assoc()['cnt'];

if ($cnt > 0) {
    die('Cannot delete member with payments');
}

/* 2. Get linked user_id */
$getUser = $conn->prepare("
    SELECT user_id FROM members WHERE member_id = ?
");
$getUser->bind_param("s", $memberId);
$getUser->execute();
$userRow = $getUser->get_result()->fetch_assoc();
$userId = $userRow['user_id'] ?? null;

/* 3. Delete group mappings */
$stmt = $conn->prepare("
    DELETE FROM chit_group_members WHERE member_id = ?
");
$stmt->bind_param("s", $memberId);
$stmt->execute();

/* 4. Delete bank details */
$stmt = $conn->prepare("
    DELETE FROM member_bank_details WHERE member_id = ?
");
$stmt->bind_param("s", $memberId);
$stmt->execute();

/* 5. Delete member */
$stmt = $conn->prepare("
    DELETE FROM members WHERE member_id = ?
");
$stmt->bind_param("s", $memberId);
$stmt->execute();

/* 6. Delete login */
if ($userId) {
    $stmt = $conn->prepare("
        DELETE FROM users WHERE id = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

echo "success";
