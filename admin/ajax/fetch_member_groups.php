<?php
include '../../config/database.php';

$memberId = $_GET['member_id'];

$stmt = $conn->prepare("
    SELECT g.id, g.group_name
    FROM chit_group_members cgm
    JOIN chit_groups g ON g.id = cgm.group_id
    WHERE cgm.member_id=?
");
$stmt->bind_param("s", $memberId);
$stmt->execute();

$res = $stmt->get_result();
$data = [];

while ($r = $res->fetch_assoc()) {
    $data[] = $r;
}

echo json_encode($data);
