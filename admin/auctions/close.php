<?php
session_start();
include '../../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    die('Unauthorized');
}

$auctionId = (int)$_POST['auction_id'];

/* Mark auction completed */
$stmt = $conn->prepare("
    UPDATE auctions
    SET status='completed'
    WHERE id=?
");
$stmt->bind_param("i", $auctionId);
$stmt->execute();

echo 'success';
?>