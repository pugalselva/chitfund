<?php
// session_start();
// include '../config/database.php';

// $loginId = $_POST['login_id'] ?? '';
// $password = $_POST['password'] ?? '';
// $role     = $_POST['role'] ?? '';

// $stmt = $conn->prepare("
//     SELECT id, role, password 
//     FROM users 
//     WHERE user_id=? AND role=? AND is_active=1
// ");
// $stmt->bind_param("ss", $loginId, $role);
// $stmt->execute();
// $res = $stmt->get_result();

// if ($res->num_rows === 1) {
//     $u = $res->fetch_assoc();
//     if (password_verify($password, $u['password'])) {
//         $_SESSION['user_id'] = $u['id'];
//         $_SESSION['role'] = $u['role'];
//         echo "success";
//     } else echo "wrong_password";
// } else echo "not_found";
?>
<?php
session_start();
include '../config/database.php';

$loginId = $_POST['login_id'] ?? '';
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

$stmt = $conn->prepare("
    SELECT id, role, password, user_id
    FROM users
    WHERE user_id=? AND role=? AND is_active=1
");
$stmt->bind_param("ss", $loginId, $role);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    echo "not_found";
    exit;
}

$u = $res->fetch_assoc();

if (!password_verify($password, $u['password'])) {
    echo "wrong_password";
    exit;
}

/* ---------------- SESSION SET ---------------- */
$_SESSION['user_id'] = $u['id'];
$_SESSION['role']    = $u['role'];

/* ✅ IMPORTANT: MEMBER SESSION */
if ($u['role'] === 'member') {

    $stmt = $conn->prepare("
        SELECT member_id, full_name, email
        FROM members
        WHERE user_id=? AND is_active=1
    ");
    $stmt->bind_param("i", $u['id']);
    $stmt->execute();
    $m = $stmt->get_result()->fetch_assoc();

    if (!$m) {
        session_destroy();
        echo "member_not_found";
        exit;
    }

    $_SESSION['member_id'] = $m['member_id'];
    $_SESSION['name']      = $m['full_name'];
    $_SESSION['email']     = $m['email'];
}

/* ---------------- DONE ---------------- */
echo "success";
?>