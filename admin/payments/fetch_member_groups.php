<?php
include '../../config/database.php';

$memberId = $_GET['member_id'] ?? '';

$stmt = $conn->prepare("
    SELECT g.id, g.group_name
    FROM chit_group_members cgm
    JOIN chit_groups g ON g.id = cgm.group_id
    WHERE cgm.member_id = ?
");
$stmt->bind_param("s", $memberId);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
