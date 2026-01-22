<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'member') {
    echo json_encode(['status'=>false,'message'=>'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

$fullName = trim($_POST['full_name']);
$email    = trim($_POST['email']);
$mobile   = trim($_POST['mobile']);
$address  = trim($_POST['address']);

$stmt = $conn->prepare("
    UPDATE members
    SET full_name=?, email=?, mobile=?, address=?
    WHERE user_id=?
");
$stmt->bind_param(
    "ssssi",
    $fullName,
    $email,
    $mobile,
    $address,
    $userId
);

if ($stmt->execute()) {
    echo json_encode(['status'=>true,'message'=>'Profile updated successfully']);
} else {
    echo json_encode(['status'=>false,'message'=>'Update failed']);
}
?>