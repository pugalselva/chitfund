<?php
session_start();
include '../config/database.php';

if ($_SESSION['role'] !== 'member') {
    header("Location: ../index.php");
    exit;
}

$memberId = $_SESSION['member_id'];

/* ===============================
   PAYMENT SUMMARY
================================ */
$summary = $conn->prepare("
SELECT
    COUNT(CASE WHEN status='paid' THEN 1 END)    AS paid_count,
    COUNT(CASE WHEN status='pending' THEN 1 END) AS pending_count,
    COUNT(CASE WHEN status='overdue' THEN 1 END) AS overdue_count
FROM payments
WHERE member_id = ?
");
$summary->bind_param("s", $memberId);
$summary->execute();
$summaryData = $summary->get_result()->fetch_assoc();

/* ===============================
   PAYMENT HISTORY
================================ */
$stmt = $conn->prepare("
SELECT 
    p.*,
    g.group_name,
    g.group_code
FROM payments p
JOIN chit_groups g ON g.id = p.chit_group_id
WHERE p.member_id = ?
ORDER BY p.created_at DESC
");
$stmt->bind_param("s", $memberId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment History</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="wrapper">
<?php include 'layout/sidebar.php'; ?>

<div class="main">

<div class="topbar">
    <div>
        <div class="page-title">Payment History</div>
        <div class="page-subtitle">
            Track your monthly contributions and payments
        </div>
    </div>
    <div style="text-align:right;">
        <b>Member User</b><br>
        <?= htmlspecialchars($_SESSION['email'] ?? '') ?>
    </div>
</div>

<div class="content">

<!-- ================= SUMMARY ================= -->
<div class="summary-cards">
    <div class="summary-card">
        <div class="summary-icon icon-paid">✔</div>
        <div>
            <div>Paid</div>
            <b><?= $summaryData['paid_count'] ?></b>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon icon-pending">⏳</div>
        <div>
            <div>Pending</div>
            <b><?= $summaryData['pending_count'] ?></b>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon icon-overdue">⚠</div>
        <div>
            <div>Overdue</div>
            <b><?= $summaryData['overdue_count'] ?></b>
        </div>
    </div>
</div>

<!-- ================= TABLE ================= -->
<div class="table-card">
    <h4>All Payments</h4>

    <table class="table">
        <thead>
            <tr>
                <th>Receipt No</th>
                <th>Chit Group</th>
                <th>Month</th>
                <th>Actual Amount</th>
                <th>Discount</th>
                <th>Amount Paid</th>
                <th>Mode</th>
                <th>Due Date</th>
                <th>Paid Date</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
        <?php if ($result->num_rows === 0): ?>
            <tr>
                <td colspan="10">No payment records found.</td>
            </tr>
        <?php endif; ?>

        <?php while ($p = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($p['receipt_no']) ?></td>

                <td>
                    <?= htmlspecialchars($p['group_name']) ?><br>
                    <small><?= htmlspecialchars($p['group_code']) ?></small>
                </td>

                <td>Month <?= $p['month_no'] ?></td>

                <td>₹<?= number_format($p['actual_amount']) ?></td>

                <td style="color:#16a34a;">
                    <?= $p['discount_amount'] > 0 ? '-₹' . number_format($p['discount_amount']) : '₹0' ?>
                </td>

                <td>₹<?= number_format($p['final_amount']) ?></td>

                <td>
                    <span class="mode <?= strtolower($p['payment_mode']) ?>">
                        <?= strtoupper($p['payment_mode']) ?>
                    </span>
                </td>

                <td><?= date('d/m/Y', strtotime($p['due_date'])) ?></td>

                <td>
                    <?= $p['payment_date'] 
                        ? date('d/m/Y', strtotime($p['payment_date'])) 
                        : '-' ?>
                </td>

                <td>
                    <span class="badge <?= $p['status'] ?>">
                        <?= ucfirst($p['status']) ?>
                    </span>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>
</div>
</div>

</body>
</html>
