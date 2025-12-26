<!DOCTYPE html>
<html>
<head>
<title>Create Chit Group</title>
<link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="wrapper">

<?php include '../layout/sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <div>
            <div class="page-title">Create Chit Group</div>
            <div class="page-subtitle">Set up a new chit fund group</div>
        </div>
    </div>

    <div class="content">

        <div class="form-box">
            <h4>Group Details</h4><br>

            <div class="form-group">
                <label>Chit Group Name *</label>
                <input type="text" class="form-control" placeholder="e.g. Elite Savings Group">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Total Members *</label>
                    <input type="number" class="form-control" value="25">
                </div>

                <div class="form-group">
                    <label>Duration (Months) *</label>
                    <input type="number" class="form-control" value="25">
                    <small>Should match total members</small>
                </div>
            </div>

            <div class="form-group">
                <label>Monthly Contribution (₹) *</label>
                <input type="number" class="form-control" value="10000">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Auction Type *</label>
                    <select class="form-control">
                        <option>Reverse (Lowest Bid Wins)</option>
                        <option>Open</option>
                    </select>
                    <small>Members bid lower amounts for higher discounts</small>
                </div>

                <div class="form-group">
                    <label>Foreman Commission (%) *</label>
                    <input type="number" class="form-control" value="5">
                </div>
            </div>

            <div class="form-group">
                <label>Start Date *</label>
                <input type="date" class="form-control">
            </div>

            <div class="form-group toggle">
                <input type="checkbox" checked>
                <label>Active Status</label>
            </div>

            <br>
            <button class="btn-primary">Create Group</button>
            <a href="index.php">
                <button class="btn-secondary" type="button">Cancel</button>
            </a>

        </div>

    </div>
</div>
</div>

</body>
</html>
