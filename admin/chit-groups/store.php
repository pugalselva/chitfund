<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo 'Unauthorized';
    exit;
}

/* Fetch default commission */
$commission = 0;
$s = $conn->query("
    SELECT setting_value 
    FROM settings 
    WHERE setting_key='default_foreman_commission' 
    AND is_active=1
")->fetch_assoc();

if ($s) {
    $commission = (int)$s['setting_value'];
}

/* Collect form data */
$groupName    = $_POST['group_name'];
$totalMembers = (int)$_POST['total_members'];
$duration     = (int)$_POST['duration'];
$auctionType  = $_POST['auction_type'];
$startDate    = $_POST['start_date'];
$status       = $_POST['status'];
$isActive     = isset($_POST['is_active']) ? 1 : 0;

/* Generate group code */
$groupCode = 'CG' . time();

/* total_value calculated later */
$totalValue = 0;

/* Insert */
$stmt = $conn->prepare("
    INSERT INTO chit_groups
    (group_code, group_name, total_value,
     duration_months, total_members, auction_type,
     commission, start_date, status, is_active)
    VALUES (?,?,?,?,?,?,?,?,?,?)
");

if (!$stmt) {
    echo 'Prepare failed';
    exit;
}

/* ✅ CORRECT TYPES */
$stmt->bind_param(
    "ssiiisissi",
    $groupCode,
    $groupName,
    $totalValue,
    $duration,
    $totalMembers,
    $auctionType,
    $commission,
    $startDate,
    $status,
    $isActive
);

if ($stmt->execute()) {
    echo 'success';
} else {
    echo 'Database error';
}
?>