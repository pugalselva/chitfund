<?php
session_name('chitfund_admin');
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = (int) $_POST['id'];
$value = trim($_POST['setting_value']);
$isActive = isset($_POST['is_active']) ? 1 : 0;

$stmt = $conn->prepare("
    UPDATE settings
    SET setting_value = ?, is_active = ?
    WHERE id = ?
");
$stmt->bind_param("sii", $value, $isActive, $id);
$stmt->execute();

header("Location: index.php?updated=1");
exit;
?>