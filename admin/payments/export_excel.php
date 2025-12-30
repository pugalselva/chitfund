<?php
include '../../config/database.php';

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=payments.csv");

$output = fopen("php://output", "w");

/* Header */
fputcsv($output, [
    'Receipt No',
    'Member',
    'Group',
    'Month',
    'Actual',
    'Discount',
    'Final',
    'Mode',
    'Status',
    'Payment Date'
]);

$q = $conn->query("
    SELECT p.*, m.full_name, g.group_name
    FROM payments p
    JOIN members m ON m.member_id = p.member_id
    JOIN chit_groups g ON g.id = p.chit_group_id
    ORDER BY p.created_at DESC
");

while ($row = $q->fetch_assoc()) {
    fputcsv($output, [
        $row['receipt_no'],
        $row['full_name'],
        $row['group_name'],
        'Month '.$row['month_no'],
        $row['actual_amount'],
        $row['discount_amount'],
        $row['final_amount'],
        $row['payment_mode'],
        ucfirst($row['status']),
        $row['payment_date']
    ]);
}

fclose($output);
exit;
?>