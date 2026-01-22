<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'member') {
    echo json_encode(['status'=>false,'message'=>'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

$current = $_POST['current_password'];
$new     = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

if ($new !== $confirm) {
    echo json_encode(['status'=>false,'message'=>'Passwords do not match']);
    exit;
}

$stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!password_verify($current, $user['password'])) {
    echo json_encode(['status'=>false,'message'=>'Current password incorrect']);
    exit;
}

$newHash = password_hash($new, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt->bind_param("si", $newHash, $userId);
$stmt->execute();

echo json_encode(['status'=>true,'message'=>'Password updated successfully']);
?>