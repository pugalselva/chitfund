<?php
include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid access');
}

/* ---------- REQUIRED FIELDS ---------- */
if (empty($_POST['utr_id'])) {
    die('UTR ID is required');
}

/* ---------- AUTO PASSWORD FROM UTR ---------- */
$utrId = trim($_POST['utr_id']);
$passwordHash = password_hash($utrId, PASSWORD_DEFAULT);

/* ---------- STATUS ---------- */
$status = isset($_POST['status']) ? 1 : 0;

/* ---------- IDS ---------- */
$memberId = 'M' . time();

/* ---------- FILE UPLOADS ---------- */
$photo = '';
$bankDoc = '';

if (!empty($_FILES['photo']['name'])) {
    $photo = time() . '_photo_' . $_FILES['photo']['name'];
    move_uploaded_file($_FILES['photo']['tmp_name'], '../../uploads/members/' . $photo);
}

if (!empty($_FILES['bank_doc']['name'])) {
    $bankDoc = time() . '_bank_' . $_FILES['bank_doc']['name'];
    move_uploaded_file($_FILES['bank_doc']['tmp_name'], '../../uploads/bank/' . $bankDoc);
}

/* ---------- IFSC VALIDATION ---------- */
if (empty($_POST['ifsc']) || strlen($_POST['ifsc']) !== 11) {
    die('Invalid IFSC code');
}

/* ---------- 1. CREATE LOGIN ---------- */
$stmt = $conn->prepare("
    INSERT INTO users (role, user_id, password, is_active)
    VALUES ('member', ?, ?, ?)
");
if (!$stmt) {
    die($conn->error);
}

$stmt->bind_param('ssi', $memberId, $passwordHash, $status);
$stmt->execute();
$userId = $stmt->insert_id;

/* ---------- 2. MEMBER DETAILS ---------- */
$stmt = $conn->prepare("
    INSERT INTO members
    (member_id, user_id, utr_id, full_name, gender, dob, address, aadhar, mobile, email, joining_date, photo, is_active)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
if (!$stmt) {
    die($conn->error);
}

$stmt->bind_param('sissssssssssi', $memberId, $userId, $utrId, $_POST['full_name'], $_POST['gender'], $_POST['dob'], $_POST['address'], $_POST['aadhaar'], $_POST['mobile'], $_POST['email'], $_POST['joining_date'], $photo, $status);
$stmt->execute();

/* ---------- 3. BANK DETAILS ---------- */
$stmt = $conn->prepare("
    INSERT INTO member_bank_details
    (member_id, account_no, account_name, bank_name, ifsc, cheque_photo, upi_id)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
if (!$stmt) {
    die($conn->error);
}

$stmt->bind_param('sssssss', $memberId, $_POST['acc_no'], $_POST['acc_name'], $_POST['bank_name'], $_POST['ifsc'], $bankDoc, $_POST['upi']);

$stmt->execute();

/* ---------- DONE ---------- */
header('Location: index.php?success=1');
exit();
?>
