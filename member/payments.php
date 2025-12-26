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
                    sandy@gmail.com
                </div>
            </div>

            <div class="content">

                <!-- SUMMARY -->
                <div class="summary-cards">
                    <div class="summary-card">
                        <div class="summary-icon icon-paid">✔</div>
                        <div>
                            <div>Paid</div>
                            <b>2</b>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="summary-icon icon-pending">⏳</div>
                        <div>
                            <div>Pending</div>
                            <b>1</b>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="summary-icon icon-overdue">⚠</div>
                        <div>
                            <div>Overdue</div>
                            <b>1</b>
                        </div>
                    </div>
                </div>

                <!-- TABLE -->
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
                            <tr>
                                <td>REC-2024-001</td>
                                <td>Elite Savings Group<br><small>CG001</small></td>
                                <td>Month 12</td>
                                <td>₹10,000</td>
                                <td>₹0</td>
                                <td>₹10,000</td>
                                <td><span class="mode">UPI</span></td>
                                <td>5/11/2024</td>
                                <td>1/11/2024</td>
                                <td><span class="badge paid">paid</span></td>
                            </tr>

                            <tr>
                                <td>REC-2024-002</td>
                                <td>Elite Savings Group<br><small>CG001</small></td>
                                <td>Month 12</td>
                                <td>₹10,000</td>
                                <td style="color:#16a34a;">-₹1,000</td>
                                <td>₹9,000</td>
                                <td><span class="mode">CASH</span></td>
                                <td>5/11/2024</td>
                                <td>2/11/2024</td>
                                <td><span class="badge paid">paid</span></td>
                            </tr>

                            <tr>
                                <td>REC-2024-003</td>
                                <td>Business Circle<br><small>CG002</small></td>
                                <td>Month 7</td>
                                <td>₹15,000</td>
                                <td>₹0</td>
                                <td>₹15,000</td>
                                <td><span class="mode">UPI</span></td>
                                <td>5/12/2024</td>
                                <td>-</td>
                                <td><span class="badge pending">pending</span></td>
                            </tr>

                            <tr>
                                <td>REC-2024-004</td>
                                <td>Elite Savings Group<br><small>CG001</small></td>
                                <td>Month 12</td>
                                <td>₹10,000</td>
                                <td>₹0</td>
                                <td>₹10,000</td>
                                <td><span class="mode">CASH</span></td>
                                <td>5/11/2024</td>
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
