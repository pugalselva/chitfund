<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Unauthorized";
    exit;
}

/* -------- FETCH DATA -------- */
$groupName      = $_POST['group_name'];
$totalMembers   = (int)$_POST['total_members'];
$duration       = (int)$_POST['duration'];
$monthly        = (int)$_POST['monthly_installment'];
$auctionType    = $_POST['auction_type'];
$commission     = (int)$_POST['commission'];
$startDate      = $_POST['start_date'];
$status         = $_POST['status'];
$isActive       = isset($_POST['is_active']) ? 1 : 0;

/* -------- CALCULATIONS -------- */
$totalValue = $monthly * $duration;
$groupCode  = 'CG' . time();

/* -------- INSERT -------- */
$stmt = $conn->prepare("
    INSERT INTO chit_groups
    (group_code, group_name, total_value, monthly_contribution,
     duration_months, total_members, auction_type, commission,
     start_date, status, is_active)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)
");

if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}

$stmt->bind_param(
    "ssiiiiisssi",
    $groupCode,
    $groupName,
    $totalValue,
    $monthly,
    $duration,
    $totalMembers,
    $auctionType,
    $commission,
    $startDate,
    $status,
    $isActive
);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "DB Error: " . $stmt->error;
}
?>