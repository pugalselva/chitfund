<?php
include '../../config/database.php';

/* Activate auctions */
$conn->query("
    UPDATE auctions
    SET status = 'active'
    WHERE status = 'upcoming'
      AND auction_datetime <= NOW()
      AND auction_end_datetime > NOW()
");

/* Close auctions */
$conn->query("
    UPDATE auctions
    SET status = 'completed'
    WHERE status != 'completed'
      AND auction_end_datetime <= NOW()
");
?>