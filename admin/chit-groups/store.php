<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

/* 1️⃣ Fetch default foreman commission from settings */
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

/* 2️⃣ Collect form data (NO commission from POST) */
$groupName   = $_POST['group_name'];
$totalMembers = (int)$_POST['total_members'];
$duration    = (int)$_POST['duration'];
$monthly     = (int)$_POST['monthly_installment'];
$auctionType = $_POST['auction_type'];
$startDate   = $_POST['start_date'];
$status      = $_POST['status'];
$isActive    = isset($_POST['is_active']) ? 1 : 0;

/* 3️⃣ Calculations */
$totalValue = $monthly * $duration;
$groupCode  = 'CG' . time();

/* 4️⃣ Insert group */
$stmt = $conn->prepare("
    INSERT INTO chit_groups
    (group_code, group_name, total_value, monthly_contribution,
     duration_months, total_members, auction_type,
     commission, start_date, status, is_active)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
    "ssiiiiisssi",
    $groupCode,
    $groupName,
    $totalValue,
    $monthly,
    $duration,
    $totalMembers,
    $auctionType,
    $commission,   // ✅ from settings
    $startDate,
    $status,
    $isActive
);

$stmt->execute();

header("Location: index.php?success=1");
exit;

?>
