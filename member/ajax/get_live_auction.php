<?php
include '../../config/database.php';

$q = $conn->query("
SELECT a.*, cg.group_name
FROM auctions a
JOIN chit_groups cg ON cg.id = a.chit_group_id
WHERE a.status = 'active'
LIMIT 1
");

if ($q->num_rows == 0) {
    echo json_encode(['status' => 'no_auction']);
    exit;
}

$auction = $q->fetch_assoc();
echo json_encode($auction);
