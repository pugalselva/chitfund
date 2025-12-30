<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

if (!isset($_POST['id'])) {
    die('Invalid request');
}

$id = (int)$_POST['id'];
$groupName = trim($_POST['group_name']);
$status = $_POST['status'];
$isActive = isset($_POST['is_active']) ? 1 : 0;

$stmt = $conn->prepare("
    UPDATE chit_groups
    SET group_name=?, status=?, is_active=?
    WHERE id=?
");

$stmt->bind_param("ssii", $groupName, $status, $isActive, $id);
$stmt->execute();

/* ✅ Redirect to VIEW page */
header("Location: view.php?id=$id&updated=1");
exit;
?>