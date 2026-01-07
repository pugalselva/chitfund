<?php
include '../../config/database.php';

$payments = $conn->query("
    SELECT p.*, 
           m.full_name, 
           g.group_name
    FROM payments p
    JOIN members m ON m.member_id = p.member_id
    JOIN chit_groups g ON g.id = p.chit_group_id
    ORDER BY p.created_at DESC
");
// summary data can be calculated here or in the HTML section
$summary = $conn
    ->query(
        "
    SELECT 
        SUM(CASE WHEN status='paid' THEN final_amount ELSE 0 END) AS total_collected,
        COUNT(CASE WHEN status='paid' THEN 1 END) AS paid_count,

        SUM(CASE WHEN status='pending' THEN final_amount ELSE 0 END) AS pending_amount,
        COUNT(CASE WHEN status='pending' THEN 1 END) AS pending_count,

        SUM(CASE WHEN status='overdue' THEN final_amount ELSE 0 END) AS overdue_amount,
        COUNT(CASE WHEN status='overdue' THEN 1 END) AS overdue_count
    FROM payments
",
    )
    ->fetch_assoc();

?>


<!DOCTYPE html>
<html>

<head>
    <title>Payment Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <div class="wrapper">
        <?php include '../layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Payment Reports</div>
                    <div class="page-subtitle">Track all payment transactions</div>
                </div>
                <div class="topbar">
                    <div>
                        <b>Admin User</b><br>
                        sandy@gmail.com
                        <a href="../../logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>

                </div>
            </div>

            <div class="content">
                <!-- SUMMARY -->
                <div class="summary-cards">

                    <div class="summary-card">
                        <small>Total Collected</small>
                        <h2>₹<?= number_format($summary['total_collected'] ?? 0) ?></h2>
                        <small><?= $summary['paid_count'] ?> payments</small>
                    </div>

                    <div class="summary-card">
                        <small>Pending</small>
                        <h2>₹<?= number_format($summary['pending_amount'] ?? 0) ?></h2>
                        <small><?= $summary['pending_count'] ?> payment</small>
                    </div>

                    <div class="summary-card red">
                        <small>Overdue</small>
                        <h2>₹<?= number_format($summary['overdue_amount'] ?? 0) ?></h2>
                        <small><?= $summary['overdue_count'] ?> payment</small>
                    </div>
                </div>

                <a href="create.php">
                    <button class="btn-primary">＋ Record Payment</button>
                </a>
                <div class="table-box">

                    <div class="tabs">
                        <span class="active" onclick="filterPayments('all', this)">
                            All (<?= $summary['paid_count'] + $summary['pending_count'] + $summary['overdue_count'] ?>)
                        </span>
                        <span onclick="filterPayments('paid', this)">Paid (<?= $summary['paid_count'] ?>)</span>
                        <span onclick="filterPayments('pending', this)">Pending
                            (<?= $summary['pending_count'] ?>)</span>
                        <span onclick="filterPayments('overdue', this)">Overdue
                            (<?= $summary['overdue_count'] ?>)</span>

                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Receipt</th>
                                <th>Member</th>
                                <th>Group</th>
                                <th>Month</th>
                                <th>Actual</th>
                                <th>Discount</th>
                                <th>Final</th>
                                <th>Mode</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while($p = $payments->fetch_assoc()): ?>
                            <tr data-status="<?= $p['status'] ?>">
                                <td><?= $p['receipt_no'] ?></td>
                                <td><?= $p['full_name'] ?></td>
                                <td><?= $p['group_name'] ?></td>
                                <td>Month <?= $p['month_no'] ?></td>
                                <td>₹<?= $p['actual_amount'] ?></td>
                                <td>-₹<?= $p['discount_amount'] ?></td>
                                <td>₹<?= $p['final_amount'] ?></td>
                                <td><?= $p['payment_mode'] ?></td>
                                <td>
                                    <span class="badge <?= $p['status'] ?>">
                                        <?= ucfirst($p['status']) ?>
                                    </span>
                                </td>
                                <!-- <td><a href="export_excel.php" class="btn-secondary">
                                    Export Excel
                                </a> -->
                                <td>
    <button class="btn-secondary"
        onclick="openInvoice('<?= $p['receipt_no'] ?>')">
        <i class="fa fa-file-invoice"></i> Invoice
    </button>
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
<script>
    function filterPayments(status, el) {
        // remove active class from all tabs
        document.querySelectorAll('.tabs span').forEach(tab => {
            tab.classList.remove('active');
        });

        // add active to clicked tab
        el.classList.add('active');

        // filter rows
        document.querySelectorAll('tbody tr').forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
<script>
    function filterPayments(status, el) {

        // highlight active tab
        document.querySelectorAll('.tabs span').forEach(tab => {
            tab.classList.remove('active');
        });
        el.classList.add('active');

        // filter table rows
        document.querySelectorAll('tbody tr').forEach(row => {
            const rowStatus = row.getAttribute('data-status');

            if (status === 'all' || rowStatus === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
<script>
function openInvoice(receipt){
    window.open('invoice.php?receipt='+receipt,'_blank');
}
</script>

