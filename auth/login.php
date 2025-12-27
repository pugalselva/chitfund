<?php
session_start();
include '../config/database.php';

$loginId = $_POST['login_id'];
$password = $_POST['password'];
$role = $_POST['role'];

$stmt = $conn->prepare("
    SELECT * FROM users
    WHERE user_id=? AND role=? AND is_active=1
");
$stmt->bind_param("ss", $loginId, $role);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        echo "success";
    } else {
        echo "wrong_password";
    }
} else {
    echo "not_found";
}
?>