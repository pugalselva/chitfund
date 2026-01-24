<?php
// session_start();
// include '../../config/database.php';

// if ($_SESSION['role'] !== 'admin') {
//     die('Unauthorized');
// }

// /* 1️⃣ Fetch grace period */
// $graceDays = 0;
// $s = $conn->query("
//     SELECT setting_value
//     FROM settings
//     WHERE setting_key='payment_grace_period'
//     AND is_active=1
// ")->fetch_assoc();

// if ($s) {
//     $graceDays = (int)$s['setting_value'];
// }

// /* 2️⃣ Collect payment data */
// $memberId   = $_POST['member_id'];
// $groupId    = (int)$_POST['chit_group_id'];
// $monthNo    = (int)$_POST['month_no'];
// $actual     = (int)$_POST['actual_amount'];
// $discount   = (int)$_POST['discount_amount'];
// $final      = $actual - $discount;
// $mode       = $_POST['payment_mode'];
// $paymentDate = $_POST['payment_date'];

// /* 3️⃣ Auto due date */
// $dueDate = date('Y-m-d', strtotime("$paymentDate + $graceDays days"));

// /* 4️⃣ Status logic */
// $status = 'paid';
// if (strtotime(date('Y-m-d')) > strtotime($dueDate)) {
//     $status = 'overdue';
// }

// /* 5️⃣ Receipt number */
// $receipt = 'REC-' . date('Ymd') . '-' . rand(100,999);

// /* 6️⃣ Insert payment */
// $stmt = $conn->prepare("
//     INSERT INTO payments
//     (receipt_no, member_id, chit_group_id, month_no,
//      actual_amount, discount_amount, final_amount,
//      payment_mode, payment_date, due_date, status)
//     VALUES (?,?,?,?,?,?,?,?,?,?,?)
// ");

// $stmt->bind_param(
//     "ssi iiiissss",
//     $receipt,
//     $memberId,
//     $groupId,
//     $monthNo,
//     $actual,
//     $discount,
//     $final,
//     $mode,
//     $paymentDate,
//     $dueDate,
//     $status
// );

// $stmt->execute();

// header("Location: index.php?success=1");
// exit;

session_name('chitfund_admin');
session_start();
include '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(401);
    die("Unauthorized");
}

/* ---------- INPUT ---------- */
$memberId = $_POST['member_id'];
$groupId = (int) $_POST['chit_group_id'];
$actual = (int) $_POST['actual_amount'];
$discount = (int) ($_POST['discount_amount'] ?? 0);
// $final      = $actual - $discount;
// $actual = (int)$_POST['actual_amount'];
$final = $actual;
$mode = $_POST['payment_mode'];
$payDate = $_POST['payment_date'];

/* ---------- RECEIPT ---------- */
$receiptNo = 'REC-' . date('Y') . '-' . rand(1000, 9999);

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

if ($stmt->execute()) {
    echo "success";
} else {
    echo "db_error";
}
?>