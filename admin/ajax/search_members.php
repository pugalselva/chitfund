<?php
include '../../config/database.php';

$q = trim($_GET['q'] ?? '');

$stmt = $conn->prepare("
    SELECT member_id, full_name
    FROM members
    WHERE is_active=1
    AND (member_id LIKE ? OR full_name LIKE ?)
    LIMIT 10
");

$like = "%$q%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();

$res = $stmt->get_result();
$data = [];

while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
