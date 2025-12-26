<?php
session_start();
include '../config/database.php';

$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];

$q = $conn->query("
    SELECT * FROM users 
    WHERE email='$email' 
    AND password=MD5('$password')
    AND role='$role'
");

if($q->num_rows){
    $u = $q->fetch_assoc();
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['role'] = $u['role'];

    if($role === 'admin'){
        header("Location: ../admin/dashboard.php");
    }else{
        header("Location: ../member/dashboard.php");
    }
}else{
    echo "Invalid login";
}
?>