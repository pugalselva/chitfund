<?php
session_start();
include '../config/database.php';

$loginId = $_POST['login_id'] ?? '';
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

$stmt = $conn->prepare("
    SELECT id, role, password 
    FROM users 
    WHERE user_id=? AND role=? AND is_active=1
");
$stmt->bind_param("ss", $loginId, $role);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
    $u = $res->fetch_assoc();
    if (password_verify($password, $u['password'])) {
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['role'] = $u['role'];
        echo "success";
    } else echo "wrong_password";
} else echo "not_found";
