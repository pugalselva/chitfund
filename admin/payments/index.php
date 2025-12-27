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
        <h2>₹19,000</h2>
        <small>2 payments</small>
    </div>

    <div class="summary-card">
        <small>Pending</small>
        <h2>₹15,000</h2>
        <small>1 payment</small>
    </div>

    <div class="summary-card red">
        <small>Overdue</small>
        <h2>₹10,000</h2>
        <small>1 payment</small>
    </div>
</div>
 <a href="create.php">
        <button class="btn-primary">＋ Record Payment</button>
    </a>
<div class="table-box">

<div class="tabs">
    <span class="active" onclick="filterPayments('all', this)">All (4)</span>
    <span onclick="filterPayments('paid', this)">Paid (2)</span>
    <span onclick="filterPayments('pending', this)">Pending (1)</span>
    <span onclick="filterPayments('overdue', this)">Overdue (1)</span>
</div>


<table>
<thead>
<tr>
<th>Receipt No</th>
<th>Member</th>
<th>Chit Group</th>
<th>Month</th>
<th>Actual</th>
<th>Discount</th>
<th>Final</th>
<th>Mode</th>
<th>Due Date</th>
<th>Payment Date</th>
<th>Status</th>
</tr>
</thead>

<tbody>
<tr data-status="paid">
<td>REC-2024-001</td>
<td>John Doe<br><small>M001</small></td>
<td>Elite Savings Group</td>
<td>Month 12</td>
<td>₹10,000</td>
<td>-</td>
<td>₹10,000</td>
<td><span class="tag upi">UPI</span></td>
<td>2024-11-05</td>
<td>2024-11-01</td>
<td><span class="badge paid">paid</span></td>
</tr>

<tr>
<td>REC-2024-002</td>
<td>Jane Smith<br><small>M002</small></td>
<td>Elite Savings Group</td>
<td>Month 12</td>
<td>₹10,000</td>
<td style="color:green;">-₹1,000</td>
<td>₹9,000</td>
<td><span class="tag cash">CASH</span></td>
<td>2024-11-05</td>
<td>2024-11-02</td>
<td><span class="badge paid">paid</span></td>
</tr>

<tr  data-status="pending">
<td>REC-2024-003</td>
<td>Robert Johnson<br><small>M003</small></td>
<td>Business Circle</td>
<td>Month 7</td>
<td>₹15,000</td>
<td>-</td>
<td>₹15,000</td>
<td><span class="tag upi">UPI</span></td>
<td>2024-12-05</td>
<td>-</td>
<td><span class="badge pending">pending</span></td>
</tr>

<tr data-status="overdue">
<td>REC-2024-004</td>
<td>Emily Davis<br><small>M004</small></td>
<td>Elite Savings Group</td>
<td>Month 12</td>
<td>₹10,000</td>
<td>-</td>
<td>₹10,000</td>
<td><span class="tag cash">CASH</span></td>
<td>2024-11-05</td>
<td>-</td>
<td><span class="badge overdue">overdue</span></td>
</tr>
</tbody>
</table>

</div>
</div>
</div>
</div>

</body>
</html>
<script>
function filterPayments(status, el){
    // remove active class from all tabs
    document.querySelectorAll('.tabs span').forEach(tab => {
        tab.classList.remove('active');
    });

    // add active to clicked tab
    el.classList.add('active');

    // filter rows
    document.querySelectorAll('tbody tr').forEach(row => {
        if(status === 'all' || row.dataset.status === status){
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
