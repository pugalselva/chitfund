<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../index.php');
    exit();
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="wrapper">

        <?php include 'layout/sidebar.php'; ?>

        <div class="main">

            <div class="topbar">
                <div>
                    <div class="page-title">Member Dashboard</div>
                    <div class="page-subtitle">Welcome back! Here's your overview.</div>
                </div>
                <div class="topbar">
                    <div>
                        <b>Member User</b><br>
                        sandy@gmail.com
                        <a href="../logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>

                </div>
            </div>

            <div class="content">

                <!-- TOP CARDS -->
                <div class="cards">
                    <div class="card">
                        <h4>Active Chits</h4>
                        <h2>2</h2>
                    </div>

                    <div class="card">
                        <h4>Total Contributions</h4>
                        <h2>₹2.4L</h2>
                    </div>

                    <div class="card">
                        <h4>Upcoming Auction</h4>
                        <h2>3 days</h2>
                    </div>

                    <div class="card">
                        <h4>Savings Progress</h4>
                        <h2>48%</h2>
                    </div>
                </div>

                <!-- CONTENT GRID -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

                    <!-- MY ACTIVE GROUPS -->
                    <div class="box">
                        <h4>My Active Groups</h4><br>

                        <b>Elite Savings Group</b><br>
                        <small>Month 12 of 25 · ₹10,000/month</small>

                        <hr style="margin:15px 0;">

                        <b>Business Circle</b><br>
                        <small>Month 7 of 20 · ₹15,000/month</small>
                    </div>

                    <!-- RECENT PAYMENTS -->
                    <div class="box">
                        <h4>Recent Payments</h4><br>

                        <div style="display:flex;justify-content:space-between;">
                            <div>
                                <b>Elite Savings Group</b><br>
                                <small>Month 12</small>
                            </div>
                            <div style="text-align:right;">
                                ₹10,000<br>
                                <small style="color:green;">paid</small>
                            </div>
                        </div>

                        <hr style="margin:15px 0;">

                        <div style="display:flex;justify-content:space-between;">
                            <div>
                                <b>Elite Savings Group</b><br>
                                <small>Month 12</small>
                            </div>
                            <div style="text-align:right;">
                                ₹9,000<br>
                                <small style="color:green;">paid</small>
                            </div>
                        </div>

                        <hr style="margin:15px 0;">

                        <div style="display:flex;justify-content:space-between;">
                            <div>
                                <b>Business Circle</b><br>
                                <small>Month 7</small>
                            </div>
                            <div style="text-align:right;">
                                ₹15,000<br>
                                <small style="color:#ea580c;">pending</small>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

</body>

</html>