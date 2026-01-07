<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') die('Unauthorized');

$memberId = $_POST['member_id'] ?? '';
$status   = $_POST['status'] ?? '';

if ($memberId === '') die('Invalid ID');

$stmt = $conn->prepare("
    UPDATE members SET is_active = ? WHERE member_id = ?
");
$stmt->bind_param("is", $status, $memberId);
$stmt->execute();

echo "success";
?>