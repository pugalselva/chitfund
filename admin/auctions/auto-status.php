<?php
include '../../config/database.php';

/* AUTO START */
$conn->query("
    UPDATE auctions
    SET status='active'
    WHERE status='upcoming'
    AND auction_datetime <= NOW()
    AND auction_end_datetime > NOW()
");

/* AUTO CLOSE */
$conn->query("
    UPDATE auctions
    SET status='completed'
    WHERE status='active'
    AND auction_end_datetime <= NOW()
");
?>