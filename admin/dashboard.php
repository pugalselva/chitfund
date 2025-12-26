<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
<div class="wrapper">

<?php include 'layout/sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h3>Dashboard</h3>
        <div>
            <b>Admin User</b><br>
            sandy@gmail.com
        </div>
    </div>

    <div class="content">
        <p style="color:#6b7280;">Welcome back! Here's your overview.</p>

        <div class="cards">
            <div class="card">
                <h4>Active Groups</h4>
                <h2>2</h2>
                <small style="color:green;">+2 this month</small>
            </div>

            <div class="card">
                <h4>Total Members</h4>
                <h2>5</h2>
                <small style="color:green;">+8 this month</small>
            </div>

            <div class="card">
                <h4>Live Auctions</h4>
                <h2>1</h2>
                <small>1 upcoming</small>
            </div>

            <div class="card">
                <h4>Total Collections</h4>
                <h2>₹0.2L</h2>
                <small style="color:green;">+12% from last month</small>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div class="box">
                <h4>Recent Auctions</h4><br>
                Elite Savings Group – Month 12<br>
                <small>Winner: Jane Smith ₹2,25,000</small><br><br>
                Business Circle – Month 7<br>
                <small>Winner: Robert Johnson ₹2,70,000</small>
            </div>

            <div class="box">
                <h4>Recent Payments</h4><br>
                John Doe – ₹10,000<br>
                <small>2024-11-01</small><br><br>
                Jane Smith – ₹9,000<br>
                <small>2024-11-02</small>
            </div>
        </div>

        <div class="alert">
            💰 <b>You have 2 pending payment(s)</b><br>
            Please follow up with members
        </div>

    </div>
</div>
</div>
</body>
</html>
