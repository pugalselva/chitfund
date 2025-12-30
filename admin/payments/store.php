<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

/* ---------- INPUT ---------- */
$memberId   = $_POST['member_id'];
$groupId    = (int)$_POST['chit_group_id'];
$actual     = (int)$_POST['actual_amount'];
$discount   = (int)($_POST['discount_amount'] ?? 0);
$final      = $actual - $discount;
$mode       = $_POST['payment_mode'];
$payDate    = $_POST['payment_date'];

/* ---------- RECEIPT ---------- */
$receiptNo = 'REC-' . date('Y') . '-' . rand(1000,9999);

/* ---------- MONTH (AUTO) ---------- */
$month = $conn->query("
    SELECT COUNT(*) + 1 AS m 
    FROM payments 
    WHERE member_id='$memberId' AND chit_group_id=$groupId
")->fetch_assoc()['m'];

/* ---------- DUE DATE ---------- */
$dueDate = date('Y-m-d', strtotime($payDate . ' +5 days'));

/* ---------- STATUS ---------- */
$status = 'paid';

/* ---------- INSERT ---------- */
$stmt = $conn->prepare("
    INSERT INTO payments
    (receipt_no, member_id, chit_group_id, month_no,
     actual_amount, discount_amount, final_amount,
     payment_mode, payment_date, due_date, status)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
    "ssiiiiissss",
    $receiptNo,
    $memberId,
    $groupId,
    $month,
    $actual,
    $discount,
    $final,
    $mode,
    $payDate,
    $dueDate,
    $status
);

if($stmt->execute()){
    echo "success";
}else{
    echo "db_error";
}
?>