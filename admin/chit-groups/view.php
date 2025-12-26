<!DOCTYPE html>
<html>
<head>
<title>Chit Group Details</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="wrapper">

<?php include '../layout/sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <div>
            <div class="page-title">Elite Savings Group</div>
            <div class="page-subtitle">Chit group details and information</div>
        </div>
    </div>

    <div class="content">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

            <!-- GROUP INFO -->
            <div class="info-box">
                <h4>Group Information</h4><br>

                <div class="info-row">
                    <span>Status</span>
                    <span class="status">active</span>
                </div>

                <div class="info-row">
                    <span>Total Members</span>
                    <span>25</span>
                </div>

                <div class="info-row">
                    <span>Monthly Contribution</span>
                    <span>₹10,000</span>
                </div>

                <div class="info-row">
                    <span>Pool Amount</span>
                    <span>₹2,50,000</span>
                </div>

                <div class="info-row">
                    <span>Duration</span>
                    <span>25 months</span>
                </div>

                <div class="info-row">
                    <span>Start Date</span>
                    <span>01-01-2024</span>
                </div>
            </div>

            <!-- PROGRESS -->
            <div class="info-box">
                <h4>Progress</h4><br>

                <div style="display:flex;justify-content:space-between;">
                    <span>Current Month</span>
                    <span>12 / 25</span>
                </div>

                <div class="progress-bar">
                    <div class="progress"></div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px;">
                    <div class="stat-box green">
                        <h2>12</h2>
                        <small>Completed</small>
                    </div>

                    <div class="stat-box blue">
                        <h2>13</h2>
                        <small>Remaining</small>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
</div>

</body>
</html>
