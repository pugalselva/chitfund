<?php
include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access");
}

/* ---------- BASIC ---------- */
$memberId = $_POST['member_id'];
$status   = isset($_POST['status']) ? 1 : 0;

/* ---------- FETCH EXISTING FILES ---------- */
$old = $conn->prepare("
    SELECT m.photo, b.cheque_photo
    FROM members m
    LEFT JOIN member_bank_details b ON b.member_id = m.member_id
    WHERE m.member_id = ?
");
$old->bind_param("s", $memberId);
$old->execute();
$oldData = $old->get_result()->fetch_assoc();

$photo   = $oldData['photo'];
$bankDoc = $oldData['cheque_photo'];

/* ---------- FILE UPLOADS (OPTIONAL) ---------- */
if (!empty($_FILES['photo']['name'])) {
    $photo = time().'_photo_'.$_FILES['photo']['name'];
    move_uploaded_file(
        $_FILES['photo']['tmp_name'],
        '../../uploads/members/'.$photo
    );
}

if (!empty($_FILES['bank_doc']['name'])) {
    $bankDoc = time().'_bank_'.$_FILES['bank_doc']['name'];
    move_uploaded_file(
        $_FILES['bank_doc']['tmp_name'],
        '../../uploads/bank/'.$bankDoc
    );
}

/* ---------- 1. UPDATE MEMBER DETAILS ---------- */
$stmt = $conn->prepare("
    UPDATE members SET
        full_name     = ?,
        gender        = ?,
        dob           = ?,
        address       = ?,
        aadhar        = ?,
        mobile        = ?,
        email         = ?,
        joining_date  = ?,
        photo         = ?,
        is_active     = ?
    WHERE member_id = ?
");
if (!$stmt) die($conn->error);

$stmt->bind_param(
    "sssssssssis",
    $_POST['full_name'],
    $_POST['gender'],
    $_POST['dob'],
    $_POST['address'],
    $_POST['aadhaar'],
    $_POST['mobile'],
    $_POST['email'],
    $_POST['joining_date'],
    $photo,
    $status,
    $memberId
);
$stmt->execute();

/* ---------- 2. UPDATE / INSERT BANK DETAILS ---------- */
$check = $conn->prepare("
    SELECT id FROM member_bank_details WHERE member_id = ?
");
$check->bind_param("s", $memberId);
$check->execute();
$exists = $check->get_result()->num_rows > 0;

if ($exists) {

    // UPDATE
    $stmt = $conn->prepare("
        UPDATE member_bank_details SET
            account_no    = ?,
            account_name  = ?,
            bank_name     = ?,
            ifsc          = ?,
            cheque_photo  = ?,
            upi_id        = ?
        WHERE member_id = ?
    ");
    if (!$stmt) die($conn->error);

    $stmt->bind_param(
        "sssssss",
        $_POST['acc_no'],
        $_POST['acc_name'],
        $_POST['bank_name'],
        $_POST['ifsc'],
        $bankDoc,
        $_POST['upi'],
        $memberId
    );
    $stmt->execute();

} else {

    // INSERT (first time)
    $stmt = $conn->prepare("
        INSERT INTO member_bank_details
        (member_id, account_no, account_name, bank_name, ifsc, cheque_photo, upi_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) die($conn->error);

    $stmt->bind_param(
        "sssssss",
        $memberId,
        $_POST['acc_no'],
        $_POST['acc_name'],
        $_POST['bank_name'],
        $_POST['ifsc'],
        $bankDoc,
        $_POST['upi']
    );
    $stmt->execute();
}

/* ---------- DONE ---------- */
header("Location: view.php?id=".$memberId."&updated=1");
exit;
