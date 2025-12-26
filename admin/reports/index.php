<!DOCTYPE html>
<html>
<head>
<title>Reports</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="wrapper">
<?php include '../layout/sidebar.php'; ?>

<div class="main">

<div class="topbar">
    <div>
        <div class="page-title">Reports</div>
        <div class="page-subtitle">Generate and download comprehensive reports</div>
    </div>
</div>

<div class="content">

<!-- SUMMARY CARDS -->
<div class="report-cards">
    <div class="report-card">
        <small>Total Collections</small>
        <h2>₹0.19L</h2>
        <small>This month</small>
    </div>

    <div class="report-card">
        <small>Pending Amount</small>
        <h2 style="color:#ea580c;">₹0.25L</h2>
        <small>To be collected</small>
    </div>

    <div class="report-card">
        <small>Total Discounts</small>
        <h2 style="color:#16a34a;">₹0.55L</h2>
        <small>From auctions</small>
    </div>

    <div class="report-card">
        <small>Active Members</small>
        <h2>5</h2>
        <small>Currently enrolled</small>
    </div>
</div>

<!-- REPORT TYPES -->
<div class="report-grid">

    <div class="report-box">
        <div class="report-left">
            <div class="report-icon icon-blue">👥</div>
            <div>
                <b>Member Report</b><br>
                <small>Complete list of all members with their details</small><br><br>
                <small>5 members</small>
            </div>
        </div>
        <button class="export-btn">⬇ Export</button>
    </div>

    <div class="report-box">
        <div class="report-left">
            <div class="report-icon icon-purple">📈</div>
            <div>
                <b>Chit Group Report</b><br>
                <small>All chit groups with status and progress</small><br><br>
                <small>4 groups</small>
            </div>
        </div>
        <button class="export-btn">⬇ Export</button>
    </div>

    <div class="report-box">
        <div class="report-left">
            <div class="report-icon icon-green">💲</div>
            <div>
                <b>Payment Collection Report</b><br>
                <small>Detailed payment transactions and collections</small><br><br>
                <small>₹19,000 collected</small>
            </div>
        </div>
        <button class="export-btn">⬇ Export</button>
    </div>

    <div class="report-box">
        <div class="report-left">
            <div class="report-icon icon-orange">🔨</div>
            <div>
                <b>Auction Report</b><br>
                <small>Auction history with winners and bid amounts</small><br><br>
                <small>2 auctions completed</small>
            </div>
        </div>
        <button class="export-btn">⬇ Export</button>
    </div>

</div>

<!-- CUSTOM REPORT -->
<div class="custom-box">
    <b>Custom Report Generation</b><br>
    <small>Generate custom reports based on date range, chit groups, or specific members.</small>

    <div class="custom-actions">
        <button>Monthly Summary</button>
        <button>Defaulters List</button>
        <button>Performance Analytics</button>
    </div>
</div>

</div>
</div>
</div>

</body>
</html>
