<?php
include '../config/database.php';

$conn->query("
UPDATE auctions
SET status = CASE
    WHEN NOW() < auction_datetime THEN 'upcoming'
    WHEN NOW() BETWEEN auction_datetime AND auction_end_datetime THEN 'active'
    ELSE 'completed'
END
WHERE is_active = 1
");
