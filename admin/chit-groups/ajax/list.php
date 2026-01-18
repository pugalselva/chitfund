<?php
include '../../../config/database.php';

/* Inputs */
$page        = max(1, (int)($_GET['page'] ?? 1));
$limit       = (int)($_GET['limit'] ?? 5);
$search      = trim($_GET['search'] ?? '');
$groupFilter = trim($_GET['group'] ?? '');

$offset = ($page - 1) * $limit;

/* WHERE conditions */
$where = "WHERE g.is_active = 1";
$params = [];
$types  = "";

/* Search */
if ($search !== '') {
    $where .= " AND (g.group_name LIKE ? OR g.group_code LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

/* Group filter */
if ($groupFilter !== '') {
    $where .= " AND g.group_name = ?";
    $params[] = $groupFilter;
    $types .= "s";
}

/* Total count */
$countSql = "
    SELECT COUNT(*) AS total
    FROM chit_groups g
    $where
";
$stmt = $conn->prepare($countSql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];

/* Data */
$sql = "
    SELECT g.*
    FROM chit_groups g
    $where
    ORDER BY g.created_at DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* Response */
echo json_encode([
    'data'  => $data,
    'total' => $total
]);
?>